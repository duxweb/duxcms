<?php

namespace App\Tools\Listener;

use App\Tools\Event\SourceEvent;
use App\Tools\Models\ToolsMagic;
use App\Tools\Models\ToolsMagicData;
use Dux\App;

class SourceListener
{
    public function data(SourceEvent $event): void
    {
        ToolsMagic::query()->get()->map(function ($item) use ($event) {
            $event->set($item->label, 'tools/data?magic=' . $item->name, ToolsMagicData::class);
        });
    }
}