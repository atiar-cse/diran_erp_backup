<?php

namespace App\Exports\Sales;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

// use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
// use PhpOffice\PhpSpreadsheet\Shared\Date;

class SalesReturnReportExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */

    use Exportable;

    private $data;

    public function __construct($data)
    {
        $this->data =$data;
    }

    public function collection()
    {
        $sales_return = $this->data;
        $dataFormat =[];

        foreach ($sales_return as $row){

            $theName =$row->product_name;

            $dataFormat[] =[
                 'Date'            =>dateConvertDBtoForm($row->sales_delivery_return_date),
                 'Return No'       =>$row->sales_delivery_return_no,
                 'Warehouse'       =>$row->purchase_ware_houses_name,
                 'Customer'        =>$row->sales_customer_name,
                 'Product'         =>" $theName",
                 'Narration'       =>$row->sales_delivery_return_details_remarks,
                 'Unit'            =>$row->sales_delivery_return_details_unit,
                 'Qty'             =>" $row->sales_delivery_return_details_qty",
                 'Price'           =>" $row->sales_delivery_return_details_unit_price",
                 'Total'           =>" $row->sales_delivery_return_details_total_price",

                ];
        }

		return collect($dataFormat);
    }

    public function headings(): array
    {
        return [
            'Dates',
            'Return No',
            'Warehouse',
            'Customer',
            'Product',
            'Narration',
            'Unit',
            'Qty',
            'Price',
            'Total',



        ];
    }    
}
