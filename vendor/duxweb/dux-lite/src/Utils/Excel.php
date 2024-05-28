<?php
declare(strict_types=1);

namespace Dux\Utils;

use Dux\Handlers\ExceptionBusiness;
use GuzzleHttp\Exception\GuzzleException;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Exception;
use Psr\Http\Message\ResponseInterface;

class Excel
{

    /**
     * 导入表格
     * @param string $path
     * @param int $start
     * @return array|null
     * @throws Exception
     * @throws GuzzleException
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public static function import(string $path, int $start = 1): ?array
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $extArr = ['xlsx', 'xls', 'csv'];
        if (!in_array($ext, $extArr)) {
            throw new ExceptionBusiness("File type error");
        }

        $local = !str_contains($path, 'http');

        if (!$local) {
            $client = new \GuzzleHttp\Client();
            $fileTmp = $client->request('GET', $path)->getBody()->getContents();
            $tmpFile = tempnam(sys_get_temp_dir(), 'excel_');
            $tmp = fopen($tmpFile, 'w');
            fwrite($tmp, $fileTmp);
            fclose($tmp);
        } else {
            $tmpFile = $path;
        }

        try {
            $objRead = \PhpOffice\PhpSpreadsheet\IOFactory::createReader(ucfirst($ext));
            $objRead->setReadDataOnly(true);
            $obj = $objRead->load($tmpFile);
            $currSheet = $obj->getSheet(0);
            $columnH = $currSheet->getHighestColumn();
            $columnCnt = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($columnH);
            $rowCnt = $currSheet->getHighestRow();
            $data = [];
            for ($_row = $start; $_row <= $rowCnt; $_row++) {
                $isNull = true;
                for ($_column = 1; $_column <= $columnCnt; $_column++) {
                    $cellName = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($_column);
                    $cellId = $cellName . $_row;
                    $data[$_row][$cellName] = trim($currSheet->getCell($cellId)->getFormattedValue());
                    if (!empty($data[$_row][$cellName])) {
                        $isNull = false;
                    }
                }
                if ($isNull) {
                    unset($data[$_row]);
                }
            }
            $table = [];
            foreach ($data as $vo) {
                $table[] = array_values($vo);
            }
            return $table;
        } catch (Exception $e) {
            throw $e;
        } finally {
            if (!$local) {
                unlink($tmpFile);
            }
        }
    }

    /**
     * 表格导出
     * @param string $title
     * @param string $subtitle
     * @param array $labels
     * @param array $data
     */
    public static function export(string $title, string $subtitle, array $labels, array $data, ResponseInterface $response): ResponseInterface
    {
        // 获取列数据
        if (!is_array($labels[0])) {
            $labels[] = $labels;
        }
        $lengths = array_map('count', $labels);
        $longestIndex = array_search(max($lengths), $lengths);
        $mainLabel = $labels[$longestIndex];
        $count = count($mainLabel);

        // 设置表格
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $worksheet = $spreadsheet->getSheet(0);
        //标题
        $worksheet->setCellValue([1, 1], $title)->mergeCells([1, 1, $count, 1]);
        $styleCenter = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
            'font' => [
                'size' => 16,
            ],
        ];
        $worksheet->getStyle([1, 1])->applyFromArray($styleCenter);

        // 副标题
        $worksheet->setCellValue([1, 2], $subtitle)->mergeCells([1, 2, $count, 2]);
        $worksheet->getStyle([1, 2])->applyFromArray([
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        // 单元格宽度
        foreach ($mainLabel as $key => $vo) {
            $worksheet->getColumnDimensionByColumn($key + 1)->setWidth($vo['width']);
        }

        // 单元格样式
        $styleArray = [
            'borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'font' => [
                'size' => 12,
            ],
        ];


        $headRow = 3;
        foreach ($labels as $label) {
            $fromCol = 1;
            foreach ($label as $vo) {
                $toCol = $fromCol;
                $col = $worksheet->setCellValueExplicit([$fromCol, $headRow], $vo['name'], DataType::TYPE_STRING);

                if ($vo['merge']) {
                    $toCol = $fromCol + $vo['merge'] - 1;
                    $col->mergeCells([$fromCol, $headRow, $toCol, $headRow]);
                }

                $gridStyle = $styleArray;

                if ($vo['align'] == 'left') {
                    $gridStyle['alignment'] = [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                    ];
                }
                if ($vo['align'] == 'center') {
                    $gridStyle['alignment'] = [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ];
                }
                if ($vo['align'] == 'right') {
                    $gridStyle['alignment'] = [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
                    ];
                }

                $worksheet->getStyle([$fromCol, $headRow, $toCol, $headRow])->applyFromArray($gridStyle);

                if ($vo['merge']) {
                    $fromCol = $toCol;
                }

                $fromCol++;
            }
            $headRow++;
        }

        foreach ($data as $list) {
            foreach ($list as $k => $vo) {
                if (is_array($vo)) {
                    $callback = $vo['callback'];
                    $content = $vo['content'];
                    $align = $vo['align'];
                } else {
                    $content = $vo;
                    $callback = '';
                    $align = '';
                }
                $worksheet->setCellValueExplicit([$k + 1, $headRow], $content, DataType::TYPE_STRING);

                $gridStyle = $styleArray;
                if ($align == 'left') {
                    $gridStyle['alignment'] = [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                    ];
                }
                if ($align == 'center') {
                    $gridStyle['alignment'] = [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ];
                }
                if ($align == 'right') {
                    $gridStyle['alignment'] = [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
                    ];
                }

                $item = $worksheet->getStyle([$k + 1, $headRow])->applyFromArray($gridStyle);
                if (is_callable($callback)) {
                    $callback($item, $worksheet);
                }
            }
            $headRow++;
        }

        unset($worksheet);

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        header('Content-Type:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition:attachment; filename=' . rawurlencode($title . '-' . date('YmdHis')) . '.xlsx');
        header('Cache-Control:max-age=0');

        $output = fopen('php://output', 'w');
        $writer->save($output);
        return $response;
    }

}
