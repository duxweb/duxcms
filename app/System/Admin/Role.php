<?php

namespace App\System\Admin;

use App\System\Models\SystemRole;
use Dux\App;
use Dux\Resources\Action\Resources;
use Dux\Resources\Attribute\Action;
use Dux\Resources\Attribute\Resource;
use Dux\Validator\Data;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

#[Resource(app: 'admin',  route: '/system/role', name: 'system.role')]
class Role extends Resources {

    protected string $model =  SystemRole::class;


    public array $excludesMany = ['permission'];

    public function transform(object $item): array
    {
        return [
            "id" => $item->id,
            "name" => $item->name,
            "permission" => $item->permission
        ];
    }

    public function validator(array $data, ServerRequestInterface $request, array $args): array
    {
        return [
            "name" => ["required", "请输入名称"],
        ];
    }

    public function format(Data $data, ServerRequestInterface $request, array $args): array
    {
        $permission = [];
        foreach ($data->permission as $vo) {
            if (!str_contains($vo, "group:")) {
                $permission[] = $vo;
            }
        }
        return [
            "name" => $data->name,
            "permission" => $permission,
        ];
    }

    #[Action(methods: 'GET', route: '/permission')]
    public function permission(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $permission = App::permission("admin")->get();
        return send($response, 'ok', $permission);

    }
}