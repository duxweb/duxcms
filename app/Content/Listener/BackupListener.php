<?php

namespace App\Content\Listener;


use App\Content\Models\Article;
use App\Content\Models\ArticleAttr;
use App\Content\Models\ArticleAttrHas;
use App\Content\Models\ArticleClass;
use App\Content\Models\ArticleRecommend;
use App\Content\Models\ArticleRecommendHas;
use App\Content\Models\ArticleReplace;
use App\Content\Models\ArticleSource;
use App\Content\Models\ArticleTags;
use App\Content\Models\ArticleTagsHas;
use App\Content\Models\Menu;
use App\Content\Models\MenuData;
use App\Content\Models\Page;
use App\Tools\Event\BackupEvent;
use Dux\Event\Attribute\Listener;

class BackupListener
{

    #[Listener(name: 'tools.backup')]
    public function collect(BackupEvent $event): void
    {
        $event->set('article', Article::class);
        $event->set('article_attr', ArticleAttr::class);
        $event->set('article_attr_has', ArticleAttrHas::class);
        $event->set('article_class', ArticleClass::class);
        $event->set('article_recommend', ArticleRecommend::class);
        $event->set('article_recommend_has', ArticleRecommendHas::class);
        $event->set('article_replace', ArticleReplace::class);
        $event->set('article_source', ArticleSource::class);
        $event->set('article_tags', ArticleTags::class);
        $event->set('article_tags_has', ArticleTagsHas::class);
        $event->set('menu', Menu::class);
        $event->set('menu_data', MenuData::class);
        $event->set('page', Page::class);
    }
}