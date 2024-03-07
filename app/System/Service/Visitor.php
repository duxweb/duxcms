<?php

namespace App\System\Service;

use App\System\Models\LogVisit;
use App\System\Models\LogVisitData;
use App\System\Models\LogVisitSpider;
use App\System\Models\LogVisitUv;
use donatj\UserAgent\UserAgentParser;
use Dux\App;
use Jaybizzle\CrawlerDetect\CrawlerDetect;

class Visitor
{
    public static function increment(\Psr\Http\Message\ServerRequestInterface $request, string $type, string|int|null $id = null, string $driver = 'web', string $path = ''): void
    {
        $date = date('Y-m-d');
        $url = $request->getUri();
        $path = $path ?: $url->getPath();
        if (str_contains($path, '/theme') || str_contains($path, '/manage') || str_contains($path, '/install')) {
            return;
        }

        App::db()->getConnection()->beginTransaction();
        try {
            $ua = $request->getHeaderLine("HTTP_USER_AGENT");

            $parser = new UserAgentParser();
            $uaInfo = $parser->parse($ua);
            $browser = $uaInfo->browser();

            // pv
            $ip = get_ip();
            $view = LogVisit::query()->firstOrCreate([
                'has_type' => $type, 'has_id' => $id
            ]);
            $view->increment('pv');

            $viewData = LogVisitData::query()->firstOrCreate([
                'has_type' => $type, 'has_id' => $id, 'driver' => $driver, 'date' => $date
            ]);
            $viewData->increment('pv');

            $keys = [
                'type' => $type,
                'id' => $id,
                'driver' => $driver,
                'ip' =>$ip,
                'browser' => $browser,
            ];

            // uv
            $key = 'app:visitor:' . sha1(implode(':', $keys));
            if (!App::Redis()->get($key)) {
                App::Redis()->setex($key, 86400 - (time() + 8 * 3600) % 86400, 1);
                $view->increment('uv');
                $viewData->increment('uv');

                $uvData = LogVisitUv::query()->firstOrCreate([
                    'has_type' => $type, 'has_id' => $id, 'date' => $date, 'ip' => $ip, 'browser' => $browser, 'driver' => $driver,
                ]);

                if (!$uvData->country) {
                    try {
                        $address = App::geo()?->search($ip) ?: '';
                        [$country, $null, $province, $city] = explode('|', $address);
                    }catch (\Exception $e) {}
                    if ($address && $country) {
                        $uvData->country = $country ?: null;
                        $uvData->province = $province ?: null;
                        $uvData->city = $city ?: null;
                    }
                }
                $uvData->num = $uvData->num + 1;
                $uvData->save();
            }

            // spider
            $CrawlerDetect = new CrawlerDetect;
            if($CrawlerDetect->isCrawler($ua)) {
                $spiderData = LogVisitSpider::query()->firstOrCreate([
                    'has_type' => $type, 'has_id' => $id, 'date' => $date, 'name' => $CrawlerDetect->getMatches(),'path' => $path,
                ]);
                $spiderData->increment('num');
            }

            App::db()->getConnection()->commit();
        } catch (\Exception $e) {
            App::db()->getConnection()->rollback();
        }
    }
}