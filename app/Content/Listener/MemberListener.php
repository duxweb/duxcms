<?php

namespace App\Content\Listener;


use App\Content\Models\Article;
use App\Member\Event\CollectEvent;
use App\Member\Event\CommentEvent;
use App\Member\Event\PraiseEvent;
use Dux\Event\Attribute\Listener;

class MemberListener
{

    #[Listener(name: 'member.collect')]
    public function collect(CollectEvent $event): void
    {
        $event->setMap('文章', 'article', Article::class, function ($item) {
            return [
                'images' => $item->hastable->images,
                'description' => $item->hastable->descriptions,
                'view' => $item->view + $item->virtual_view
            ];
        });
    }

    #[Listener(name: 'member.comment')]
    public function comment(CommentEvent $event): void
    {
        $event->setMap('文章', 'article', Article::class);
    }

    #[Listener(name: 'member.praise')]
    public function praise(PraiseEvent $event): void
    {
        $event->setMap('文章', 'article', Article::class);
    }

}