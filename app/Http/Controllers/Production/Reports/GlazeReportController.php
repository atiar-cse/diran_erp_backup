<?php

namespace App\Http\Controllers\Production\Reports;

use App\Model\Production\ProductionGlaze;
use App\Model\Production\ProductionGlazeDetails;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

class GlazeReportController extends Controller
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

            $openingGlazeIDs = ProductionGlaze::whereBetween('glaze_date', [$from_date, $to_date])
                ->orderBy('glaze_date','asc')
                ->pluck('id');

            $openingProductsQty = [];
            if ( count($openingGlazeIDs) >0 ) {
                $openingProductsQty = ProductionGlazeDetails::whereIn('glaze_section_id',$openingGlazeIDs)
                    ->groupBy('product_id')
                    ->pluck('current_qty','product_id');
            }

            $glazeDetails = ProductionGlazeDetails::with('product')
                ->whereIn('glaze_section_id',$openingGlazeIDs)
                ->selectRaw('*, "" as opening_qty, 
                                sum(receive_qty) as rec_f_dry, 
                                "" as total,
                                sum(trans_kiln_qty) as trans_kiln_qty, 
                                sum(waste_qty) as waste_qty, 
                                "" as total_issue, "" as balance')
                ->groupBy('product_id')
                ->orderBy('id','desc')
                ->get();

            $data = [
                'opening_products_qty' => $openingProductsQty,
                'prod_glaze_details' => $glazeDetails,
            ];

            return $data;
        }
        return view('production.production_report.glaze_report');
    }

    /*public function glazeFilterReport(Request $request){

        $return ='';
        if($request->ajax()){
            $where='';
            $from_date2 = $request->from_date;
            $end_date2  = $request->end_date;

            if($from_date2!=''){
                $from_date=date('Y-m-d', strtotime(str_replace('/', '-',$from_date2)));
            }else{$from_date='';}

            if($end_date2!=''){
                $end_date=date('Y-m-d', strtotime(str_replace('/', '-',$end_date2)));
            }else{$end_date='';}

            if($from_date!=''){$where.=" and p_glaze.glaze_date>='$from_date' ";}
            if($end_date!=''){$where.=" and p_glaze.glaze_date<='$end_date' ";}

            if($where!=''){

                $return = DB::table('production_glaze_details as dtails')
                    ->leftJoin('production_glazes as p_glaze', 'dtails.glaze_section_id','=','p_glaze.id')
                    ->leftJoin('production_products as pp', 'dtails.product_id','=','pp.id')
                    ->leftJoin('production_current_stocks as pc_stock', 'dtails.product_id','=','pc_stock.product_id')
                    ->select('dtails.*',
                        'p_glaze.glaze_date','p_glaze.narration',
                        'pp.product_name',
                        DB::raw('SUM(dtails.current_qty) as openQty'),
                        DB::raw('SUM(dtails.receive_qty) as rgq'),
                        DB::raw('SUM(dtails.current_qty + dtails.receive_qty) as total'),
                        DB::raw('SUM(dtails.trans_kiln_qty) as klnQty'),
                        DB::raw('SUM(dtails.waste_qty) as rjQty'),
                        DB::raw('SUM(dtails.trans_kiln_qty + dtails.waste_qty) as total_issus'),
                        DB::raw('SUM(dtails.current_qty + dtails.receive_qty)-SUM(dtails.trans_kiln_qty + dtails.waste_qty) as balance ')
                    )
                    ->groupBy('dtails.product_id')
                    ->orderBy('dtails.id','desc')
                    ->whereRaw('p_glaze.approve_status=1  '.$where)
                    ->get();
            }

            return $return;

        }
        return view('production.production_report.glaze_report');

    }*/

}
