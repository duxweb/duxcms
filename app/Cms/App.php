<?php

declare(strict_types=1);

namespace App\Cms;
use App\Content\Models\ArticleReplace;
use Dux\App\AppExtend;
use Dux\Bootstrap;
use Parsedown;

/**
 * Application Registration
 */
class App extends AppExtend
{

    public function init(Bootstrap $app): void
    {
    }



    public function register(Bootstrap $app): void
    {

        \Dux\App::view('web')->addExtension(new \Latte\Essential\RawPhpExtension);
        \Dux\App::view('web')->addFilter('json', function ($data) {
            return json_encode($data);
        });
        \Dux\App::view('web')->addFilter('keywords', function ($data, $keyword) {
            $keyword = preg_replace('/\s+/', ' ', $keyword ?: '');
            $keywords = explode(' ', $keyword);
            foreach ($keywords as $keyword) {
                $data = str_replace($keyword, "<span style='color: red'>$keyword</span>", $data);
            }
            return $data;
        });

        \Dux\App::view('web')->addFilter('tags', function ($content, $prefix, $tags) {
            foreach ($tags as $vo) {
                $content = str_replace($vo->name, "<a href='$prefix/{$vo->name}'>$vo->name</a>", $content);
            }
            return $content;
        });

        \Dux\App::view('web')->addFilter('replace', function ($data) {
            $has = 'content-replace';
            if (!\Dux\App::di()->has($has)) {
                $replaceList = ArticleReplace::query()->get();
                \Dux\App::di()->set($has, $replaceList);
            }else {
                $replaceList = \Dux\App::di()->get($has);
            }
            foreach ($replaceList as $vo) {
                $data = str_replace($vo->from, $vo->to, $data);
            }
            return $data;
        });


        \Dux\App::view('web')->addFilter('markdown', function ($content) {
            $ParsedownToc = new \ParsedownToc();
            return $ParsedownToc->body($content);
        });


        \Dux\App::view('web')->addFilter('markdown_toc', function ($content) {
            $ParsedownToc = new \ParsedownToc();
            $ParsedownToc->body($content);
            return $ParsedownToc->contentsList();
        });

    }

}
