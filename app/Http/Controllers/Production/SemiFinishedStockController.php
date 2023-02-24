<?php

namespace App\Http\Controllers\Production;

use App\Model\Production\ProductionCurrentStock;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

use App\Model\Production\ProductionTesting;
use App\Model\Production\ProductionTestingDetails;

use App\Model\Production\ProductionFinishedStock;
use App\Model\Production\ProductionFinishedStockDetails;

use App\Model\Production\ProductionAssembling;
use App\Model\Production\ProductionAssemblingDetails;

class SemiFinishedStockController extends Controller
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
                                            ->where('approve_status',1)
                                            ->orderBy('testing_date','asc')
                                            ->pluck('id');
            $openingProductsQty = [];
            if ( count($openingShappingIDs) >0 ) {
                $openingProductsQty = ProductionTestingDetails::whereIn('testing_section_id',$openingShappingIDs)
                                            ->groupBy('product_id')
                                            ->pluck('current_qty','product_id');
            }

            $productionTestingDetails = ProductionTestingDetails::with('product')
                                ->whereIn('testing_section_id',$openingShappingIDs)
                                ->selectRaw('*, "0" as opening_qty,
                                                sum(trans_to_finishing) as prod_of_month,
                                                "" as total,
                                                sum(trans_to_finishing) as trans_to_finishing,
                                                "" as total_issue, "" as balance, "" as trans_to_store_qty, "" as reject_qty')
                                ->groupBy('product_id')
                                ->orderBy('id','desc')
                                ->get();

            //Transfer to Strore Qty
            $finishedStockIDs = ProductionFinishedStock::whereBetween('date', [$from_date, $to_date])
                                            ->where('approve_status',1)
                                            ->orderBy('date','asc')
                                            ->pluck('id');

                    $finished_rows = ProductionFinishedStockDetails::with('product')->whereIn('finished_stock_section_id',$finishedStockIDs)
                                        ->selectRaw('product_id,trans_to_store_qty,reject_qty,remarks,current_qty,receive_qty,adj_qty,remain_qty')
                                        ->groupBy('product_id')
                                        ->get();
                    $finishedItems = $finished_rows->pluck('product_id','trans_to_store_qty');
                    $rejectedItems = $finished_rows->pluck('reject_qty','product_id');

            $assemblingIDs = ProductionAssembling::whereBetween('assembling_date', [$from_date, $to_date])
                                            ->where('approve_status',1)
                                            ->pluck('id');
                    $assembledItems = ProductionAssemblingDetails::whereIn('assembling_section_id',$assemblingIDs)
                                        ->selectRaw('product_id, sum(total_used_qty) as used_qty')
                                        ->groupBy('product_id')
                                        ->pluck('used_qty','product_id');

            $data = [
                //'opening_products_qty' => $openingProductsQty,
                //'prod_testing_details' => $productionTestingDetails,
                'finished_items' => $finished_rows,//$finishedItems,
                //'assembled_items' => $assembledItems,
                //'rejected_items' => $rejectedItems,
            ];

            return $data;
        }
        return view('production.production_section.semi_finished_stock');
    }
}
