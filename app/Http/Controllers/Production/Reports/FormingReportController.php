<?php

namespace App\Http\Controllers\Production\Reports;

use App\Model\Production\ProductionForming;
use App\Model\Production\ProductionFormingDetails;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\CompanyInfo;
use DB;

class FormingReportController extends Controller
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

            $openingFormingIDs = ProductionForming::whereBetween('forming_date', [$from_date, $to_date])
                ->orderBy('forming_date','asc')
                ->pluck('id');

            $openingProductsQty = [];
            if ( count($openingFormingIDs) >0 ) {
                $openingProductsQty = ProductionFormingDetails::whereIn('forming_section_id',$openingFormingIDs)
                    ->groupBy('product_id')
                    ->pluck('current_qty','product_id');
            }

            $formingDetails = ProductionFormingDetails::with('product')
                ->whereIn('forming_section_id',$openingFormingIDs)
                ->selectRaw('*, "" as opening_qty, 
                                sum(receive_qty) as prod_of_month, 
                                "" as total,
                                sum(trans_to_shapping_qty) as trans_to_shapping_qty, 
                                sum(shapping_westage_qty) as shapping_westage_qty, 
                                "" as total_issue, "" as balance')
                ->groupBy('product_id')
                ->orderBy('id','desc')
                ->get();

            $data = [
                'opening_products_qty' => $openingProductsQty,
                'prod_forming_details' => $formingDetails,
            ];

            return $data;
        }
        return view('production.production_report.forming_report');
    }




    /*public function formingFilterReport(Request $request){

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

            if($from_date!=''){$where.=" and p_forming.forming_date>='$from_date' ";}
            if($end_date!=''){$where.=" and p_forming.forming_date<='$end_date' ";}

            if($where!=''){

                $return = DB::table('production_forming_details as dtails')
                    ->leftJoin('production_formings as p_forming', 'dtails.forming_section_id','=','p_forming.id')
                    ->leftJoin('production_products as pp', 'dtails.product_id','=','pp.id')
                    ->leftJoin('production_current_stocks as pc_stock', 'dtails.product_id','=','pc_stock.product_id')
                    ->select('dtails.*',
                        'p_forming.forming_date','p_forming.narration',
                        'pp.product_name',
                        DB::raw('SUM(dtails.current_qty) as openQty'),
                        DB::raw('SUM(dtails.receive_qty) as rfg'),
                        DB::raw('SUM(dtails.current_qty + dtails.receive_qty) as total'),
                        DB::raw('SUM(dtails.trans_to_shapping_qty) as spQty'),
                        DB::raw('SUM(dtails.forming_westage_qty) as rjQty'),
                        DB::raw('SUM(dtails.trans_to_shapping_qty + dtails.forming_westage_qty) as total_issus'),
                        DB::raw('SUM(dtails.current_qty + dtails.receive_qty)-SUM(dtails.trans_to_shapping_qty + dtails.forming_westage_qty) as balance ')
                    )
                    ->groupBy('dtails.product_id')
                    ->orderBy('dtails.id','desc')
                    ->whereRaw('p_forming.approve_status=1  '.$where)
                    ->get();
            }

            return $return;

        }
        return view('production.production_report.forming_report');
    }*/

}
