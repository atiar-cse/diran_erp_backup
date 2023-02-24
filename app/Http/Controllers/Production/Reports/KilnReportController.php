<?php

namespace App\Http\Controllers\Production\Reports;

use App\Model\Production\ProductionKiln;
use App\Model\Production\ProductionKilnDetails;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

class KilnReportController extends Controller
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

            $openingkilnIDs = ProductionKiln::whereBetween('kiln_date', [$from_date, $to_date])
                ->orderBy('kiln_date','asc')
                ->pluck('id');

            $openingProductsQty = [];
            if ( count($openingkilnIDs) >0 ) {
                $openingProductsQty = ProductionKilnDetails::whereIn('kiln_section_id',$openingkilnIDs)
                    ->groupBy('product_id')
                    ->pluck('current_qty','product_id');
            }

            $kilnDetails = ProductionKilnDetails::with('product')
                ->whereIn('kiln_section_id',$openingkilnIDs)
                ->selectRaw('*, "" as opening_qty, 
                                sum(receive_qty) as rec_f_gla, 
                                "" as total,
                                sum(trans_to_hv_qty) as trans_to_hv_qty, 
                                sum(reject_qty) as reject_qty, 
                                "" as total_issue, "" as balance')
                ->groupBy('product_id')
                ->orderBy('id','desc')
                ->get();

            $data = [
                'opening_products_qty' => $openingProductsQty,
                'prod_kiln_details' => $kilnDetails,
            ];

            return $data;
        }
        return view('production.production_report.kiln_report');
    }

   /* public function kilnFilterReport(Request $request){
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

            if($from_date!=''){$where.=" and p_kiln.kiln_date>='$from_date' ";}
            if($end_date!=''){$where.=" and p_kiln.kiln_date<='$end_date' ";}

            if($where!=''){

                $return = DB::table('production_kiln_details as dtails')
                    ->leftJoin('production_kilns as p_kiln', 'dtails.kiln_section_id','=','p_kiln.id')
                    ->leftJoin('production_products as pp', 'dtails.product_id','=','pp.id')
                    ->leftJoin('production_current_stocks as pc_stock', 'dtails.product_id','=','pc_stock.product_id')
                    ->select('dtails.*',
                        'p_kiln.kiln_date',
                        'pp.product_name',
                        DB::raw('SUM(dtails.current_qty) as openQty'),
                        DB::raw('SUM(dtails.receive_qty) as rfg'),
                        DB::raw('SUM(dtails.current_qty + dtails.receive_qty) as total'),
                        DB::raw('SUM(dtails.trans_to_hv_qty) as thvQty'),
                        DB::raw('SUM(dtails.reject_qty) as rjQty'),
                        DB::raw('SUM(dtails.trans_to_hv_qty + dtails.reject_qty) as total_issus'),
                        DB::raw('SUM(dtails.current_qty + dtails.receive_qty)-SUM(dtails.trans_to_hv_qty + dtails.reject_qty) as balance ')
                        )
                    ->groupBy('dtails.product_id')
                    ->orderBy('dtails.id','desc')
                    ->whereRaw('p_kiln.approve_status=1  '.$where)
                    ->get();
            }

            return $return;

        }
        return view('production.production_report.kiln_report');

      }*/


}
