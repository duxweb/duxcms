<?php

namespace App\Member\Listener;

use App\Member\Event\ContentEvent;
use App\Member\Models\MemberComment;
use Dux\Event\Attribute\Listener;

class MemberListener
{
    #[Listener(name: 'member.content')]
    public function data(ContentEvent $event): void
    {
        $event->setMap('comment', MemberComment::class);
    }

}