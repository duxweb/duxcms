<?php

namespace Dux\Utils;

use Dux\Utils\Excel\Sheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Psr\Http\Message\ResponseInterface;

class ExcelExport
{

    /**
     * @var Sheet[]
     */
    public array $sheet = [];

    public \PhpOffice\PhpSpreadsheet\Spreadsheet $excel;
    public function __construct()
    {
        $this->excel = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    }


    public function sheet(Sheet $sheet): static
    {
        $this->sheet[] = $sheet;
        return $this;
    }

    public function getObject(): \PhpOffice\PhpSpreadsheet\Spreadsheet
    {
        return $this->excel;
    }

    public function send(string $name, ResponseInterface $response)
    {
        foreach ($this->sheet as $k => $sheet) {
            $sheet->send($this->excel, $k);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($this->excel);

        $output = fopen('php://temp', 'w+');
        $writer->save($output);


        $stream = new \Slim\Psr7\Stream($output);

        fseek($output,0,SEEK_END);
        $size = ftell($output);

        return $response
            ->withHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->withHeader('Content-Disposition', 'attachment; filename='.rawurlencode($name . '-' . date('YmdHis')) . '.xlsx')
            ->withHeader('Content-Length', $size)
            ->withHeader('Cache-Control', 'max-age=0')
            ->withBody($stream);
    }

    public function getFile(): string
    {
        foreach ($this->sheet as $k => $sheet) {
            $sheet->send($this->excel, $k);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($this->excel);

        $output = tempnam(sys_get_temp_dir(), 'spreadsheet');
        $writer->save($output);
        return $output;
    }


}