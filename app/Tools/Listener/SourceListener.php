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
            $fields = [];
            foreach ($item->fields as $field) {
                if (!$field['search']) {
                    continue;
                }
                $fields[] = $field['name'];
            }
            $event->set(
                name: 'magic.' . $item->name,
                label: $item->label,
                route: 'tools/data?magic=' . $item->name,
                data: function ($ids = null, $keyword = null) use ($fields, $item) {
                    $query = ToolsMagicData::query()->where('magic_id', $item->id);
                    if (isset($ids)) {
                        $query->whereIn('id', $ids);
                    }
                    if (isset($keyword)) {
                        foreach ($fields as $key) {
                            $query->where("data->$key", 'like', '%' . $keyword . '%');
                        }
                    }
                    return $query->get()->map(function ($item) {
                        return [
                            'id' => $item->id,
                            ...$item->data
                        ];
                    })->toArray();
                });
        });
    }
}