<?php

namespace App\Content\Service;

use App\Content\Models\ArticleClass;
use App\System\Enum\PlatformEnum;
use App\System\Service\Config;
use App\Tools\Service\Qrcode;
use Dux\App;
use Dux\Handlers\ExceptionBusiness;
use Dux\Handlers\ExceptionNotFound;
use Psr\Http\Message\ServerRequestInterface;

class Article
{
    // 查询
    public static function query(array $where = [], $classId = null, $recId = null, $top = false, $image = null, ?string $keyword = null, ?string $tag = null, ?string  $order = 'id desc'): \Illuminate\Database\Eloquent\Builder
    {
        $query = \App\Content\Models\Article::query()->with(['attrs', 'tags', 'recommend'])->where(function ($query) {
            $query->whereNull('push_at')->orWhere('push_at', '<=', now()->toDateTimeString());
        });

        if ($where) {
            $query->where($where);
        }

        if ($classId) {
            $classList = ArticleClass::query()->with(['descendants'])->whereIn('id', is_array($classId) ? $classId : [$classId])->get();
            $classIds = [];
            $tops = [];
            foreach ($classList as $vo) {
                $classIds[] = $vo['id'];
                $classIds = [...$classIds, ...$vo->descendants->pluck('id')];
                if ($vo->tops) {
                    $tops = [...$tops, ...$vo->tops];
                }
            }
            $query->whereIn('class_id', $classIds);
            if ($tops && $top) {
                $topsStr = implode(',', $tops);
                $query->select('*', App::db()->getConnection()->raw("IF(id IN ($topsStr), true, false) as top"));

                $topsSql = [];
                $tops = array_reverse($tops);
                foreach ($tops as $index => $id) {
                    $topsSql[] = "when id = $id then $index";
                }
                $query->orderByRaw(sprintf("(case %s else -1 end) desc", implode(' ', $topsSql)));
            }
        }

        if (isset($keyword)){
            $keywords = explode(' ', $keyword);
            foreach ($keywords as &$k) {
                $k = '+' . $k;
            }
            unset($k);
            return self::query()->whereFullText(['title', 'content'], implode(' ', $keywords), ['mode' => 'boolean']);
        }

        if (isset($tag)) {
            $query->whereHas('tags', function ($query) use ($tag) {
                $query->where('name', $tag);
            });
        }

        if ($recId) {
            $query->whereHas('recommend', function ($query) use ($recId) {
                $query->where('id', $recId);
            });
            $order = null;
        }

        if ($order) {
            $query->orderByRaw($order);
        }

        if (isset($image)) {
            if ($image) {
                $query->whereRaw('JSON_LENGTH(images) > 0');
            }else {
                $query->whereRaw('JSON_LENGTH(images) = 0');
            }
        }

        return $query;
    }

    // 列表
    public static function lists(array $where = [], $classId = null, $recId = null, $top = false, $image = null,  ?string $keyword = null, ?string $tag = null, $limit = 20, $order = 'id desc'): \Illuminate\Database\Eloquent\Collection|array
    {
        return self::query($where, $classId, $recId, $top, $image, $keyword, $tag,  $order)->limit($limit)->get();
    }

    // 分页
    public static function page(array $where = [], $classId = null, $recId = null, $top = true, $image = null,  ?string $keyword = null, ?string $tag = null, int $limit = 20, $order = 'id desc'): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return self::query($where, $classId, $recId, $top, $image, $keyword, $tag, $order)->paginate($limit);
    }

    // 详情
    public static function info(int|string $id, array $where = []): object
    {
        $info = self::query($where)->where(function ($query) use ($id) {
            $query->where('id', $id)->orwhere('url', $id);
        })->first();
        if (!$info) {
            throw new ExceptionNotFound();
        }
        $info->increment('view');
        return $info;
    }


    // 推荐内容
    public static function recommend(object $info, int $limit = 5): \Illuminate\Database\Eloquent\Collection|array
    {
        return \App\Content\Service\Article::query()->whereHas('tags', function($q) use ($info) {
            $q->whereIn('name', $info->tags->pluck('name'));
        })->limit($limit)->get();
    }


    // 上一篇
    public static function prev(int $id): object|null
    {
        return \App\Content\Service\Article::query()->where('id', '<', $id)->latest('id')->first();
    }

    // 下一篇
    public static function next(int $id): object|null
    {
        return \App\Content\Service\Article::query()->where('id', '>', $id)->latest('id')->first();
    }



    // 目录
    public static function catalogs($classId = null)
    {
        $list = \App\Content\Models\ArticleClass::query()->descendantsAndSelf($classId)->toTree();
        $ids = ArticleClass::query()->descendantsAndSelf($classId)->pluck("id");
        $articleList = \App\Content\Models\Article::query()->whereIn('class_id', $ids)->get()->groupBy("class_id");
        return self::children($list, $articleList);
    }

    private static function children($list, $articleList)
    {
        foreach ($list as $key => $vo) {
            if ($vo->children) {
                $list[$key]->children = self::children($vo->children, $articleList);
            }
            $list[$key]->articles = $articleList[$vo->id];
        }
        return $list;
    }


    public static function genQrcode(int $id, int $userId, ServerRequestInterface $request)
    {
        $info = \App\Content\Models\Article::query()->find($id);
        $event = new \App\Content\Event\QrcodeEvent($info, $userId);
        App::event()->dispatch($event, 'content.qrcode');

        $params = $event->getParams();

        $platform = PlatformEnum::from($request->getHeaderLine('platform') ?: 'web');
        $refererUrl = $platform->url();
        if (!$refererUrl) {
            $uri = $request->getUri();
            $refererUrl = sprintf('%s://%s', $uri->getScheme(), $uri->getHost());
        }
        $qrcode = Qrcode::generate($platform, $refererUrl, '/duxcmsContent/pages/article/detail?id=' . $info->id, $params);

        $cacheKeyStr = 'poster.' . sha1(json_encode($params));
        if (App::cache()->has($cacheKeyStr)) {
            return App::cache()->get($cacheKeyStr);
        }

        $config = Config::getJsonValue('content', []);

        $poster = $config['article_poster'];
        if (!$poster) {
            throw new ExceptionBusiness('请联系管理员设置模板');
        }

        $content = \App\Poster\Service\Poster::generate($poster, [
            'image' => $info->images[0],
            'qrcode' => $qrcode,
            'title' => $info->title,
            'source' => $info->source,
            'time' => $info->created_at?->toDateString(),
        ]);
        $basename = bin2hex(random_bytes(10));
        $filename = sprintf('article-%s.%0.8s', $basename, 'png');
        $publicUrl = "/poster/$filename";
        App::storage()->write($publicUrl, $content);
        $url = App::storage()->publicUrl($publicUrl);
        App::cache()->set($cacheKeyStr, $url, 14400);
        return $url;
    }

}