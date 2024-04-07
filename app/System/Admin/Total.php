<?php

namespace App\System\Admin;

use App\System\Service\Stats;
use App\System\Service\System;
use Dux\App;
use Dux\Handlers\ExceptionBusiness;
use Dux\Resources\Action\Resources;
use Dux\Resources\Attribute\Action;
use Dux\Resources\Attribute\Resource;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionExtension;

#[Resource(app: 'admin', route: '/system/total', name: 'system.total', actions: false)]
class Total extends Resources
{

    #[Action(methods: 'GET', route: '')]
    public function stats(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $v = "version()";
        $mysql = App::db()->getConnection()->select('select version()')[0]->$v;

        $redisVer = null;
        try {
            $redis = new ReflectionExtension('redis');
            $redisVer = $redis->getVersion();
        } catch (\Exception) {
        }

        return send($response, 'ok', [
            'sys' => [
                'php' => phpversion(),
                'mysql' => $mysql,
                'redis' => $redisVer,
                'time' => gmdate("Y-m-d H:i:s"),
            ],
            'extend' => [
                'gd' => extension_loaded('gd'),
                'imagick' => extension_loaded('imagick'),
                'zip' => extension_loaded('zip'),
                'redis' => extension_loaded('redis'),
            ]
        ]);
    }

    #[Action(methods: 'GET', route: '/hardware')]
    public function hardware(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $stats = new System();
        return send($response, 'ok', [
            'load' => $stats->getLoad(),
            'mem' => $stats->getMemUsage(),
            'cpu' => $stats->getCpuUsage(),
            'disk' => $stats->getHdUsage()
        ]);
    }

    #[Action(methods: 'GET', route: '/speed')]
    public function speedNode(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $client = new Client();
        $result = $client->post('https://www.cesu.net/user/getPingList');
        $content = $result?->getBody()?->getContents();
        $data = json_decode($content, true);

        if ($data['code']) {
            throw new ExceptionBusiness($data['msg']);
        }

        if (!$data['data'] || !is_array($data['data'])) {
            throw new ExceptionBusiness(__('system.total.validator.node', 'manage'));
        }

        $values = [];
        foreach ($data['data'] as $vo) {
            $values[$vo['node_type_txt']] = true;
        }

        $token = App::config('use')->get('extend.speed');
        return send($response, 'ok', array_keys($values), [
            'token' => !!$token
        ]);
    }

    #[Action(methods: 'POST', route: '/speed')]
    public function speedTest(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = $request->getParsedBody() ?: [];
        $net = $data['net'];
        $token = $data['token'];

        if (!$net || !is_array($net)) {
            throw new ExceptionBusiness(__('system.total.validator.line', 'manage'));
        }

        $t = App::config('use')->get('extend.speed');
        if (!$t && !$token) {
            throw new ExceptionBusiness(__('system.total.validator.token', 'manage'));
        }

        if ($token) {
            $use = App::config('use');
            $use->set('extend.speed', $token);
            $use->toFile(config_path('/use.yaml'));
            $t = $token;
        }

        $client = new Client();

        $result = $client->post('https://www.cesu.net/user/getPingList');
        $content = $result?->getBody()?->getContents();
        $data = json_decode($content, true);

        if ($data['code']) {
            throw new ExceptionBusiness($data['msg']);
        }

        if (!$data['data'] || !is_array($data['data'])) {
            throw new ExceptionBusiness(__('system.total.validator.node', 'manage'));
        }

        $provinces = $this->chinaProvinceConver();
        $nodes = [];
        foreach ($data['data'] as $vo) {
            if (!in_array($vo['node_type_txt'], $net)) {
                continue;
            }
            $nodes[] = [
                'nid' => $vo['id'],
                'china_province' => $provinces[$vo['provincial']],
                'province' => $provinces[$vo['provincial']] ?: $vo['provincial'],
                'city' => $vo['city'],
                'type' => $vo['node_type_txt'],
            ];
        }
        if (!$nodes) {
            throw new ExceptionBusiness(__('system.total.validator.node', 'manage'));
        }

        $result = $client->post('https://www.cesu.net/user/GetTaskId', [
            'form_params' => [
                'token' => $t,
                'type' => 3,
                'url' => $_SERVER['HTTP_HOST'],
                'nid' => join(',', array_column($nodes, 'nid'))
            ]
        ]);
        $content = $result?->getBody()?->getContents();
        $data = json_decode($content, true);

        if ($data['code']) {
            throw new ExceptionBusiness($data['msg']);
        }

        return send($response, 'ok', [
            'taskId' => $data['data']['taskId'],
            'nodes' => $nodes
        ]);
    }

    private function chinaProvinceConver(): array
    {
        return [
            "河北" => "河北省",
            "山西" => "山西省",
            "辽宁" => "辽宁省",
            "吉林" => "吉林省",
            "黑龙江" => "黑龙江省",
            "江苏" => "江苏省",
            "浙江" => "浙江省",
            "安徽" => "安徽省",
            "福建" => "福建省",
            "江西" => "江西省",
            "山东" => "山东省",
            "河南" => "河南省",
            "湖北" => "湖北省",
            "湖南" => "湖南省",
            "广东" => "广东省",
            "海南" => "海南省",
            "四川" => "四川省",
            "贵州" => "贵州省",
            "云南" => "云南省",
            "陕西" => "陕西省",
            "甘肃" => "甘肃省",
            "青海" => "青海省",
            "台湾" => "台湾省",
            "北京" => "北京市",
            "天津" => "天津市",
            "上海" => "上海市",
            "重庆" => "重庆市",
            "香港" => "香港特别行政区",
            "澳门" => "澳门特别行政区",
            "内蒙" => "内蒙古自治区",
            "广西" => "广西壮族自治区",
            "西藏" => "西藏自治区",
            "宁夏" => "宁夏回族自治区",
            "新疆" => "新疆维吾尔族自治区"
        ];
    }

}