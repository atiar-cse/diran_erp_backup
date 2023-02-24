<?php

namespace App\Http\Controllers\Production;


use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Model\Production\ProductionRequisitionForRm;
use App\Model\Production\ProductionRmRatioSetup;
use App\Model\Production\ProductionProducts;


use App\Model\Production\ProductionRequisitionForRmDetails;
use App\Model\Purchase\PurchaseWareHouse;


use App\Model\Accounts\AccountsChartofAccounts;
use App\Model\Accounts\AccountsJournalEntry;
use App\Model\Accounts\AccountsJournalEntryDetails;
use App\Model\Accounts\AccountsMainCode;
use App\Model\Accounts\AccountsCostCenter;


use App\Model\Inventory\InventoryCurrentStock;
use App\Model\Inventory\StockTransectionLog;
use App\Model\Inventory\StockTransectionLogDetails;

use Illuminate\Support\Facades\Auth;
use DB;

use App\Lib\Enumerations\AccountsTransactionType;
use App\Lib\Enumerations\StockTransactionType;

use App\Repositories\CommonRepositories;

use App\Model\Production\ProductionRecycleChip;

class RequisitionForRawMaterialController extends Controller
{
    protected $commonRepositories;
    public function __construct(CommonRepositories $commonRepositories)
    {
        $this->commonRepositories = $commonRepositories;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = DB::table('production_requisition_for_rms')
                ->leftJoin('purchase_ware_houses', 'production_requisition_for_rms.warehouse_id','=','purchase_ware_houses.id')
                ->leftJoin('users as ura', 'production_requisition_for_rms.created_by','=','ura.id')
                ->leftJoin('users as ured', 'production_requisition_for_rms.updated_by','=','ured.id')
                ->leftJoin('users as urea', 'production_requisition_for_rms.approve_by','=','urea.id')
                ->whereNull('production_requisition_for_rms.deleted_at')
                ->select(['production_requisition_for_rms.id AS id',
                    'production_requisition_for_rms.rm_requisition_no',
                    'production_requisition_for_rms.requisition_date',
                    'production_requisition_for_rms.requisition_types',
                    'production_requisition_for_rms.estimate_qty_for_production',
                    'production_requisition_for_rms.total_qty',
                    'production_requisition_for_rms.total_amount',
                    'production_requisition_for_rms.created_by',
                    'production_requisition_for_rms.updated_by',
                    'production_requisition_for_rms.approve_by',
                    'production_requisition_for_rms.approve_status',
                    'purchase_ware_houses.purchase_ware_houses_name',
                    'ura.user_name as ad_name',
                    'ured.user_name as ed_name',
                    'urea.user_name as ap_name'
                ]);

            return datatables()->of($query)->toJson();


        }

        $productLists   = ProductionProducts::select('production_products.id','production_products.product_name',
                            'production_products.buying_price',
                            'production_measure_units.measure_unit')
                            ->leftJoin('production_measure_units', 'production_products.measure_unit_id', '=', 'production_measure_units.id')
                            ->where('production_products.status', '1')->where('production_products.is_raw_material_status', '1')
                            ->get();

        $warehouse   = PurchaseWareHouse::where('status', '1')->get();
        $chartofAccountList = AccountsChartofAccounts::where('status', '11111')->get();
        $costCenterLists  = AccountsCostCenter::where('status', '1')->get();

        return view('production.production_section.requisition_for_rm',
            compact('productLists','warehouse','chartofAccountList','costCenterLists'));
    }

    public function requisitionRMNoGenerate(){
        $id = ProductionRequisitionForRm::withTrashed()->count();
        $currentId = $id+1;
        return 'RRM-'.date('Ym').$currentId;
    }

    public function rmRatioSetupList()
    {

        $rmRationSetupList  = ProductionRmRatioSetup::select('production_rm_ratio_setups.product_id','production_rm_ratio_setups.one_kg_weight', 'production_products.product_name', 'production_products.buying_price')
                            ->join('production_products', 'production_rm_ratio_setups.product_id', '=', 'production_products.id')
                            ->get();
        $dataFormat = [];
        foreach ($rmRationSetupList as $key => $value) {
         $dataFormat[] = [
            'product_id' => $value->product_id,
            'one_kg_weight' => $value->one_kg_weight,
            'current_stock_qty' => 0,
            'qty' => $value->one_kg_weight,
            'remarks' => '',
            'unit_price' =>  $value->buying_price,
            'total_price' => 0,
         ];
        }

        return $dataFormat;
    }

    public function store(Request $request)
    {

        $inv_no_like = 'RRM-';
        $rtn_val =InvStore($request->rm_requisition_no, $inv_no_like,
            'production_requisition_for_rms','rm_requisition_no');

        $request->merge(['rm_requisition_no' => $rtn_val]) ;



        $this->validate($request, [
            'rm_requisition_no' => 'required',
            'requisition_date' => 'required',
            'warehouse_id' => 'required',

            'details.*.product_id' => 'required',
            'details.*.qty' => 'required',
            'details.*.unit_price' => 'required',
            'details.*.total_price' => 'required',
        ], [
            'details.*.product_id.required' => 'Required field.',
            'details.*.qty.required' => 'Required field.',
            'details.*.unit_price.required' => 'Required field.',
            'details.*.total_price.required' => 'Required field.',
        ]);

        $data = [
            'rm_requisition_no' => $request->rm_requisition_no,
            'requisition_date' => dateConvertFormtoDB($request->requisition_date),
            'warehouse_id' => $request->warehouse_id,
            'narration' => $request->narration,
            'requisition_types' => $request->requisition_types,
            'estimate_qty_for_production' => $request->estimate_qty_for_production,
            'total_qty' => $request->total_qty,
            'total_amount' => $request->total_amount,
            'created_by' => Auth::user()->id,
        ];

        try {
            DB::beginTransaction();

            $result = ProductionRequisitionForRm::create($data);
            $details = $this->dataFormat($request, $result->id);
            ProductionRequisitionForRmDetails::insert($details);

            DB::commit();
            //return response()->json(['status' => 'success', 'message' => 'The Requisition Form was successfully saved!']);
            return response()->json(['status' => 'success', 'message' => "The Requisition Form # " . $request->rm_requisition_no .' Successfully Saved!']);
        } catch (\Exception $e) {

            DB::rollback();
            print_r($e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    public function show($id)
    {
        try {
            $editModeData = ProductionRequisitionForRm::with('warehouse')->FindOrFail($id);
            $editModeDetailsData = ProductionRequisitionForRmDetails::Select('production_requisition_for_rm_details.*','production_products.product_name','production_products.buying_price','production_measure_units.measure_unit')
                ->leftJoin('production_products', 'production_requisition_for_rm_details.product_id', '=', 'production_products.id')
                ->leftJoin('production_measure_units', 'production_products.measure_unit_id', '=', 'production_measure_units.id')
                ->where('requisition_for_rm_id',$id)->get();

            $data = [
                'id'    => $editModeData->id,
                'rm_requisition_no' => $editModeData->rm_requisition_no,
                'requisition_date' => dateConvertDBtoForm($editModeData->requisition_date),
                'warehouse_id' => $editModeData->warehouse->purchase_ware_houses_name,
                'requisition_types' => $editModeData->requisition_types,
                'narration' => $editModeData->narration,
                'estimate_qty_for_production' => $editModeData->estimate_qty_for_production,
                'total_qty' => $editModeData->total_qty,
                'total_amount' => $editModeData->total_amount,
                'details'    => $editModeDetailsData
            ];
            return response()->json(['status'=>'success','data'=>$data]);
        } catch(\Exception $e){
            //echo $e->getMessage();
            return response()->json(['status'=>'error','data'=>[]]);
        }
    }

    public function edit($id)
    {
        try {
            $editModeData = ProductionRequisitionForRm::FindOrFail($id);
            $editModeDetailsData = ProductionRequisitionForRmDetails::Select('production_requisition_for_rm_details.*','production_rm_ratio_setups.one_kg_weight')
                                    ->leftJoin('production_rm_ratio_setups', 'production_rm_ratio_setups.product_id','=','production_requisition_for_rm_details.product_id')
                                    ->where('requisition_for_rm_id',$id)
                                    ->get();

            $dataFormat = [];
            foreach ($editModeDetailsData as $key => $value) {

                $ics = DB::table('inventory_current_stocks as st')
                    ->where('st.inventory_current_stocks_product_id',$value->product_id)
                    ->where('st.inventory_current_stocks_warehouse_id',$editModeData->warehouse_id)
                    ->first();

                    $dataFormat[] = [
                        'id' =>  $value->id,
                        'requisition_for_rm_id' => $value->requisition_for_rm_id,
                        'product_id' => $value->product_id,
                        'qty' => $value->qty,
                        'unit_price' => $value->unit_price,
                        'total_price' => $value->total_price,
                        'remarks' => $value->remarks,
                        'one_kg_weight' => $value->one_kg_weight,
                        'current_stock_qty' => $ics ? $ics->inventory_stocks_current_qty : 0,
                    ];
            }

            $data = [
                'id'    => $editModeData->id,
                'rm_requisition_no' => $editModeData->rm_requisition_no,
                'requisition_date' => dateConvertDBtoForm($editModeData->requisition_date),
                'warehouse_id' => $editModeData->warehouse_id,
                'requisition_types' => $editModeData->requisition_types,
                'narration' => $editModeData->narration,
                'estimate_qty_for_production' => $editModeData->estimate_qty_for_production,
                'total_qty' => $editModeData->total_qty,
                'total_amount' => $editModeData->total_amount,
                'approve_status' => $editModeData->approve_status,
                'details'    => $dataFormat,
                'deleteID' => [],

            ];
            return response()->json(['status'=>'success','data'=>$data]);
        } catch(\Exception $e){
            return response()->json(['status'=>'error','data'=>[]]);
        }
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'rm_requisition_no' => 'required',
            'requisition_date' => 'required',
            'warehouse_id' => 'required',

            'details.*.product_id' => 'required',
            'details.*.qty' => 'required',
            'details.*.unit_price' => 'required',
            'details.*.total_price' => 'required',
        ], [

            'details.*.product_id.required' => 'Required field.',
            'details.*.qty.required' => 'Required field.',
            'details.*.unit_price.required' => 'Required field.',
            'details.*.total_price.required' => 'Required field.',
        ]);

        $app = $request->approve_status;


        try {
            DB::beginTransaction();
            $editModeData = ProductionRequisitionForRm::FindOrFail($id);

            $editModeData->rm_requisition_no           = $request->rm_requisition_no;
            $editModeData->requisition_date            = dateConvertFormtoDB($request->requisition_date);
            $editModeData->warehouse_id                = $request->warehouse_id;
            $editModeData->requisition_types           = $request->requisition_types;
            $editModeData->narration                   = $request->narration;
            $editModeData->estimate_qty_for_production = $request->estimate_qty_for_production;
            $editModeData->total_qty                   = $request->total_qty;
            $editModeData->total_amount                = $request->total_amount;

            if ($app!=1) {
                $editModeData->updated_by = Auth::user()->id;
            }

            $editModeData->save();

            /* Insert update and delete rm requisition details  */
            if (count($request->deleteID) > 0) {
                ProductionRequisitionForRmDetails::whereIn('id', $request->deleteID)->delete();
            }

            $dataFormat = [];
            $count = count($request->details);
            for ($i = 0; $i < $count; $i++) {
                if (isset($request->details[$i]['id']) && $request->details[$i]['id'] !='') {
                    $updateData = [
                        'requisition_for_rm_id' => $id,
                        'product_id'            => $request->details[$i]['product_id'],
                        'current_stock_qty'     => $request->details[$i]['current_stock_qty'] ? $request->details[$i]['current_stock_qty'] : 0,
                        'qty'                   => $request->details[$i]['qty'],
                        'unit_price'               => $request->details[$i]['unit_price'],
                        'total_price'               => $request->details[$i]['total_price'],
                        'remarks'               => $request->details[$i]['remarks'],
                    ];
                    ProductionRequisitionForRmDetails::where('id', $request->details[$i]['id'])->update($updateData);
                } else {
                    $dataFormat[$i] =[
                        'requisition_for_rm_id'    => $id,
                        'product_id'                => $request->details[$i]['product_id'],
                        'current_stock_qty'               => $request->details[$i]['current_stock_qty'] ? $request->details[$i]['current_stock_qty'] : 0,
                        'qty'               => $request->details[$i]['qty'],
                        'unit_price'               => $request->details[$i]['unit_price'],
                        'total_price'               => $request->details[$i]['total_price'],
                        'remarks'               => $request->details[$i]['remarks'],
                    ];
                }
            }

            if(count($dataFormat) > 0){
                ProductionRequisitionForRmDetails::insert($dataFormat);
            }

            if($app == 1) {
                $this->approve($id);
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'The Requisition Form was successfully updated!']);
        } catch (\Exception $e) {

            DB::rollback();
            print_r($e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    public function destroy($id)
    {
        try{

            ProductionRequisitionForRmDetails::where('requisition_for_rm_id',$id)->delete();
            ProductionRequisitionForRm ::FindOrFail($id)->delete();

            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->errorInfo[1];
        }


        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Requisition for RM successfully Deleted !']);
        } elseif ($bug == 1451) {
            return response()->json(['status' => 'error', 'message' => 'This data is used another table,can not delete data']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    public function dataFormat($request, $id)
    {
        $dataFormat = [];
        $count = count($request->details);
        for ($i = 0; $i < $count; $i++) {
            $dataFormat[$i] = [
                'requisition_for_rm_id' => $id,
                'product_id' => $request->details[$i]['product_id'],
                'current_stock_qty' => $request->details[$i]['current_stock_qty'] ? $request->details[$i]['current_stock_qty'] : 0,
                'qty' => $request->details[$i]['qty'],
                'unit_price' => $request->details[$i]['unit_price'],
                'total_price' => $request->details[$i]['total_price'],
                'remarks' => $request->details[$i]['remarks']
            ];
        }

        return $dataFormat;
    }

    private function decrementStockQTY($ProductArray,$warehouseId){
        foreach($ProductArray as $val) {
            InventoryCurrentStock::where('inventory_current_stocks_product_id', $val['product_id'])
                ->where('inventory_current_stocks_warehouse_id', $warehouseId)
                ->decrement('inventory_stocks_current_qty', $val['qty']);

            InventoryCurrentStock::where('inventory_current_stocks_product_id', $val['product_id'])
                ->where('inventory_current_stocks_warehouse_id', $warehouseId)
                ->decrement('total_price', $val['total_price']);
        }
    }

    public function approve($id)
    {

        $appData = ProductionRequisitionForRm::where('id', $id)->first();
        if ($appData->approve_status == 0) {

            if ($appData->requisition_types == 'Glaze Materials' && $appData->estimate_qty_for_production > 0) {

                $price_per_kg = $appData->total_amount / $appData->estimate_qty_for_production;

                $recycleChip = ProductionRecycleChip::First();
                $recycleChip->glaze_material_price_per_kg = round($price_per_kg, 2);
                $recycleChip->updated_by = Auth::user()->id;
                $recycleChip->save();
            }


            $appDetailsData = ProductionRequisitionForRmDetails::where('requisition_for_rm_id', $id)->get();

            $date = $appData->requisition_date;
            $is_in_out = 2; //2=Stock Out
            $log_type = StockTransactionType::$prodution_req_rm; ;
            $serial = $appData->rm_requisition_no;

            $ref_tbl = 'production_requisition_for_rms';
            $ref_tbl_id = 0;
            $log_tbl = 'production_requisition_for_rms';

            $sup_cus_tabl = '';
            $supplier = 0;
            $warehouseId = $appData->warehouse_id;
            $total_qty = $appData->total_qty;
            $total_price = $appData->total_amount;


            /**************************************** STOCK LOG INFO START ********************************************************/

            $stock_log = [
                'stock_transection_logs_date'           => $date,
                'is_in_out'                             => $is_in_out,
                'stock_transection_logs_type'           => $log_type,
                'stock_transection_logs_number'         => $serial,
                'stock_transection_logs_ref_table_name' => $ref_tbl,
                'stock_transection_logs_ref_table_id'   => $ref_tbl_id,
                'stock_transection_logs_table_name'     => $log_tbl,
                'stock_transection_logs_table_id'       => $id,
                'stock_trans_supp_cus_table_name'       => $sup_cus_tabl,
                'stock_trans_supp_cus_table_id'         => $supplier,
                'stock_trans_tender' 					=> '',
                'stock_trans_warehouse_id'              => $warehouseId,
                'stock_trans_qty'                       => $total_qty,
                'stock_trans_total_price'               => $total_price,
                'created_by'                            => Auth::user()->id,
            ];
            $mainStock = StockTransectionLog::create($stock_log);

            $Stockdetails[] = '';
            $details_tbl = 'production_requisition_for_rm_details';

            $a=0;
            foreach($appDetailsData as $row){
                $invSt = InventoryCurrentStock::where('inventory_current_stocks_product_id', $row->product_id)
                    ->where('inventory_current_stocks_warehouse_id', $warehouseId)->first();

                $inventory_stocks_open_qty= 0;
                $inventory_stocks_current_qty= 0;
                if ($invSt) {
                    $inventory_stocks_open_qty= $invSt->inventory_stocks_current_qty;

                    $inventory_stocks_current_qty= $invSt->inventory_stocks_current_qty - $row->qty;
                }

                $Stockdetails[$a] = [
                    'log_id'            => $mainStock->id,
                    'is_in_out'         => $is_in_out,
                    'log_table_name'    => $details_tbl,
                    'log_table_id'      => $row->id,
                    'product_id'        => $row->product_id,
                    'warehouse_id'      => $warehouseId,
                    'log_entry_qty'     => $row->qty,
                    'log_open_qty'      => $inventory_stocks_open_qty,
                    'log_current_qty'   => $inventory_stocks_current_qty,
                    'log_close_qty'     => $inventory_stocks_current_qty,

                    'log_unit_price'    => $row->unit_price,
                    'log_total_price'   => $row->total_price,

                    'add_sub'           => 0,

                    'entry_date'        => $date,
                    'created_by'        => Auth::user()->id,
                    'created_at'        => Carbon::now(),
                    'updated_at'        => Carbon::now(),
                ];
                $a++;
            }

            StockTransectionLogDetails::insert($Stockdetails);

            $this->decrementStockQTY($appDetailsData,$warehouseId);

            /**************************************** Approved ********************************************************/
            if ($appData->approve_status == 0) {

                $appData->approve_status = 1;
                $appData->approve_by = Auth::user()->id;
                $appData->approve_at = Carbon::now();
                $appData->save();
            }

        }
    }
}
