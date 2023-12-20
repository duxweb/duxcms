<?php

namespace App\Member\Api;

use App\Member\Models\MemberUser;
use App\Sms\Service\Sms;
use App\System\Service\Config;
use Dux\App;
use Dux\Handlers\ExceptionBusiness;
use Dux\Route\Attribute\Route;
use Dux\Route\Attribute\RouteGroup;
use Dux\Validator\Validator;
use Intervention\Image\ImageManager;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

#[RouteGroup(app: 'apiMember', pattern: '/member/setting')]
class Setting
{
    #[Route(methods: 'GET', pattern: '/info')]
    public function info(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $auth = $request->getAttribute('auth');
        $userInfo = MemberUser::query()->find($auth['id']);
        return send($response, "ok", [
            'avatar' => $userInfo->avatar,
            'nickname' => $userInfo->nickname,
            'sex' => $userInfo->sex,
            'tel' => $userInfo->tel,
            'email' => $userInfo->email,
        ]);
    }

    #[Route(methods: 'POST', pattern: '/avatar')]
    public function avatar(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $auth = $request->getAttribute('auth');
        $uploads = $request->getUploadedFiles();
        $url = '';
        $manager = new ImageManager(['driver' => 'gd']);
        foreach ($uploads as $key => $vo) {
            $stream = $manager->make($vo->getStream())->resize(120, 120)->stream('jpg', 80);

            $basename = bin2hex(random_bytes(10));
            $path = '/avatar/' . $basename . '.jpg';
            App::storage()->write($path, $stream->getContents());
            $url = App::storage()->publicUrl($path);
            break;
        }
        MemberUser::query()->where('id', $auth['id'])->update([
            'avatar' => $url
        ]);
        return send($response, "修改头像成功", [
            'list' => [
                [
                    'url' => $url
                ]
            ]
        ]);
    }

    #[Route(methods: 'POST', pattern: '/data')]
    public function data(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = Validator::parser($request->getParsedBody(), [
            "nickname" => ["required", "请输入昵称"],
            "birthday" => [
                ["date", "请输入生日"],
                ["optional"],
            ],
        ]);

        $auth = $request->getAttribute('auth');
        $userData = [
            'nickname' => $data->nickname,
            'sex' => (int)$data->sex,
        ];
        if (isset($data->birthday)) {
            $userData['birthday'] = $data->birthday;
        }

        MemberUser::query()->where('id', $auth['id'])->update($userData);

        return send($response, "修改资料成功");
    }

    #[Route(methods: 'GET', pattern: '/code')]
    public function code(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $auth = $request->getAttribute('auth');
        $userInfo = MemberUser::query()->find($auth['id']);
        $tplId = Config::getValue('user_code');
        if (!$tplId) {
            throw new ExceptionBusiness('请设置验证码模板');
        }
        if (!$userInfo->tel) {
            throw new ExceptionBusiness('未设置手机号码');
        }
        Sms::code((int)$tplId, $userInfo->tel, 2);
        return send($response, '验证码已发送，请注意查收！');
    }

    #[Route(methods: 'POST', pattern: '/password')]
    public function password(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $auth = $request->getAttribute('auth');
        $userInfo = MemberUser::query()->find($auth['id']);
        if (!$userInfo->tel) {
            throw new ExceptionBusiness("未绑定手机号");
        }

        $data = Validator::parser($request->getParsedBody(), [
            "password" => ["required", "请输入新密码"],
            "code" => ["required", "请输入验证码"],
        ]);

        if (!$data->code) {
            throw new ExceptionBusiness("请输入验证码");
        }
        Sms::verify($userInfo->tel, $data->code, 2);

        MemberUser::query()->where('id', $auth['id'])->update([
            'password' => password_hash($data->password, PASSWORD_DEFAULT)
        ]);

        return send($response, "修改密码成功");
    }

    #[Route(methods: 'POST', pattern: '/bindTel')]
    public function bindTel(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $auth = $request->getAttribute('auth');
        $userInfo = MemberUser::query()->find($auth['id']);
        if ($userInfo->tel) {
            throw new ExceptionBusiness("已绑定手机号");
        }
        $data = Validator::parser($request->getParsedBody(), [
            "tel" => ["required", "请输入手机号码"],
            "code" => ["required", "请输入验证码"],
        ]);
        $exists = MemberUser::query()->where('tel', $data->tel)->exists();
        if ($exists) {
            throw new ExceptionBusiness("该手机号码已被绑定");
        }

        // 验证码
        $tplId = Config::getValue('user_code');
        if (!$tplId) {
            throw new ExceptionBusiness('请设置验证码模板');
        }
        Sms::verify($data->tel, $data->code, 2);

        MemberUser::query()->where('id', $auth['id'])->update([
            'tel' => $data->tel,
        ]);

        return send($response, "绑定手机号成功");
    }

    #[Route(methods: 'POST', pattern: '/bindEmail')]
    public function bindEmail(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $auth = $request->getAttribute('auth');
        $userInfo = MemberUser::query()->find($auth['id']);
        if ($userInfo->email) {
            throw new ExceptionBusiness("已绑定邮箱");
        }

        $data = Validator::parser($request->getParsedBody(), [
            "email" => ["required", "请输入邮箱"],
            //"code" => ["required", "请输入验证码"],
        ]);
        $exists = MemberUser::query()->where('email', $data->email)->exists();
        if ($exists) {
            throw new ExceptionBusiness("该邮箱已被绑定");
        }

        MemberUser::query()->where('id', $auth['id'])->update([
            'email' => $data->email
        ]);

        return send($response, "绑定邮箱成功");
    }

    #[Route(methods: 'GET', pattern: '/replaceCode')]
    public function replaceCode(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $auth = $request->getAttribute('auth');
        $params = $request->getQueryParams();
        $userInfo = MemberUser::query()->find($auth['id']);
        $tplId = Config::getValue('user_code');
        if (!$tplId) {
            throw new ExceptionBusiness('请设置验证码模板');
        }
        if (!$params['tel']) {
            throw new ExceptionBusiness('请输入手机号码');
        }
        if (empty($userInfo->tel)) {
            throw new ExceptionBusiness('未设置手机号码');
        }
        Sms::code((int)$tplId, $params['tel'], 9);
        return send($response, '验证码已发送，请注意查收！');
    }

    #[Route(methods: 'POST', pattern: '/replaceTel')]
    public function replaceTel(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $auth = $request->getAttribute('auth');
        $userInfo = MemberUser::query()->find($auth['id']);
        if (!$userInfo->tel) {
            throw new ExceptionBusiness('未设置手机号码');
        }
        $data = Validator::parser($request->getParsedBody(), [
            'tel' => ['required', '请输入更换手机号码'],
            'code' => ['required', '请输入更换验证码'],
            'code2' => ['required', '请输入原验证码'],
        ]);
        if ($userInfo->tel == $data->tel) {
            throw new ExceptionBusiness('原手机号码与新手机号码相同');
        }
        $exists = MemberUser::query()->where('tel', $data->tel)->exists();
        if ($exists) {
            throw new ExceptionBusiness("该手机号码已被绑定");
        }
        Sms::verify($userInfo->tel, $data->code2, 2);
        Sms::verify($data->tel, $data->code, 9);

        MemberUser::query()->where('id', $auth['id'])->update([
            'tel' => $data->tel,
        ]);

        return send($response, "绑定手机号成功");
    }
}