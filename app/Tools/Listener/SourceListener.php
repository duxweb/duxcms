<?php

namespace App\Tools\Listener;

use App\Tools\Event\SourceEvent;
use App\Tools\Models\ToolsMagic;
use App\Tools\Models\ToolsMagicData;
use Dux\App;
use Dux\Event\Attribute\Listener;

class SourceListener
{
    #[Listener(name: 'tools.magic.source')]
    public function data(SourceEvent $event): void
    {
        ToolsMagic::query()->get()->map(function ($item) use ($event) {
            $fields = [];
            foreach ($item->fields as $field) {
                if (!$field['search']) {
                    continue;
                }
                if ($field['type'] == 'editor') {
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
                    if (isset($keyword)) {
                        foreach ($fields as $key) {
                            $query->where("data->$key", 'like', '%' . $keyword . '%');
                        }
                    }
                    if ($item->type === 'tree') {
                        $data = $query->get()->toTree();
                    }else {
                        if (isset($ids)) {
                            $query->whereIn('id', $ids);
                        }
                        $data = $query->get();
                    }
                    return $this->formatData($data);
                });
        });
    }

    private function formatData($data) {
        return $data->map(function ($item) {
            $array = [
                'id' => $item->id,
                ...$item->data
            ];
            if ($item->children) {
                $array['parent_id'] = $item->parent_id;
                $array['children'] = $this->formatData($item->children);
            }
            return $array;
        })->toArray();
    }
}