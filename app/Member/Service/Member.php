<?php

namespace App\Member\Service;

use App\Member\Event\RegisterEvent;
use App\Member\Event\UserEvent;
use App\Member\Models\MemberLevel;
use App\Member\Models\MemberUser;
use App\System\Service\Config;
use Dux\App;
use Dux\Auth\Auth;
use Dux\Handlers\ExceptionBusiness;
use Dux\Validator\Data;
use Exception;
use Phpfastcache\Exceptions\PhpfastcacheSimpleCacheException;
use Psr\Cache\InvalidArgumentException;
use const FILTER_VALIDATE_EMAIL;

class Member
{

    /**
     * 账户注册
     * @param string $nickname
     * @param string $tel
     * @param string $email
     * @param string $password
     * @param Data|null $params
     * @return int
     * @throws InvalidArgumentException
     * @throws PhpfastcacheSimpleCacheException
     */
    public static function Register(string $nickname = '', string $tel = '', string $email = '', string $password = '', ?Data $params = null): int
    {
        if (!$tel && !$email) {
            throw new ExceptionBusiness('请输入账号');
        }
        // 修复手机号前后特殊字符问题
        if ($tel) {
            $tel = trim($tel);
            if (strlen($tel) != 11) {
                throw new ExceptionBusiness('手机号长度错误');
            }
            if (!preg_match('/^1[3456789]\d{9}$/', $tel)) {
                throw new ExceptionBusiness('手机号格式错误');
            }
        }

        $info = MemberUser::query()->where(function ($query) use ($tel, $email) {
            if ($tel) {
                $query->orWhere('tel', $tel);
            }
            if ($email) {
                $query->orWhere('email', $tel);
            }
        })->first();
        if ($info) {
            throw new ExceptionBusiness('该账号已注册');
        }
        $key = 'user.register.' . ($tel ?: $email);
        App::cache()->set($key, true, 60);
        try {
            $info = new MemberUser();
            if ($nickname) {
                $info->nickname = $nickname;
            }
            if ($tel) {
                $info->tel = $tel;
            }
            if ($email) {
                $info->email = $email;
            }
            if ($password) {
                $info->password = password_hash($password, PASSWORD_DEFAULT);
            }
            // 默认等级
            $info->level_id = MemberLevel::query()->where('default', 1)->value('id');
            // 默认头像
            $info->avatar = Config::getValue('user_default_avatar');
            $info->save();
            if (!$info->nickname) {
                $info->nickname = "默认昵称$info->id";
            }
            $info->save();

            // 注册接口
            // NOTE member.register
            App::event()->dispatch(new RegisterEvent($info, $params), 'member.register');

        } catch (Exception $e) {
            App::cache()->delete($key);
            throw $e;
        }
        App::cache()->delete($key);
        return $info->id;
    }

    /**
     * 账户登录
     * @param int $userId
     * @return array
     */
    public static function Login(int $userId): array
    {
        $info = self::getUserInfo($userId);
        if (!$info['status']) {
            throw new ExceptionBusiness("该用户已注销");
        }
        return [
            "userInfo" => $info,
            "token" => "Bearer " . Auth::token("member", [
                    'id' => $userId,
                    'password' => $info['password']
                ], 2592000)
        ];
    }

    /**
     * 获取用户资料
     * @param int $userId
     * @return array
     */
    public static function getUserInfo(int $userId): array
    {
        $info = MemberUser::query()->find($userId);
        if (empty($info)) return [];

        $event = new UserEvent($info);
        $event->setData([
            "id" => $info->id,
            "merchant_id" => $info->merchant_id,
            "nickname" => $info->nickname,
            "avatar" => $info->avatar,
            "tel" => $info->tel,
            "email" => $info->email,
            "birthday" => $info->birthday,
            "sex" => $info->sex,
            'status' => $info->status,
            'password' => $info->password
        ]);

        // NOTE member.user.info (用户资料信息)
        App::event()->dispatch($event, 'member.user.info');
        return $event->getData();
    }

    /**
     * 获取账户类型
     * @param $username
     * @return string
     */
    public static function getUserType($username): string
    {
        if (empty($username)) {
            throw new ExceptionBusiness('请输入账号');
        }

        if (!filter_var($username, FILTER_VALIDATE_EMAIL)) {
            $type = 'tel';
        } else {
            $type = 'email';
        }
        switch ($type) {
            case 'tel':
                if (!preg_match('/(^1[0-9]{10}$)/', $username)) {
                    throw new ExceptionBusiness('手机号码错误!');
                }
                break;
            case 'email' :
                if (!filter_var($username, FILTER_VALIDATE_EMAIL)) {
                    throw new ExceptionBusiness('邮箱账号不正确!');
                }
                break;
        }
        return $type;
    }
}