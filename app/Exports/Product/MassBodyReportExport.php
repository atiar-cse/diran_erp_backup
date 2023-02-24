<?php

namespace App\Exports\Product;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

// use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
// use PhpOffice\PhpSpreadsheet\Shared\Date;

class MassBodyReportExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */

    use Exportable;

    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        // return collect($this->data);

        $massbodies = $this->data;

        $dataFormat = [];


        $total = 0;

        foreach ($massbodies as $key => $row) {

            $consumption =$row->consumption;

            $dataFormat[] = [
                'S/L'              => ++$key,
                'NUMBER OF MATERIALS'=> $row->product_name,
                'CONSUMPTION'      => " $consumption",
                'REMARKS'          => $row->remarks,

            ];

            $total = $total + $row->consumption;

        }

        $dataFormat[] = [
            'S/L'                => '',
            'NUMBER OF MATERIALS'=> 'Total',
            'CONSUMPTION'        => number_format($total,2),
            'REMARKS'            => '',

        ];

        return collect($dataFormat);

    }

    public function headings(): array
    {
        return [
            'S/L',
            'NUMBER OF MATERIALS',
            'CONSUMPTION',
            'REMARKS',

        ];
    }    
}
