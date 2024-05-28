<?php

namespace App\Cms\Admin;

use App\Content\Models\Article;
use App\System\Models\LogVisit;
use App\System\Models\LogVisitData;
use App\System\Models\LogVisitSpider;
use App\System\Models\LogVisitUv;
use Dux\App;
use Dux\Resources\Attribute\Action;
use Dux\Resources\Attribute\Resource;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

#[Resource(app: 'admin', route: '/cms/stats', name: 'cms', actions: false)]
class Stats
{
    #[Action(methods: 'GET', route: '/index')]
    public function index(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {

        // 计数统计
        $articleCount = Article::query()->count();
        $spiderCount = LogVisitSpider::query()->where('has_type', 'common')->sum('num');
        $pvCount = LogVisit::query()->where('has_type', 'common')->sum('pv');
        $uvCount = LogVisit::query()->where('has_type', 'common')->sum('uv');

        $nums = [
            'article' => $articleCount,
            'spider' => $spiderCount,
            'pv' => $pvCount,
            'uv' => $uvCount,
        ];

        // 访问统计
        $dates = collect(range(30,0))->map(function ($daysAgo) {
            return now()->subDays($daysAgo)->format('Y-m-d');
        })->toArray();

        $startTime = now()->subDays(30)->format('Y-m-d 00:00:00');

        $viewData = LogVisitData::query()
            ->select(
                'date',
                App::db()->getConnection()->raw('SUM(pv) as pv'),
                App::db()->getConnection()->raw('SUM(uv) as uv'))
            ->groupBy('date')
            ->where('created_at', '>=', $startTime)
            ->where('has_type', 'common')
            ->get()->keyBy('date');

        $viewLabels = $dates;
        $viewPvs = [];
        $viewUvs = [];
        foreach ($viewLabels as $date) {
            $viewPvs[] = $viewData[$date]->pv ?: 0;
            $viewUvs[] = $viewData[$date]->uv ?: 0;
        }

        // 发布统计
        $startTime = now()->subDays(6)->format('Y-m-d 00:00:00');
        $pushData = Article::query()
            ->select(
                App::db()->getConnection()->raw('DATE_FORMAT(created_at, "%Y-%m-%d") as date'),
                App::db()->getConnection()->raw('COUNT(*) as num'),
            )
            ->groupBy(App::db()->getConnection()->raw('DATE_FORMAT(created_at, "%Y-%m-%d")'))
            ->where('created_at', '>=', $startTime)
            ->get()->keyBy('date');


        $dates = collect(range(6,0))->map(function ($daysAgo) {
            return now()->subDays($daysAgo)->format('Y-m-d');
        })->toArray();

        $pushNums = [];
        foreach ($dates as $date) {
            $pushNums[] = $pushData[$date]->num ?: 0;
        }
        $pushLabels = collect(range(6, 0))->map(function ($daysAgo) {
            return now()->subDays($daysAgo)->isoFormat('dddd');
        })->toArray();

        // 分类统计
        $classData = Article::query()
            ->with('class')
            ->select(
                'class_id',
                App::db()->getConnection()->raw('COUNT(*) as num'),
            )
            ->groupBy('class_id')
            ->get();

        $classLabels = [];
        $classNums = [];
        foreach ($classData as $data) {
            $classNums[] = $data->num;
            $classLabels[] = $data->class->name;
        }

        // 浏览器来源
        $browsers = LogVisitUv::query()
            ->select(
                'browser',
                App::db()->getConnection()->raw('COUNT(*) as num'),
            )
            ->groupBy('browser')
            ->limit(10)
            ->orderBy('num')
            ->get()
            ->toArray();

        // 地区来源
        $ips = LogVisitUv::query()
            ->select(
                'city',
                App::db()->getConnection()->raw('COUNT(*) as num'),
            )
            ->groupBy('city')
            ->limit(10)
            ->orderBy('num')
            ->get()
            ->toArray();


        // 蜘蛛统计
        $spiderData = LogVisitSpider::query()
            ->select(
                'date',
                'name',
                App::db()->getConnection()->raw('COUNT(*) as num'),
            )
            ->groupBy('name', 'date')
            ->orderBy('num')
            ->limit(6)
            ->get()->toArray();

        $dates = collect(range(30,0))->map(function ($daysAgo) {
            return now()->subDays($daysAgo)->format('Y-m-d');
        })->toArray();
        $spiderLabels = $dates;
        $spiderMaps = [];
        foreach ($spiderData as $spider) {
            $spiderMaps[$spider['name']][$spider['date']] = $spider['num'] ?: 0;
        }

        $spiderFormat = [];
        foreach ($spiderMaps as $key => $spider) {
            foreach ($dates as $date) {
                $spiderFormat[$key][] = $spider[$date] ?: 0;
            }
        }
        $spiderResult = [];
        foreach ($spiderFormat as $key => $vo) {
            $spiderResult[] = [
              'name' => $key,
              'data' => $vo ?: []
            ];
        }


        return send($response, 'ok', [
            'nums' => $nums,
            'views' => [
                'labels' => $viewLabels,
                'pvs' => $viewPvs,
                'uvs' => $viewUvs,
            ],
            'push' => [
                'labels' => $pushLabels,
                'nums' => $pushNums
            ],
            'class' => [
                'labels' => $classLabels,
                'nums' => $classNums
            ],
            'spider' => [
                'labels' => $spiderLabels,
                'data' => $spiderResult
            ],
            'browsers' => $browsers,
            'ips' => $ips
        ]);
    }

}