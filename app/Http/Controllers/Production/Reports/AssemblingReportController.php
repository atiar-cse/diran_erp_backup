<?php

namespace App\Http\Controllers\Production\Reports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Model\Production\ProductionAssembling;
use App\Model\Production\ProductionAssemblingDetails;

class AssemblingReportController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $from_date = date('Y-m-01');
            $to_date = date('Y-m-d');

            if ($request->filled('from_date')) {
                $from_date = $request->from_date;
            }
            if ($request->filled('to_date')) {
                $to_date = $request->to_date;
            }

			$finishingProducts = ProductionAssembling::with('finish_product')
											->whereBetween('assembling_date', [$from_date, $to_date])
		  									->where('approve_status',1)
		  									->groupBy('finishing_product_id')
		  									->selectRaw('id, finishing_product_id, sum(trans_to_packing_qty) as total_trans_to_store_qty')
		  									->get();
		  	$data = [];
		  	foreach ($finishingProducts as $row) {
				$assemblingIDs = ProductionAssembling::whereBetween('assembling_date', [$from_date, $to_date])
			  									->where('finishing_product_id',$row->finishing_product_id)
			  									->where('approve_status',1)
			  									->pluck('id');
			  	$usedItems = ProductionAssemblingDetails::with('product')
			  							->whereIn('assembling_section_id',$assemblingIDs)
			  							->groupBy('product_id')
			  							->selectRaw('id, product_id, count_qty, sum(semi_finished_use_qty) as total_semi_finished_use_qty, sum(inventory_use_qty) as total_inventory_use_qty')
			  							->get();
			  	$data[] = array(
			  					'finishing_product_name' => $row->finish_product->product_name,
			  					'total_trans_to_store_qty' => $row->total_trans_to_store_qty,
			  					'rowspan' => $usedItems->count(),
			  					'used_product_list' => $usedItems,
			  				);
		  	}
		  	return $data;
        }

        return view('production.production_report.assembling_report');
    }
}
