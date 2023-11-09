<?php

namespace App\System\Admin;

use App\System\Models\LogLogin;
use App\System\Models\SystemUser;
use donatj\UserAgent\UserAgentParser;
use Dux\App;
use Dux\Auth\AuthService;
use Dux\Handlers\ExceptionBusiness;
use Dux\Resources\Attribute\Action;
use Dux\Resources\Attribute\Resource;
use Dux\Route\Attribute\Route;
use Dux\Validator\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

#[Resource(app: 'admin', route: '/', name: 'auth', auth: false, actions: false)]
class Auth
{
    #[Action(methods: 'POST', route: 'login')]
    public function login(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = Validator::parser([...$request->getParsedBody(), ...$args], [
            "username" => ["required", __('system.auth.validator.username', 'manage')],
            "password" => ["required", __('system.auth.validator.password', 'manage')],
        ]);
        $info = SystemUser::query()->where("username", $data->username)->first();
        if (!$info) {
            throw new ExceptionBusiness(__('system.auth.error.login', 'manage'));
        }

        $this->loginCheck((int)$info->id);

        $useragent = $request->getHeader("user-agent")[0];
        $parser = new UserAgentParser();
        $ua = $parser->parse($useragent);
        $loginModel = LogLogin::query();
        $logData = [
            'user_type' => SystemUser::class,
            'user_id' => $info->id,
            'browser' => $ua->browser() . ' ' . $ua->browserVersion(),
            'ip' => get_ip(),
            'platform' => $ua->platform(),
        ];
        if (!password_verify($data->password, $info->password)) {
            $logData['status'] = false;
            $loginModel->create($logData);
            throw new ExceptionBusiness(__('system.auth.error.login', 'manage'));
        }

        $logData['status'] = true;
        $loginModel->create($logData);

        return send($response, "ok", [
            "userInfo" => [
                "id" => $info->id,
                "avatar" => $info->avatar,
                "username" => $info->username,
                "nickname" => $info->nickname,
                "rolename" => $info->roles[0]->name,
            ],
            "token" => "Bearer " . \Dux\Auth\Auth::token("admin", [
                    'id' => $info->id,
                ]),
            "permission" => $info->permission
        ]);
    }

    #[Action(methods: 'POST', route: 'check')]
    public function check(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = Validator::parser([...$request->getParsedBody(), ...$args], [
            "token" => ["required", "token does not exist"],
        ]);
        $request = $request->withHeader('Authorization', $data->token);
        $auth = new AuthService('admin');
        if (!$auth->check($request)) {
            throw new ExceptionBusiness('Expired or incorrect token');
        }
        $id = $auth->id($request);
        $info = SystemUser::query()->find($id);
        if (!$info->status) {
            throw new ExceptionBusiness('User Disabled');
        }

        return send($response, 'ok', [
            "userInfo" => [
                "id" => $info->id,
                "avatar" => $info->avatar,
                "username" => $info->username,
                "nickname" => $info->nickname,
                "rolename" => $info->roles[0]->name,
            ],
            "token" => "Bearer " . \Dux\Auth\Auth::token("admin", [
                    'id' => $info->id,
                ]),
            'permission' => $info->permission
        ]);
    }

    #[Action(methods: 'GET', route: 'menu', auth: true)]
    public function menu(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $auth = $request->getAttribute("auth");
        $userInfo = SystemUser::query()->find($auth["id"]);
        return send($response, "ok", App::menu('admin')->get($userInfo->permission ?: []));
    }

    private function loginCheck(int $id): void
    {
        $lasSeconds = now()->subSeconds(60);
        $loginList = LogLogin::query()->where('user_type', SystemUser::class)->where([
            'user_id' => $id,
            'status' => false,
            ['created_at', '>=', $lasSeconds->toDateTimeString()]
        ])->orderByDesc('id')->limit(3)->get();
        $loginLast = $loginList->first();
        $loginStatus = $loginList->count();
        $time = now();
        if ($loginStatus >= 3 && $loginLast->created_at->addSeconds(60)->gt($time)) {
            throw new ExceptionBusiness(__('system.auth.error.passwordCheck', 'manage'));
        }
    }


}