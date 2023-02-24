<?php

namespace App\Exports\Sales;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

// use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
// use PhpOffice\PhpSpreadsheet\Shared\Date;

class SalesSummaryReportExport implements FromCollection, WithHeadings, ShouldAutoSize
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
        $sales = $this->data;
        $dataFormat =[];

        foreach ($sales as $row){

            $theName =$row->product_name;

            $dataFormat[] =[
                 'Date'            =>dateConvertDBtoForm($row->sales_delivery_date),
                 'Customer'        =>$row->sales_customer_name,
                 'Product'         =>" $theName",
                 'Narration'       =>$row->sales_delivery_details_description,
                 'Unit'            =>$row->sales_delivery_details_unit,
                 'Qty'             =>" $row->sales_delivery_details_qty",
                 'Price'           =>" $row->sales_delivery_details_unit_price",
                 'Total'           =>" $row->sales_delivery_details_total_price",

                ];
        }

		return collect($dataFormat);
    }

    public function headings(): array
    {
        return [
            'Dates',
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
