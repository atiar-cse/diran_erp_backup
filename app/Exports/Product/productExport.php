<?php

namespace App\Exports\Product;

use App\Model\Production\ProductionProducts;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

// use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
// use PhpOffice\PhpSpreadsheet\Shared\Date;

class productExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */

    use Exportable;

    public function collection()
    {
        // return SalesCustomer::all();

    	$data = [];

    	$results = ProductionProducts::with('get_type','category','brand','measureunit','countryOfOrigin')->orderBy('id','ASC')->get();
    	
    	foreach ($results as $key => $row) {

    		$data[] = [
    					'SL.' => ++$key,
    					'Product Name' => $row->product_name,
    					'Description'  => $row->product_description,
    					'Product Type' => $row->get_type ? $row->get_type->product_type_name : '',
    					'Category'     => $row->category ? $row->category->product_category_name : '',
    					'Measure Unit' => $row->measureunit ? $row->measureunit->measure_unit : '',
    					'Buying Price' => $row->buying_price,
    					'Selling Price'=> $row->selling_price,
    					'Alert Qty'    => $row->alert_qty,
    					'Status'       => $row->status ==1 ? 'Active' : 'Inactive',
    				];
    	}

        return collect($data);
    }


    public function headings(): array
    {
        return [
            'SL.',
            'Product Name',
            'Description',
            'Product Type',
            'Category',
            'Measure Unit',
            'Buying Price',
            'Selling Price',
            'Alert Qty',
            'Status',
        ];
    }

}
