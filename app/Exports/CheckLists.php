<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithStyles;
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
        $column = 1;
        $date = $this->linkedData->first()->chqdat;
        $reformatDate = substr($date,6,2)."/".substr($date,4,2)."/".substr($date,0,4);
        foreach($this->linkedData as $data){
            $totalAmount = 0;
            $totalVat= 0;
            $totalNet= 0;
            $sheet->setCellValue("A$column","ID : ".$this->ebill['taxid'][$data->id]);
            $sheet->setCellValue("B$column",$data->chqnum);
            $sheet->setCellValue("C$column",$reformatDate);
            $sheet->setCellValue("D$column",$this->ebill['name'][$data->id]);
            $sheet->setCellValue("E$column",$this->ebill['addr1'][$data->id]);
            $sheet->setCellValue("F$column",$this->ebill['addr2'][$data->id]);
            $sheet->setCellValue("G$column",$this->ebill['addr3'][$data->id]);
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
                $sheet->setCellValue("F$column",$aprcpit->aptrn->netamt);
                $column += 1;
            }
            $column += 1;
        }
    }
}