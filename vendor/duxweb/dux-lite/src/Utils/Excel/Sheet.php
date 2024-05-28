<?php

namespace Dux\Utils\Excel;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use \PhpOffice\PhpSpreadsheet\Spreadsheet;

class Sheet
{
    public function __construct()
    {
    }

    private string $title = "";
    private array $subtitle = [];

    /**
     * @var HeaderItem[]
     */
    private array $header = [];

    /**
     * @var HeaderGroupItem[][]
     */
    private array $headerGroup = [];
    private array $data = [];

    private string $remark = "";

    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function setSubtitle(string $label, string $value): static
    {
        $this->subtitle[] = [
            'label' => $label,
            'value' => $value
        ];
        return $this;
    }

    public function setHeader(HeaderItem ...$header): static
    {
        $this->header = $header;
        return $this;
    }

    public function setHeaderGroup(array ...$header): static
    {
        $this->headerGroup[] = $header;
        return $this;
    }

    public function setRemark(string $content): static
    {
        $this->remark = $content;
        return $this;
    }

    public function setData(array $data): static
    {
        /*$data = [
            [
                'id' => 1,
            ]
        ];*/
        $this->data = $data;
        return $this;
    }

    private Worksheet $worksheet;

    public function send(Spreadsheet $excel, int $sheet): void
    {
        $this->worksheet = $excel->getSheet($sheet);

        // 解析头部信息
        $columnNum = count($this->header);

        $rowIndex = 1;

        // 设置主标题
        $this->sendTitle($rowIndex, $columnNum);

        // 设置副标题
        $this->sendSubtitle($rowIndex, $columnNum);

        // 设置头组
        $this->sendHeaderGroup($rowIndex);
        $this->sendHeader($rowIndex);

        // 设置数据
        $this->sendData($rowIndex);

        // 设置备注
        $this->sendRemark($rowIndex, $columnNum);

    }


    private function sendTitle(int &$rowIndex, int $columnNum): void
    {
        if (!$this->title) {
            return;
        }
        $this->worksheet->setCellValue([1, $rowIndex], $this->title)->mergeCells([1, $rowIndex, $columnNum, $rowIndex]);
        $styleCenter = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
            'font' => [
                'size' => 16,
            ],
        ];
        $this->worksheet->getStyle([$rowIndex, $rowIndex])->applyFromArray($styleCenter);
        $rowIndex++;
    }

    private function sendSubtitle(int &$rowIndex, int $columnNum): void
    {
        if (!$this->subtitle) {
            return;
        }

        $data = [];
        foreach ($this->subtitle as $vo) {
            $data[] = $vo['label'] . ': ' . $vo['value'];
        }
        $this->worksheet->setCellValue([1, $rowIndex], join("\t ", $data))->mergeCells([1, $rowIndex, $columnNum, $rowIndex]);
        $this->worksheet->getStyle([1, 2])->applyFromArray([
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ]);
        $rowIndex++;
    }

    private function sendHeaderGroup(int &$rowIndex): void
    {
        if (!$this->headerGroup) {
            return;
        }
        foreach ($this->headerGroup as $groups) {
            foreach ($groups as $group) {
                $fromCol = 1;
                foreach ($group as $header) {
                    $toCol = $fromCol;
                    // 获取合并数
                    $merge = $header->getColNum($this->header);
                    // 设置表格值
                    $col = $this->worksheet->setCellValueExplicit([$fromCol, $rowIndex], $header->data['title'], DataType::TYPE_STRING);
                    // 合并列
                    if ($merge > 1) {
                        $toCol = $fromCol + $merge - 1;
                    }
                    $col->mergeCells([$fromCol, $rowIndex, $toCol, $rowIndex]);
                    // 设置样式
                    $this->worksheet->getStyle([$fromCol, $rowIndex, $toCol, $rowIndex])->applyFromArray([...$this->getGridStyle(), ...$header->getStyle()]);

                    if ($merge > 1) {
                        $fromCol = $toCol;
                    }
                    $fromCol++;
                }
            }
            $rowIndex++;
        }
    }

    private function sendHeader(int &$rowIndex): void
    {
        if (!$this->header) {
            return;
        }
        $fromCol = 1;
        foreach ($this->header as $header) {
            // 设置表格值
            $this->worksheet->setCellValueExplicit([$fromCol, $rowIndex], $header->data['title'], DataType::TYPE_STRING);
            $this->worksheet->getColumnDimensionByColumn($fromCol)->setWidth($header->data['width'] ?: 10);
            // 设置样式
            $this->worksheet->getStyle([$fromCol, $rowIndex, $fromCol, $rowIndex])->applyFromArray([...$this->getGridStyle(), ...$header->getStyle()]);
            $fromCol++;
        }
        $rowIndex++;
    }

    private function sendData(int &$rowIndex): void
    {
        if (!$this->data) {
            return;
        }
        foreach ($this->data as $data) {
            $index = 1;
            foreach ($this->header as $field) {
                if (is_callable($data[$field->data['name']])) {
                    $data[$field->data['name']]($this->worksheet, [$index, $rowIndex]);
                }else {
                    $this->worksheet->setCellValueExplicit([$index, $rowIndex], $data[$field->data['name']], DataType::TYPE_STRING);
                }
                // 设置样式
                $this->worksheet->getStyle([$index, $rowIndex, $index, $rowIndex])->applyFromArray([...$this->getGridStyle(), ...$field->getStyle()]);
                $index++;
            }
            $rowIndex++;
        }
    }


    private function sendRemark(int &$rowIndex, int $columnNum): void
    {
        if (!$this->remark) {
            return;
        }
        $this->worksheet->setCellValue([1, $rowIndex], $this->remark)->mergeCells([1, $rowIndex, $columnNum, $rowIndex]);
        $this->worksheet->getStyle([1, $rowIndex])->applyFromArray([
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ]);
        $rowIndex++;
    }

    private function getGridStyle(): array
    {
        return [
            'borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'font' => [
                'size' => 12,
            ],
        ];
    }



}