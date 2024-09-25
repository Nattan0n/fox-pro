<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CheckLists implements WithStyles
{
    /**
    *@return \Illuminate\Support\Collection
    */
    protected $linkedData;
    protected $ebill;

    public function __construct($linkedData,$ebill)
    {
        $this->linkedData= $linkedData;
        $this->ebill = $ebill;
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->setCellValue("A1","เลขที่เช็ค");
        $sheet->setCellValue("B1","ชื่อบริษัท");
        $sheet->setCellValue("C1","วันที่บิล");
        $sheet->setCellValue("D1","วันที่จ่าย");
        $sheet->setCellValue("E1","ที่อยู่");
        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(40);
        $sheet->getColumnDimension('C')->setWidth(13);
        $sheet->getColumnDimension('D')->setWidth(13);
        $sheet->getColumnDimension('E')->setWidth(13);
        $sheet->getColumnDimension('F')->setWidth(13);
        $column = 2;
        $date = $this->linkedData->first()->chqdat;
        $reformatDate = substr($date,6,2)."/".substr($date,4,2)."/".substr($date,0,4);
        foreach($this->linkedData as $data){
            $totalAmount = 0;
            $totalVat= 0;
            $totalNet= 0;
            $sheet->setCellValue("A$column",$data->chqnum);
            $sheet->setCellValue("D$column",$reformatDate);
            $sheet->setCellValue("B$column",$this->ebill['name'][$data->id]);
            $sheet->getStyle("B$column")->getAlignment()->setWrapText(true);
            $sheet->setCellValue("E$column",$this->ebill['addr1'][$data->id] . $this->ebill['addr2'][$data->id] . $this->ebill['addr3'][$data->id]);
            $column += 1;
            foreach($data->aprcpit as $aprcpit){
                $totalAmount += $aprcpit->aptrn->amount ?? 0;
                $totalVat += $aprcpit->aptrn->vatamt ?? 0;
                $totalNet += $aprcpit->aptrn->netamt ?? 0; 
                 $dateinv = substr($aprcpit->aptrn->duedat, 6, 2) . "/" . substr($aprcpit->aptrn->duedat, 4, 2) . "/" . substr($aprcpit->aptrn->duedat, 0, 4); 
                $sheet->setCellValue("B$column","INV".$aprcpit->aptrn->refnum);
                $sheet->setCellValue("C$column",$dateinv);
                $sheet->setCellValue("D$column",$aprcpit->aptrn->amount);
                $sheet->setCellValue("E$column",$aprcpit->aptrn->vatamt);
                $column += 1;
            }
            $sheet->setCellValue("D$column",$totalAmount);
            $sheet->setCellValue("E$column",$totalVat);
            $sheet->setCellValue("F$column",$totalNet);
            $sheet->setCellValue("G$column",$totalNet - $data->amount ?? 0  );
            $sheet->setCellValue("H$column",$data->amount);
            $sheet->getStyle("A$column:H$column")->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);

            $column += 2;
        }
          $sheet->getStyle('D3:H'.($column))->getNumberFormat()->setFormatCode('#,##0.00');
    }
}