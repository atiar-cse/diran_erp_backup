<?php

namespace App\Http\Controllers\Production\Reports;

use App\Model\Production\ProductionTesting;
use App\Model\Production\ProductionTestingDetails;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

class TestingReportController extends Controller
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

            $openingShappingIDs = ProductionTesting::whereBetween('testing_date', [$from_date, $to_date])
                                            ->orderBy('id','asc')
                                            ->pluck('id');
            $openingProductsQty = [];
            if ( count($openingShappingIDs) >0 ) {
                $openingProductsQty = ProductionTestingDetails::whereIn('testing_section_id',$openingShappingIDs)
                                            ->groupBy('product_id')
                                            ->pluck('current_qty','product_id');
            }

            $shappingDetails = ProductionTestingDetails::with('product')
                                ->whereIn('testing_section_id',$openingShappingIDs)
                                ->selectRaw('*, "" as opening_qty,
                                                sum(receive_qty) as rec_f_kiln,
                                                sum(adj_qty) as adj_qty,
                                                "" as total,
                                                sum(reject_qty) as reject_qty,
                                                sum(trans_to_finishing) as trans_to_finishing,
                                                "" as total_issue, "" as balance')
                                ->groupBy('product_id')
                                ->orderBy('id','desc')
                                ->get();

            $data = [
                'opening_products_qty' => $openingProductsQty,
                'prod_testing_details' => $shappingDetails,
            ];

            return $data;
        }

        return view('production.production_report.testing_hv_report');
    }

}
