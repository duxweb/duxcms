<?php

namespace App\System\Admin;

use App\System\Models\SystemRole;
use App\System\Models\SystemUser;
use Dux\Resources\Action\Resources;
use Dux\Resources\Attribute\Resource;
use Dux\Validator\Data;
use Illuminate\Database\Eloquent\Builder;
use Psr\Http\Message\ServerRequestInterface;

#[Resource(app: 'admin',  route: '/system/user', name: 'system.user')]
class User extends Resources {

    protected string $model = SystemUser::class;

    public function transform(object $item): array
    {
        return [
            "id" => $item->id,
            "username" => $item->username,
            "nickname" => $item->nickname,
            "avatar" => $item->avatar,
            "status" => (bool)$item->status,
            "roles" => $item->roles->pluck("id")->toArray(),
        ];
    }

    public function queryMany(Builder $query, ServerRequestInterface $request, array $args): void
    {
        $params = $request->getQueryParams();

        if ($params['keyword']) {
            $query->where('nickname', 'like', '%'.$params['keyword'].'%');
        }

        switch ($params['tab']) {
            case 1:
                $query->where('status', 1);
                break;
            case 2:
                $query->where('status', 0);
                break;
        }
    }

    public function validator(array $data, ServerRequestInterface $request, array $args): array
    {
        return [
            "nickname" => ["required", __('system.user.validator.nickname', 'manage')],
            "username" => [
                ["required", __('system.user.validator.username', 'manage')],
                [function($field, $value, $params, $fields) use ($args) {
                    $model = SystemUser::query()->where('username', $fields['username']);
                    if ($args['id']) {
                        $model->where("id", "<>", $args['id']);
                    }
                    return !$model->exists();
                }, __('system.user.validator.usernameExists', 'manage')]
            ],
            "password" => ["requiredWithout", "id", __('system.user.validator.password', 'manage')],
            "roles" => ["required", __('system.user.validator.roles', 'manage')],
        ];
    }

    public function format(Data $data, ServerRequestInterface $request, array $args): array
    {
        $formatData = [
            "nickname" => $data->nickname,
            "username" => $data->username,
            "avatar" => $data->avatar,
            "status" => $data->status,
        ];
        if ($data->password) {
            $formatData['password'] = function($value) {
                return password_hash($value, PASSWORD_BCRYPT);
            };
        }
        return $formatData;
    }

    public function createAfter(Data $data, mixed $info): void
    {
        $info->roles()->sync($data->roles ?: []);
    }

    public function editAfter(Data $data, mixed $info): void
    {
        $info->roles()->sync($data->roles ?: []);
    }


}