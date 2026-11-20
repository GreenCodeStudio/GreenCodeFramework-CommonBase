<?php

namespace CommonBase\UniversalExporter;
use mikehaertl\wkhtmlto\Pdf;

class UniversalExporter
{
    public function __construct(private $typeName)
    {
    }
    public function export($type, $rows, callable $rowCallback): array
    {
        if ($type == 'xml') {
            $xml = simplexml_load_string('<root/>');
            $xml->addChild('data');
            foreach ($rows as $i=>$row) {
                $rowXml = $xml->data->addChild($this->typeName);
                $rowData=$rowCallback($row);
                foreach ($rowData as $j=>$cell) {
                    $rowXml->addChild($cell->name, $cell->value);
                }
            }
            return ['mime' => 'application/xml', 'data' => $xml->asXML()];
        }else if($type=='pdf'){
            $pdf = new Pdf();
            $pdf->addPage('aaaa');
//            return ['mime' => 'application/pdf', 'data' => $pdf->toString()];
            return ['mime' => 'text/plain', 'data' => $pdf->toString()];
        }else if ($type == 'xlsx'){
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            if(!empty($rows)){
                $rowData=$rowCallback($rows[0]);
                foreach ($rowData as $j=>$cell) {
                    $sheet->setCellValue([$j+1, 1], $cell->title);
                }
            }
            foreach ($rows as $i=>$row) {
                $rowData=$rowCallback($row);
                foreach ($rowData as $j=>$cell) {
                    $sheet->setCellValue([$j+1, $i+2], $cell->value);
                }
            }
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            ob_start();
            $writer->save('php://output');
            $data = ob_get_clean();
            return ['mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'data' => $data];
        }
    }
}
