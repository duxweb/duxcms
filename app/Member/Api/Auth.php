<?php

namespace App\Member\Api;

use App\Member\Models\MemberUser;
use App\Member\Service\Member;
use App\System\Service\Config;
use Dux\Handlers\ExceptionBusiness;
use Dux\Route\Attribute\Route;
use Dux\Validator\Validator;
use Dux\Vaptcha\Vaptcha;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use App\Sms\Service\Sms;

class Auth
{
    private bool $debug = false;

    #[Route(methods: 'GET',  pattern: '/member/info', app: 'apiMember')]
    public function info(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $auth = $request->getAttribute('auth');
        $data = Member::getUserInfo((int)$auth['id']);
        return send($response, 'ok', $data);
    }

    #[Route(methods: 'GET',  pattern: '/member/check', app: 'api')]
    public function check(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = Validator::parser($request->getQueryParams() ?: [], [
            "username" => ["required", "请输入账号"],
        ]);
        $userType = Member::getUserType($data->username);
        $model = MemberUser::query();
        if ($userType == 'tel') {
            $model->where('tel', $data->username);
        }
        if ($userType == 'email') {
            $model->where('email', $data->username);
        }

        $check = $model->exists();
        return send($response, 'ok', [
            'check' => $check
        ]);
    }

    #[Route(methods: 'POST', pattern: '/member/register', app: 'api')]
    public function register(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = Validator::parser($request->getParsedBody() ?: [], [
            "username" => ["required", "请输入账号"],
        ]);
        $userType = Member::getUserType($data->username);

        $model = MemberUser::query();
        if ($userType == 'tel') {
            $model->where('tel', $data->username);
        }
        if ($userType == 'email') {
            $model->where('email', $data->username);
        }
        $info = $model->first();
        if ($info) {
            throw new ExceptionBusiness("该账号已被注册");
        }
        if ($userType == 'tel') {
            if (!$data->code) {
                throw new ExceptionBusiness("请输入验证码");
            }
            if (!$this->debug) {
                Sms::verify($data->username, $data->code);
            }
        }
        if ($userType == 'email') {
            if (!$data->code) {
                throw new ExceptionBusiness("请输入验证码");
            }
        }
        $password = '';
        if ($data->password) {
            $password = $data->password;
        }
        if ($userType == 'tel') {
            $uid = Member::Register(tel: $data->username, password: $password, params: $data);
        }
        if ($userType == 'email') {
            $uid = Member::Register(email: $data->username, password: $password, params: $data);
        }
        return send($response, "ok", Member::Login($uid));
    }

    #[Route(methods: 'POST', pattern: '/member/login', app: 'api')]
    public function login(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = Validator::parser($request->getParsedBody() ?: [], [
            "username" => ["required", "请输入账号"],
        ]);
        $types = ['password', 'code'];
        $type = in_array($data->type, $types) ? $data->type : 'code';
        $userType = Member::getUserType($data->username);
        $model = MemberUser::query();
        if ($userType == 'tel') {
            $model->where('tel', $data->username);
        }
        if ($userType == 'email') {
            $model->where('email', $data->username);
        }
        $info = $model->first();
        if (!$info) {
            throw new ExceptionBusiness("该账号未注册");
        }
        if ($userType == 'email') {
            if (!$info->password) {
                throw new ExceptionBusiness("请输入登录密码");
            }
        }
        if ($userType == 'tel') {
            if ($type === 'password') {
                if (!$data->password) {
                    throw new ExceptionBusiness("请输入密码");
                }
                if ($info && !$info->password) {
                    throw new ExceptionBusiness("该账号只能用验证码登录");
                }
                if (!password_verify($data->password, $info->password)) {
                    throw new ExceptionBusiness("账号或密码错误");
                }
            }
            if ($type === 'code') {
                if (!$data->code) {
                    throw new ExceptionBusiness("请输入验证码");
                }
                if (!$this->debug) {
                    Sms::verify($data->username, $data->code);
                }
            }
        }
        return send($response, "ok", Member::Login($info->id));
    }

    #[Route(methods: 'POST', pattern: '/member/forget', app: 'api')]
    public function forget(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = Validator::parser($request->getParsedBody() ?: [], [
            "username" => ["required", "请输入账号"],
        ]);

        $userType = Member::getUserType($data->username);

        $model = MemberUser::query();
        if ($userType == 'tel') {
            $model->where('tel', $data->username);
        }
        if ($userType == 'email') {
            $model->where('email', $data->username);
        }
        $info = $model->first();

        if (!$info) {
            throw new ExceptionBusiness("该账号不存在");
        }

        if ($userType == 'tel') {
            if (!$data->code) {
                throw new ExceptionBusiness("请输入验证码");
            }
            if (!$this->debug) {
                Sms::verify($data->username, $data->code);
            }
        }

        if ($userType == 'email') {
            if (!$data->code) {
                throw new ExceptionBusiness("请输入验证码");
            }
        }

        $password = password_hash($data->password, PASSWORD_DEFAULT);
        $info->password = $password;
        $info->save();
        return send($response, "修改密码成功");
    }

    #[Route(methods: 'GET', pattern: '/member/code', app: 'api')]
    public function code(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = Validator::parser($request->getQueryParams() ?: [], [
            "username" => ["required", "请输入账号"],
        ]);
        $tplId = Config::getValue('user_code');
        if (!$tplId) {
            throw new ExceptionBusiness('请设置验证码模板');
        }
        $userType = Member::getUserType($data->username);

        Vaptcha::Verify($data->server, $data->token);

        if ($userType == 'tel') {
            Sms::code((int)$tplId, $data->username);
        }
        if ($userType == 'email') {
            throw new ExceptionBusiness("暂无邮件配置");
        }
        return send($response, '验证码已发送，请注意查收！');
    }

    #[Route(methods: 'GET', pattern: '/member/agreement', app: 'api')]
    public function agreement(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $content = Config::getValue('user_agreement');
        return send($response, 'ok', [
            'content' => $content
        ]);
    }

    #[Route(methods: 'GET', pattern: '/member/privacy', app: 'api')]
    public function privacy(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $content = Config::getValue('user_privacy');
        return send($response, 'ok', [
            'content' => $content
        ]);
    }

    #[Route(methods: 'GET', pattern: '/member/about', app: 'api')]
    public function about(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $content = Config::getValue('user_about');
        return send($response, 'ok', [
            'content' => $content
        ]);
    }

}