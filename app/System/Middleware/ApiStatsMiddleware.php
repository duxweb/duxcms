<?php

namespace App\System\Middleware;

use App\System\App;
use App\System\Models\LogApi;
use Illuminate\Support\Facades\DB;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Routing\RouteContext;

class ApiStatsMiddleware {

    private string $hasType;

    public function __construct(string $hasType = "common") {
        $this->hasType = $hasType;
    }

    public function __invoke(Request $request, RequestHandler $handler): Response {
        $startTime = microtime(true);
        $response = $handler->handle($request);
        $time = round(microtime(true) - $startTime, 3);
        $route = RouteContext::fromRequest($request)->getRoute();
        $info = LogApi::query()->where("name", $route->getName())->where("has_type", $this->hasType)->where("data", date("Y-m-d"))->first();
        \Dux\App::db()->getConnection()->beginTransaction();
        try {
            // PV
            if ($info) {
                $data = [];
                if ($time <= $info->min_time) {
                    $data['min_time'] = $time;
                }
                if ($time >= $info->max_time) {
                    $data['max_time'] = $time;
                }
                LogApi::query()->where('id', $info->id)->increment('pv', 1, $data);
                $id = $info->id;
            } else {
                $data = [
                    'method' => $request->getMethod(),
                    'name' => $route->getName(),
                    'desc' => $route->getArgument("route:title"),
                    'date' => date('Y-m-d'),
                    'min_time' => $time,
                    'max_time' => $time,
                    'has_type' => $this->hasType
                ];
                $id = LogApi::query()->create($data)->id;
            }
            // UV
            $keys = [
                'method' => $data['method'],
                'name' => $data['name'],
                'ip' => get_ip(),
                'ua' => $request->getHeaderLine("HTTP_USER_AGENT"),
            ];
            $key = 'log:api:'.md5(implode(':', $keys));
            if (!\Dux\App::redis()->get($key)) {
                \Dux\App::redis()->setex($key, 86400 - (time() + 8 * 3600) % 86400, 1);
                LogApi::query()->where('id', $id)->increment('uv');
            }
            \Dux\App::db()->getConnection()->commit();
        }catch (\Exception $e) {
            \Dux\App::db()->getConnection()->rollBack();
            throw $e;
        }
        return $response;
    }
}