<?php

namespace Dux\Utils\Excel;

class HeaderGroupItem
{
    public array $data = [];
    public function __construct(string $key)
    {
        $this->data['key'] = $key;
    }

    public function title(string $value): static
    {
        $this->data['title'] = $value;
        return $this;
    }

    public function align(AlignEnum $value): static
    {
        $this->data['align'] = $value->value;
        return $this;
    }

    public function getColNum(array $header): int
    {
        $tmp = 0;
        foreach ($header as $vo) {
            if (!$vo->data['group']) {
                continue;
            }

            $group = $vo->data['group'];
            if (!is_array($group)) {
                $group = [$group];
            }

            if (!in_array($this->data['key'], $group)) {
                continue;
            }
            $tmp++;
        }
        return $tmp ?: 1;
    }

    public function getStyle(): array
    {
        return [
            'alignment' => [
                'horizontal' => $this->data['align'],
            ]
        ];
    }
}