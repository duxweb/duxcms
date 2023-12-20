<?php
declare(strict_types=1);

namespace App\Member\Service;

use App\Member\Event\UnionEvent;
use App\Member\Models\MemberUnion;
use App\Member\Models\MemberUser;
use Dux\App;
use Dux\Handlers\ExceptionBusiness;

class Union
{

    /**
     * @param string $data 三方加密数据
     * @return array
     */
    static function login(string $data): array
    {
        $str = decryption($data);
        $unionData = json_decode($str, true);
        if (!$unionData) {
            throw new ExceptionBusiness('非法用户数据');
        }
        if (!$unionData['expire']) {
            throw new ExceptionBusiness('数据获取异常');
        }
        if ($unionData['expire'] < time()) {
            throw new ExceptionBusiness('登录已过期，请重新登录');
        }

        if ($unionData['extend'] != 'tel' && !$unionData['open_id'] && $unionData['union_id']) {
            throw new ExceptionBusiness('登录参数缺失');
        }

        if ($unionData['extend'] != 'tel') {
            // 查找关联
            $unionInfo = MemberUnion::query()->where('type', $unionData['type'])->where('open_id', $unionData['open_id'])->first();
            if ($unionInfo) {
                // 更新union信息
                if ($unionData['union_id']) {
                    $unionInfo->union_id = $unionData['union_id'];
                    $unionInfo->save();
                }

                // 更新用户信息
                $userInfo = MemberUser::query()->find($unionInfo->user_id);
                if ($unionData['avatar']) {
                    $userInfo->avatar = $unionData['avatar'];
                }
                if ($unionData['nickname']) {
                    $userInfo->nickname = $unionData['nickname'];
                }
                $userInfo->save();
                return Member::Login($unionInfo->user_id);
            }
            if (!$unionData['union_id']) {
                throw new ExceptionBusiness('该用户未关联', 511);
            }

            // 查找联合关联
            $unionInfo = MemberUnion::query()->where('type', $unionData['type'])->where('union_id', $unionData['union_id'])->first();
            if (!$unionInfo) {
                throw new ExceptionBusiness('该用户未关联', 511);
            }

            // 注册该关联到用户
            $data = new MemberUnion();
            $data->user_id = $unionInfo->user_id;
            $data->type = $unionData['type'];
            $data->open_id = $unionData['open_id'];
            $data->union_id = $unionData['union_id'];
            $data->data = $unionData;
            $data->save();

            // 更新数据
            $userInfo = MemberUser::query()->find($unionInfo->user_id);
            if ($unionData['avatar']) {
                $userInfo->avatar = $unionData['avatar'];
            }
            if ($unionData['nickname']) {
                $userInfo->nickname = $unionData['nickname'];
            }
            $userInfo->save();
            return Member::Login($unionInfo->user_id);
        } else {
            // 查找用户
            $tel = $unionData['open_id'];
            $userInfo = MemberUser::query()->where('tel', $tel)->first();
            $userId = $userInfo->id;
            if (!$userInfo) {
                $userId = Member::Register(tel: $tel);
            }
            return Member::Login($userId);
        }
    }

    /**
     * 获取三方数据
     * @param string $type
     * @param string $code
     * @param array $params
     * @return string
     */
    static function get(string $type, string $code, array $params = []): string
    {
        $event = new UnionEvent($type, $code, $params);
        // NOTE login.$type
        App::event()->dispatch($event, "login.$type");
        $data = $event->getLoginData();

        if (!$data) {
            throw new ExceptionBusiness('登录类型不存在');
        }
        if (!$data['open_id']) {
            throw new ExceptionBusiness('请设置openid');
        }
        $data['expire'] = time() + 5 * 60;
        return encryption(json_encode($data));
    }

    static function bind(string $data, int $userId = 0): void
    {
        $str = decryption($data);
        $unionData = json_decode($str, true);
        if (!$unionData) {
            throw new ExceptionBusiness('非法用户数据');
        }
        // 查找关联
        $unionInfo = MemberUnion::query()->where('type', $unionData['type']);
        if ($unionData['open_id']) {
            $unionInfo->where('open_id', $unionData['open_id']);
        } elseif ($unionData['union_id']) {
            $unionInfo->where('union_id', $unionData['union_id']);
        }
        $unionInfo = $unionInfo->first();
        if ($unionInfo) {
            throw new ExceptionBusiness('该用户已被绑定');
        }
        MemberUnion::query()->create([
            'user_id' => $userId,
            'type' => $unionData['type'],
            'union_id' => $unionData['union_id'],
            'open_id' => $unionData['open_id'],
            'data' => $unionData
        ]);
    }
}