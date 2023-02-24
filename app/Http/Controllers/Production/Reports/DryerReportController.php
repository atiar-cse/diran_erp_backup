<?php

namespace App\Http\Controllers\Production\Reports;

use App\Model\Production\ProductionDryer;
use App\Model\Production\ProductionDryerDetails;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

class DryerReportController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {

            $from_date = date('Y-m-01');
            $to_date   = date('Y-m-d');

            if ($request->filled('from_date')) {
                $from_date = $request->from_date;
            }
            if ($request->filled('to_date')) {
                $to_date = $request->to_date;
            }

            $openingDryerIDs = ProductionDryer::whereBetween('dryer_date', [$from_date, $to_date])
                ->orderBy('dryer_date','asc')
                ->pluck('id');

            $openingProductsQty = [];
            if ( count($openingDryerIDs) >0 ) {
                $openingProductsQty = ProductionDryerDetails::whereIn('dryer_section_id',$openingDryerIDs)
                    ->groupBy('product_id')
                    ->pluck('current_qty','product_id');
            }

            $dryDetails = ProductionDryerDetails::with('product')
                ->whereIn('dryer_section_id',$openingDryerIDs)
                ->selectRaw('*, "" as opening_qty, 
                                sum(receive_qty) as prod_of_month, 
                                "" as total,
                                sum(trans_to_glaze_qty) as trans_to_glaze_qty, 
                                sum(glaze_westage_qty) as glaze_westage_qty, 
                                "" as total_issue, "" as balance')
                ->groupBy('product_id')
                ->orderBy('id','desc')
                ->get();

            $data = [
                'opening_products_qty' => $openingProductsQty,
                'prod_dryer_details' => $dryDetails,
            ];

            return $data;
        }
        return view('production.production_report.dryer_report');
    }

}

/*public function dryerFilterReport(Request $request){

        $return ='';

        if($request->ajax()){

            $where ='';
            $from_date2 = $request->from_date;
            $end_date2  = $request->end_date;

            if($from_date2!=''){
                $from_date=date('Y-m-d', strtotime(str_replace('/', '-',$from_date2)));
            }else{$from_date='';}

            if($end_date2!=''){
                $end_date=date('Y-m-d', strtotime(str_replace('/', '-',$end_date2)));
            }else{$end_date='';}

            if($from_date!=''){$where.=" and p_dryers.dryer_date>='$from_date' ";}
            if($end_date!=''){$where.=" and p_dryers.dryer_date<='$end_date' ";}

            if($where!='') {

                $return = DB::table('production_dryer_details as dtails')
                    ->leftJoin('production_dryers as p_dryers', 'dtails.dryer_section_id','=','p_dryers.id')
                    ->leftJoin('production_products as pp', 'dtails.product_id','=','pp.id')
                    ->leftJoin('production_current_stocks as pc_stock', 'dtails.product_id','=','pc_stock.product_id')
                    ->select('dtails.*',
                        'p_dryers.dryer_date','p_dryers.narration',
                        'pp.product_name',
                        DB::raw('SUM(dtails.current_qty) as openQty'),
                        DB::raw('SUM(dtails.receive_qty) as rec_from_for'),
                        DB::raw('SUM(dtails.current_qty + dtails.receive_qty) as total'),
                        DB::raw('SUM(dtails.trans_to_glaze_qty) as tra_to_sf'),
                        DB::raw('SUM(dtails.current_qty + dtails.receive_qty)-SUM(dtails.trans_to_glaze_qty) as balance ')
                    )
                    ->groupBy('dtails.product_id')
                    ->orderBy('dtails.id','desc')
                    ->whereRaw('p_dryers.approve_status=1  '.$where)
                    ->get();
            }
            return $return;
          }
         return view('production.production_report.dryer_report');
    }*/
