<?php

namespace App\Exports\Inventory;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

// use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
// use PhpOffice\PhpSpreadsheet\Shared\Date;

class InventoryStockReportExport implements FromCollection, WithHeadings, ShouldAutoSize
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
        $stock_details_reports = $this->data;
        $dataFormat =[];

        foreach ($stock_details_reports as $row){
            $prdName =$row->product_name;

            $dataFormat[] =[
                'Warehouse'   =>$row->purchase_ware_houses_name,
                'Product'     => " $prdName",
                'Open qty'    =>" $row->inventory_stocks_open_qty",
                'Current Qty' =>" $row->inventory_stocks_current_qty" ,


            ];
        }

        return collect($dataFormat);
    }

    public function headings(): array
    {
        return [
            'Warehouse',
            'Product',
            'Open qty',
            'Current Qty',

        ];
    }    
}
