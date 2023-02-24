<?php

namespace App\Exports\Purchase;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

// use Maatwebsite\Excel\Concerns\FromQuery;
// use PhpOffice\PhpSpreadsheet\Shared\Date;

class PurchaseSummaryReportExport implements FromCollection, WithHeadings, ShouldAutoSize
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
        $purchases = $this->data;
        $dataFormat =[];

        foreach ($purchases as $row){
            $theName =$row->product_name;
            $dataFormat[] =[
                'Date'            =>dateConvertDBtoForm($row->po_receive_date),
                'Supplier'        =>$row->purchase_supplier_name,
                'Product'         =>" $theName",
                'Narration'       =>$row->po_narration,
                'Unit'            =>$row->pod_unit,
                'Qty'             =>" $row->pod_qty",
                'Price'           =>" $row->pod_unit_price",
                'Total'           =>" $row->pod_total_price",
            ];
        }

        return collect($dataFormat);
    }


    public function headings(): array
    {
        return [
            'Date',
            'Supplier',
            'Product',
            'Narration',
            'Unit',
            'Qty',
            'Price',
            'Total',

        ];
    }

}
