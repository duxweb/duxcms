<?php

namespace Dux\Utils\Excel;

class HeaderItem
{
    public array $data = [];
    public function __construct(string $name)
    {
        $this->data['name'] = $name;
    }

    public function title(string $value): static
    {
        $this->data['title'] = $value;
        return $this;
    }

    public function width(string $value): static
    {
        $this->data['width'] = $value;
        return $this;
    }

    public function align(AlignEnum $value): static
    {
        $this->data['align'] = $value->value;
        return $this;
    }

    public function group(string $value): static
    {
        $this->data['group'] = $value;
        return $this;
    }

    public function getStyle(): array
    {
        return [
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => $this->data['align'],
                'wrapText' => true,
            ]
        ];
    }
}