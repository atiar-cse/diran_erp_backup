<?php

namespace App\Exports\Purchase;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

// use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
// use PhpOffice\PhpSpreadsheet\Shared\Date;

class PurchaseReturnReportExport implements FromCollection, WithHeadings, ShouldAutoSize
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
        $purchases_return = $this->data;
        $dataFormat =[];

        foreach ($purchases_return as $row){

            $theName =$row->product_name;

            $dataFormat[] =[
                'Date'            =>dateConvertDBtoForm($row->po_return_date),
                'Return No'        =>$row->po_return_no,
                'Warehouse'       =>$row->purchase_ware_houses_name,
                'Supplier'        =>$row->purchase_supplier_name,
                'Product'         =>" $theName",
                'Narration'       =>$row->po_rtnd_remarks,
                'Unit'            =>$row->po_rtnd_messure_unit,
                'Qty'             =>" $row->po_rtnd_qty",
                'Price'           =>" $row->po_rtnd_unit_price",
                'Total'           =>" $row->po_rtnd_total_price",
            ];
        }

        return collect($dataFormat);
    }


    public function headings(): array
    {
        return [
            'Date',
            'Return No',
            'Warehouse',
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
