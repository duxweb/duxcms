<?php

declare(strict_types=1);

namespace App\Member\Admin;

use App\Member\Event\RegisterEvent;
use App\Member\Models\MemberUser;
use App\System\Service\Config;
use Dux\App;
use Dux\Handlers\ExceptionBusiness;
use Dux\Resources\Action\Resources;
use Dux\Resources\Attribute\Action;
use Dux\Resources\Attribute\Resource;
use Dux\Validator\Data;
use Illuminate\Database\Eloquent\Builder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

#[Resource(app: 'admin', route: '/member/user', name: 'member.user')]
class User extends Resources
{
    protected string $model = MemberUser::class;

    public function queryMany(Builder $query, ServerRequestInterface $request, array $args): void
    {
        $params = $request->getQueryParams();
        $search = $params["keyword"];
        if ($search) {
            $query->where(function (Builder $query) use ($search) {
                $query->where("nickname", "like", "%$search%");
                $query->orWhere("tel", $search);
                $query->orWhere("email", $search);
            });
        }
        $primary = $params["primary"];
        if ($primary) {
            $query->where('id', $primary);
        }
    }

    public function transform(object $item): array
    {
        return [
            "id" => $item->id,
            "level_id" => $item->level_id,
            "level_name" => $item->level->name,
            "nickname" => $item->nickname,
            "email" => $item->email,
            "tel" => $item->tel,
            "avatar" => $item->avatar,
            "sex" => $item->sex,
            "birthday" => $item->birthday,
            "growth" => $item->growth,
            "login_at" => $item->login_at,
            "login_ip" => $item->login_ip,
            "status" => (bool)$item->status,
            "created_time" => $item->created_at?->format('Y-m-d H:i'),
        ];
    }

    public function validator(array $data, ServerRequestInterface $request, array $args): array
    {
        return [
            "nickname" => ["required", __('member.user.validator.nickname', 'manage')],
            "tel" => ["required", __('member.user.validator.tel', 'manage')],
            "level_id" => ["required", __('member.user.validator.level', 'manage')],
        ];
    }

    public function format(Data $data, ServerRequestInterface $request, array $args): array
    {
        $id = $args['id'];
        $model = MemberUser::query()->where('tel', $data->tel);
        if ($id) {
            $model->where("id", "<>", $id);
        }
        if ($model->exists()) {
            throw new ExceptionBusiness(__('member.user.validator.exists', 'manage'));
        }
        $saveData = [
            "level_id" => $data->level_id,
            "nickname" => $data->nickname,
            "tel" => $data->tel,
            "email" => $data->email,
            "avatar" => $data->avatar,
            "sex" => $data->sex,
            "birthday" => $data->birthday,
            "status" => $data->status,
        ];
        // 默认头像
        if (empty($saveData['avatar'])) {
            $saveData['avatar'] = Config::getValue('user_default_avatar');
        }

        if ($data->password) {
            $saveData["password"] = password_hash($data->password, PASSWORD_BCRYPT);
        }
        return $saveData;
    }

    public function saveAfter(Data $data, $info, int $id): void
    {
        if ($id) {
            return;
        }
        // NOTE member.register （用户注册事件）
        App::event()->dispatch(new RegisterEvent($info), 'member.register');
    }


}
