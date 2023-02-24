<?php

namespace App\Http\Controllers\Production\Reports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Model\Production\ProductionAssembling;
use App\Model\Production\ProductionAssemblingDetails;

class SemifinishedController extends Controller
{


    public function index(Request $request)
    {
        /*
        if ($request->ajax()) {

            $from_date = date('Y-m-01');
            $to_date = date('Y-m-d');

            if ($request->filled('from_date')) {
                $from_date = $request->from_date;
            }
            if ($request->filled('to_date')) {
                $to_date = $request->to_date;
            }

			$openingShappingIDs = ProductionShapping::whereBetween('shapping_date', [$from_date, $to_date])
		  									->orderBy('shapping_date','asc')
		  									->pluck('id');
		  	$openingProductsQty = [];
		  	if ( count($openingShappingIDs) >0 ) {
		  		$openingProductsQty = ProductionShappingDetails::whereIn('shapping_section_id',$openingShappingIDs)
		  									->groupBy('product_id')
		  									->pluck('current_qty','product_id');
		  	}

            $shappingDetails = ProductionShappingDetails::with('product')
            					->whereIn('shapping_section_id',$openingShappingIDs)
            					->selectRaw('*, "" as opening_qty, 
            									sum(receive_qty) as prod_of_month, 
            									"" as total,
            									sum(trans_to_dry_qty) as trans_to_dry_qty, 
            									sum(dry_westage_qty) as dry_westage_qty, 
            									"" as total_issue, "" as balance')
            					->groupBy('product_id')
            					->orderBy('id','desc')
            					->get();

            $data = [
                'opening_products_qty' => $openingProductsQty,
                'prod_shapping_details' => $shappingDetails,
            ];

            return $data;
        }

        return view('production.production_report.semi_finished_report');
        */
    }

}
