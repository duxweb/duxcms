<?php

namespace App\System\Admin;

use App\System\Models\LogOperate;
use App\System\Models\SystemUser;
use Dux\Resources\Action\Resources;
use Dux\Resources\Attribute\Resource;
use Illuminate\Database\Eloquent\Builder;
use Psr\Http\Message\ServerRequestInterface;

#[Resource(app: 'admin',  route: '/system/operate', name: 'system.operate', actions: ['list', 'show'])]
class Operate extends Resources {

    protected string $model = LogOperate::class;

    public array $excludesMany = ['request_params'];

    public function queryMany(Builder $query, ServerRequestInterface $request, array $args): void
    {
        $query->with("user");
        $query->where("user_type", SystemUser::class);
        $params = $request->getQueryParams();

        $userId = $params["user"];
        if ($userId) {
            $query->where('user_id', $userId);
        }

        $method = $params["method"];
        if ($method) {
            $query->where('request_method', $method);
        }

        $date = $params["date"];
        if ($date && is_array($date)) {
            $query->whereBetween('created_at', [$date[0], $date[1]]);
        }

        $query->orderByDesc('id');
    }

    public function transform(object $item): array
    {
        return [
            "id" => $item->id,
            "username" => $item->user->username,
            "nickname" => $item->user->nickname,
            "request_method" => $item->request_method,
            "request_url" => $item->request_url,
            "request_time" => $item->request_time,
            "request_params" => $item->request_params,
            "route_name" => $item->route_name,
            "route_title" => $item->route_title,
            "client_ua" => $item->client_ua,
            "client_ip" => $item->client_ip,
            "client_browser" => $item->client_browser,
            "client_device" => $item->client_device,
            "time" => $item->created_at->format("Y-m-d H:i:s"),
        ];
    }


}