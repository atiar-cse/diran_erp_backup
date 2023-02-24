<?php

namespace App\Http\Controllers\Production\Setup;

use App\Model\Production\ProductionProducts;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Production\ProductionRmRatioSetup;
use App\Model\Production\ProductionRecycleChip;
use Illuminate\Support\Facades\Auth;
use DB;
use Carbon\Carbon;

use App\Model\Accounts\AccountsChartofAccounts;
use App\Model\Accounts\AccountsJournalEntryDetails;
use App\Model\Accounts\AccountsSubCode2;

use App\Model\Production\ProductionForming;
use App\Model\Production\ProductionShapping;
use App\Model\Production\ProductionDryer;
use App\Model\Production\ProductionGlaze;

class RawMaterialRatioSetupController extends Controller
{

    public function index(Request $request)
    {
        if($request->ajax()) {
            $result  = ProductionRmRatioSetup::get();
            $recycleChip = ProductionRecycleChip::First();

            $data['rm_ratio_items'] = $result;
            $data['recyle_chip'] = $recycleChip;
            return $data;
        }

        $productList = ProductionProducts::where('status', '1')
                            ->where('is_raw_material_status', '1')
                            ->with('category')
                            ->get();

        /* @ Previous Month : Factory Expenses - COA Subcode Head (2107) */
        $overhead_info = [];
        $sub_code2 = AccountsSubCode2::where('sub_code2',2107)->First();

        if ($sub_code2) {

            $from_date = Carbon::now()->subMonth()->startOfMonth()->toDateString();
            $end_date = Carbon::now()->subMonth()->endOfMonth()->toDateString();

            $accountIDs = AccountsChartofAccounts::where('status',1)
                                ->where('sub_code2_id', $sub_code2->id)
                                ->pluck('id');

            $overhead_info = AccountsJournalEntryDetails::whereIn('char_of_account_id', $accountIDs)
                            ->whereHas('get_jrnl_entry', function ($query) use ($from_date, $end_date) {
                                $query->where('approve_status', 1);
                                $query->where('transaction_date', '>=', $from_date);
                                $query->where('transaction_date', '<=', $end_date);
                            })
                            ->selectRaw('sum(credit_amount) as total_cr, sum(debit_amount) as total_dr')
                            ->First();
        }

        /* @ Wastage Overhead Amount */
        $wastage_oh = $this->calcWastageOverhead();
        $reject = $this->calcRejectAmount();

        return view('production.production_setup.raw_material_ratio_setup',compact('productList','overhead_info','wastage_oh','reject'));
    }

    public function store(Request $request)
    {

        $this->validate($request, [
            'details.*.product_id' => 'required',
            'details.*.thousand_kgs_weight' => 'required',
            'glaze_material_price_per_kg' => 'required',
            'last_month_overhead_amt' => 'required',
            'last_month_production_kg' => 'required',
        ], [
            'details.*.product_id.required' => 'Required field.',
            'details.*.thousand_kgs_weight.required' => 'Required field.',
        ]);

        $dataFormat = [];
        foreach ($request->details as $value) {
            $dataFormat[] = [
                'product_id' => $value['product_id'],
                'thousand_kgs_weight' => $value['thousand_kgs_weight'],
                'one_kg_weight' => $value['one_kg_weight'],
                'created_by' => Auth::user()->id,
            ];
        }

        try {
            DB::beginTransaction();

            ProductionRmRatioSetup::truncate();
            ProductionRmRatioSetup::insert($dataFormat);

            $recycleChip = ProductionRecycleChip::First();
            if ($recycleChip) {
                $recycleChip->recycle_chip_current_weight = $request->recycle_chip_current_weight;
                $recycleChip->recycle_chip_unit_price = $request->recycle_chip_unit_price;
                $recycleChip->recycle_chip_total_amt = $request->recycle_chip_total_amt;

                $recycleChip->glaze_material_price_per_kg = $request->glaze_material_price_per_kg;

                $recycleChip->last_month_overhead_amt  = $request->last_month_overhead_amt;
                $recycleChip->last_month_production_kg = $request->last_month_production_kg;
                $recycleChip->overhead_per_kg          = $request->overhead_per_kg;

                $recycleChip->updated_by = Auth::user()->id;
                $recycleChip->save();
            } else {
                ProductionRecycleChip::insert([
                                            'recycle_chip_current_weight' => $request->recycle_chip_current_weight, 
                                            'recycle_chip_unit_price' => $request->recycle_chip_unit_price, 
                                            'recycle_chip_total_amt' => $request->recycle_chip_total_amt, 
                                            
                                            'glaze_material_price_per_kg' => $request->glaze_material_price_per_kg, 
                                            
                                            'last_month_overhead_amt' => $request->last_month_overhead_amt, 
                                            'last_month_production_kg' => $request->last_month_production_kg, 
                                            'overhead_per_kg' => $request->overhead_per_kg, 
                                            'created_by' => Auth::user()->id,
                                        ]);
            }

            DB::commit();
            $bug = 0;
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Raw Material Ratio Setup Successfully Inserted !']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }

    }

    public function calcWastageOverhead()
    {
        $wastage_oh = [];

        $from_date = Carbon::now()->subMonth()->startOfMonth()->toDateString();
        $end_date = Carbon::now()->subMonth()->endOfMonth()->toDateString();

        $wastage_oh['forming_prev'] = ProductionForming::where('approve_status', 1)
                                            ->whereBetween('forming_date', [$from_date, $end_date])
                                            ->selectRaw('sum(wastage_overhead_amt) as amount')
                                            ->First();
        $wastage_oh['forming_all'] = ProductionForming::where('approve_status', 1)
                                            ->selectRaw('sum(wastage_overhead_amt) as amount')
                                            ->First();
        
        $wastage_oh['shapping_prev'] = ProductionShapping::where('approve_status', 1)
                                            ->whereBetween('shapping_date', [$from_date, $end_date])
                                            ->selectRaw('sum(wastage_overhead_amt) as amount')
                                            ->First();
        $wastage_oh['shapping_all'] = ProductionShapping::where('approve_status', 1)
                                            ->selectRaw('sum(wastage_overhead_amt) as amount')
                                            ->First();
        
        $wastage_oh['dryer_prev'] = ProductionDryer::where('approve_status', 1)
                                            ->whereBetween('dryer_date', [$from_date, $end_date])
                                            ->selectRaw('sum(dryer_date) as amount')
                                            ->First();
        $wastage_oh['dryer_all'] = ProductionDryer::where('approve_status', 1)
                                            ->selectRaw('sum(wastage_overhead_amt) as amount')
                                            ->First();
        
        $wastage_oh['glaze_prev'] = ProductionGlaze::where('approve_status', 1)
                                            ->whereBetween('glaze_date', [$from_date, $end_date])
                                            ->selectRaw('sum(wastage_overhead_amt) as amount')
                                            ->First();
        $wastage_oh['glaze_all'] = ProductionGlaze::where('approve_status', 1)
                                            ->selectRaw('sum(wastage_overhead_amt) as amount')
                                            ->First();     
                                            
        return $wastage_oh;   
    }

    public function calcRejectAmount()
    {   

        $reject = [];

        $from_date = Carbon::now()->subMonth()->startOfMonth()->toDateString();
        $end_date = Carbon::now()->subMonth()->endOfMonth()->toDateString();

        $reject['kilns_prev'] = DB::table('production_kilns')
                                    ->where('approve_status',1)
                                    ->whereBetween('kiln_date', [$from_date, $end_date])
                                    ->selectRaw('sum(reject_amt) as reject_amt, sum(reject_overhead_amt) as reject_oh_amt')
                                    ->First();
        $reject['kilns_all'] = DB::table('production_kilns')  
                                    ->where('approve_status',1)                                  
                                    ->selectRaw('sum(reject_amt) as reject_amt, sum(reject_overhead_amt) as reject_oh_amt')
                                    ->First();

        $reject['testing_prev'] = DB::table('production_testings')
                                    ->where('approve_status',1)
                                    ->whereBetween('testing_date', [$from_date, $end_date])
                                    ->selectRaw('sum(reject_amt) as reject_amt, sum(reject_overhead_amt) as reject_oh_amt')
                                    ->First();
        $reject['testing_all'] = DB::table('production_testings')  
                                    ->where('approve_status',1)                                  
                                    ->selectRaw('sum(reject_amt) as reject_amt, sum(reject_overhead_amt) as reject_oh_amt')
                                    ->First();

        $reject['finished_prev'] = DB::table('production_finished_stocks')
                                    ->where('approve_status',1)
                                    ->whereBetween('date', [$from_date, $end_date])
                                    ->selectRaw('sum(reject_amt) as reject_amt, sum(reject_overhead_amt) as reject_oh_amt')
                                    ->First();
        $reject['finished_all'] = DB::table('production_finished_stocks')
                                    ->where('approve_status',1)
                                    ->selectRaw('sum(reject_amt) as reject_amt, sum(reject_overhead_amt) as reject_oh_amt')
                                    ->First();

        $reject['packing_prev'] = DB::table('production_packings')
                                    ->where('approve_status',1)
                                    ->whereBetween('packing_date', [$from_date, $end_date])
                                    ->selectRaw('sum(reject_amt) as reject_amt, sum(reject_overhead_amt) as reject_oh_amt')
                                    ->First();
        $reject['packing_all'] = DB::table('production_packings')      
                                    ->where('approve_status',1)                              
                                    ->selectRaw('sum(reject_amt) as reject_amt, sum(reject_overhead_amt) as reject_oh_amt')
                                    ->First();

        return $reject;
    }
}
