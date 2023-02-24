<?php

namespace App\Http\Controllers\Inventory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Model\Production\ProductionProducts;
use App\Model\Purchase\PurchaseWareHouse;

use App\Model\Inventory\InventoryStockTransfer;
use App\Model\Inventory\InventoryStockTransferDetails;

use App\Model\Inventory\InventoryCurrentStock;
use App\Model\Inventory\StockTransectionLog;
use App\Model\Inventory\StockTransectionLogDetails;

use App\Lib\Enumerations\StockTransactionType;

use Illuminate\Support\Facades\Auth;
use DB;


class InventoryStockTransferController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $query = DB::table('inventory_stock_transfers')
                ->leftJoin('purchase_ware_houses as whfrm', 'inventory_stock_transfers.from_warehouse_id','=','whfrm.id')
                ->leftJoin('purchase_ware_houses as wfto', 'inventory_stock_transfers.to_warehouse_id','=','wfto.id')
                ->leftJoin('users as ura', 'inventory_stock_transfers.created_by','=','ura.id')
                ->leftJoin('users as ured', 'inventory_stock_transfers.updated_by','=','ured.id')
                ->leftJoin('users as urea', 'inventory_stock_transfers.approve_by','=','urea.id')
                ->whereNull('inventory_stock_transfers.deleted_at')
                ->select(['inventory_stock_transfers.id AS id',
                    'inventory_stock_transfers.transfers_no',
                    'inventory_stock_transfers.transfers_date',
                    'inventory_stock_transfers.transfers_narration',
                    'inventory_stock_transfers.transfers_total_qty',
                    'inventory_stock_transfers.transfers_total_price',
                    'inventory_stock_transfers.approve_status',
                    'inventory_stock_transfers.created_by',
                    'inventory_stock_transfers.updated_by',
                    'inventory_stock_transfers.approve_by',
                    'whfrm.purchase_ware_houses_name as whfrm_name',
                    'wfto.purchase_ware_houses_name as wfto_name',
                    'ura.user_name as ad_name',
                    'ured.user_name as ed_name',
                    'urea.user_name as ap_name',
                ]);

            return datatables()->of($query)->toJson();

        }

        $warehouseLists  = PurchaseWareHouse::orderBy('purchase_ware_houses_name','ASC')->where('status', '1')->get();

        return view('inventory.inventory_stock_transfer',compact( 'warehouseLists'));
    }

    public function transNoGenerate(){

        $rtn_val = InvGenerate('TR','inventory_stock_transfers','transfers_no');
        return $rtn_val;

        /*$id = InventoryStockTransfer::max('id');
        $currentId = $id+1;
        return 'TR-'.date('Ym').$currentId;*/
    }

    public function store(Request $request)
    {
        date_default_timezone_set("Asia/Dhaka");

        $inv_no_like = 'TR';
        $rtn_val =InvStore($request->transfers_no, $inv_no_like,
            'inventory_stock_transfers','transfers_no');

        $request->merge(['transfers_no' => $rtn_val]) ;


        $this->validate($request, [
            'transfers_no' => 'required|unique:inventory_stock_transfers,transfers_no',
            'transfers_date' => 'required',
            'from_warehouse_id' => 'required',
            'to_warehouse_id' => 'required',
            'transfers_total_qty' => 'required',
            'transfers_total_price' => 'required',

            'details.*.product_id' => 'required',
            'details.*.unit_price' => 'required',
            'details.*.qty' => 'required',
            'details.*.total_price' => 'required',
        ], [
            'details.*.product_id.required' => 'Required field.',
            'details.*.unit_price.required' => 'Required field.',
            'details.*.qty.required' => 'Required field.',
            'details.*.total_price.required' => 'Required field.',
        ]);

        $date = str_replace('/', '-', $request->transfers_date);
        $date_val =date('Y-m-d', strtotime($date));

        $app= $request->approve;

        $data = [
            'transfers_no'          => $request->transfers_no,
            'transfers_date'        => $date_val,
            'from_warehouse_id'     => $request->from_warehouse_id,
            'to_warehouse_id'       => $request->to_warehouse_id,
            'transfers_narration'   => $request->transfers_narration,
            'transfers_total_qty'   => $request->transfers_total_qty,
            'transfers_total_price' => $request->transfers_total_price,
            'created_by'            => Auth::user()->id,
        ];

        try {
            DB::beginTransaction();

            $result = InventoryStockTransfer::create($data);
            $details = $this->dataFormat($request, $result->id);
            InventoryStockTransferDetails::insert($details);

            if($app ==1){
                $this->approve($result->id);

                if($request->updated_by == Null){
                    DB::table('inventory_stock_transfers')->where('id', $result->id)->update(array('updated_by' => Auth::user()->id));
                }
            }

            DB::commit();
            //return response()->json(['status' => 'success', 'message' => 'Stock Transfer successfully saved!']);
            return response()->json(['status' => 'success', 'message' => "Stock Transfer # " . $request->transfers_no .' Successfully Saved!']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    public function show($id)
    {
        /*echo $id; exit();*/

        try {
            $editModeData = InventoryStockTransfer::where('id',$id)->first();
            $editModeDetailsData = InventoryStockTransferDetails::where('transfer_id',$id)->get();
            $dataFormat = [];

            $from_warehouse= PurchaseWareHouse::where('id',$editModeData->from_warehouse_id)->first()->purchase_ware_houses_name;
            $to_warehouse= PurchaseWareHouse::where('id',$editModeData->to_warehouse_id)->first()->purchase_ware_houses_name;

            foreach ($editModeDetailsData as $key => $value) {

                $qty_val = DB::table('inventory_current_stocks as st')
                    ->where('st.product_id',$value->product_id)
                    ->where('st.warehouse_id',$editModeData->from_warehouse_id)
                    ->first();

                if($qty_val){
                    $qty = $qty_val->current_qty;
                }else{
                    $qty= 0;
                }

                $product_name= ProductionProducts::where('id',$value->product_id)->first()->product_name;

                $dataFormat[] = [
                    'id'            => $value->id,
                    'transfer_id'   => $value->transfer_id,
                    'product_id'    => $product_name,
                    'remarks'       => $value->remarks,
                    'unit'          => $value->unit,
                    'unit_price'    => $value->unit_price,
                    'current_qty'   => $qty,
                    'qty'           => $value->qty,
                    'total_price'   => $value->total_price,

                ];
            }
            $data = [
                'id'                        => $editModeData->id,
                'transfers_no'              => $editModeData->transfers_no,
                'transfers_date'            => date('d/m/Y', strtotime($editModeData->transfers_date)),
                'from_warehouse_id'         => $from_warehouse,
                'to_warehouse_id'           => $to_warehouse,
                'transfers_narration'       => $editModeData->transfers_narration,
                'transfers_total_qty'       => $editModeData->transfers_total_qty,
                'transfers_total_price'     => $editModeData->transfers_total_price,

                'approve'                   => $editModeData->approve_status,
                'details'                   => $dataFormat,
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
            $editModeData = InventoryStockTransfer::FindOrFail($id);
            $editModeDetailsData = InventoryStockTransferDetails::where('transfer_id',$id)->get();
            $dataFormat = [];

            foreach ($editModeDetailsData as $key => $value) {

                $qty_val = DB::table('inventory_current_stocks as st')
                    ->where('st.inventory_current_stocks_product_id',$value->product_id)
                    ->where('st.inventory_current_stocks_warehouse_id',$editModeData->from_warehouse_id)
                    ->first();

                if($qty_val){
                    $qty = $qty_val->inventory_stocks_current_qty;
                }else{
                    $qty= 0;
                }

                $dataFormat[] = [
                    'id'            =>  $value->id,
                    'transfer_id'   => $value->transfer_id,
                    'product_id'    => $value->product_id,
                    'remarks'       => $value->remarks,
                    'unit'          => $value->unit,
                    'unit_price'    => $value->unit_price,
                    'current_qty'   => $qty,
                    'qty'           => $value->qty,
                    'total_price'   => $value->total_price,

                ];
            }
            $data = [
                'id'                        => $editModeData->id,
                'transfers_no'              => $editModeData->transfers_no,
                'transfers_date'            => date('d/m/Y', strtotime($editModeData->transfers_date)),
                'from_warehouse_id'         => $editModeData->from_warehouse_id,
                'to_warehouse_id'           => $editModeData->to_warehouse_id,
                'transfers_narration'       => $editModeData->transfers_narration,
                'transfers_total_qty'       => $editModeData->transfers_total_qty,
                'transfers_total_price'     => $editModeData->transfers_total_price,

                'approve'                   => '',
                'deleteID'                  => [],
                'details'                   => $dataFormat,
            ];

            $pid_list = DB::table('production_products as pr')
                ->leftJoin('production_measure_units as mr', 'pr.measure_unit_id', '=', 'mr.id')
                ->leftJoin('inventory_current_stocks as st', 'st.inventory_current_stocks_product_id','=','pr.id')
                ->selectRaw('pr.*, mr.measure_unit,st.inventory_stocks_current_qty as c_qty')
                ->where('pr.status', '1')
                ->where('st.inventory_current_stocks_warehouse_id', $editModeData->from_warehouse_id)
                ->orderBy('pr.product_name','ASC')
                ->get();
            $test_val = [
                'edit_mode' => $data,
                'pid_list' => $pid_list,
            ];
            return response()->json(['status'=>'success','data'=>$test_val]);
        } catch(\Exception $e){
            echo $e->getMessage();
            return response()->json(['status'=>'error','data'=>[]]);
        }
    }

    public function update(Request $request, $id)
    {
        date_default_timezone_set("Asia/Dhaka");
        $this->validate($request, [
            'transfers_no' => 'required|unique:inventory_stock_transfers,transfers_no,'.$id.',id',
            'transfers_date' => 'required',
            'from_warehouse_id' => 'required',
            'to_warehouse_id' => 'required',
            'transfers_total_qty' => 'required',
            'transfers_total_price' => 'required',

            'details.*.product_id' => 'required',
            'details.*.unit_price' => 'required',
            'details.*.qty' => 'required',
            'details.*.total_price' => 'required',
        ], [
            'details.*.product_id.required' => 'Required field.',
            'details.*.unit_price.required' => 'Required field.',
            'details.*.qty.required' => 'Required field.',
            'details.*.total_price.required' => 'Required field.',
        ]);


        $date = str_replace('/', '-', $request->transfers_date);
        $date_val =date('Y-m-d', strtotime($date));

        $app= $request->approve;


        try {
            DB::beginTransaction();

            $editModeData = InventoryStockTransfer::FindOrFail($id);

            $editModeData->transfers_no          = $request->transfers_no;
            $editModeData->transfers_date        = $date_val;
            $editModeData->from_warehouse_id     = $request->from_warehouse_id;
            $editModeData->to_warehouse_id       = $request->to_warehouse_id;
            $editModeData->transfers_narration   = $request->transfers_narration;
            $editModeData->transfers_total_qty   = $request->transfers_total_qty;
            $editModeData->transfers_total_price = $request->transfers_total_price;

            if($app !=1){
                $editModeData->updated_by = Auth::user()->id;
            }


            $editModeData->save();


            /***************************************************** DETAILS TRANSECTION **********************************************************/

            if (count($request->deleteID) > 0) {
                InventoryStockTransferDetails::whereIn('id', $request->deleteID)->delete();
            }

            $dataFormat = [];
            $count = count($request->details);
            for ($i = 0; $i < $count; $i++) {
                if (isset($request->details[$i]['id']) && $request->details[$i]['id'] !='') {

                    $updateData = [
                        'transfer_id'       => $id,
                        'product_id'        => $request->details[$i]['product_id'],
                        'remarks'           => $request->details[$i]['remarks'],
                        'unit'              => $request->details[$i]['unit'],
                        'unit_price'        => $request->details[$i]['unit_price'],
                        'qty'               => $request->details[$i]['qty'],
                        'total_price'       => $request->details[$i]['total_price'],
                    ];
                    InventoryStockTransferDetails::where('id', $request->details[$i]['id'])->update($updateData);
                } else {
                    $dataFormat[$i] =[
                        'transfer_id'       => $id,
                        'product_id'        => $request->details[$i]['product_id'],
                        'remarks'           => $request->details[$i]['remarks'],
                        'unit'              => $request->details[$i]['unit'],
                        'unit_price'        => $request->details[$i]['unit_price'],
                        'qty'               => $request->details[$i]['qty'],
                        'total_price'       => $request->details[$i]['total_price'],
                    ];
                }
            }

            if(count($dataFormat) > 0){
                InventoryStockTransferDetails::insert($dataFormat);
            }


            if($app ==1){
                $result = $this->approve($id);

                if($result == 2)
                {
                    DB::rollback();
                    return response()->json(['status' => 'error', 'message' => 'Current Qty Less than order qty so can not approve']);
                }
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Stock Transfer successfully updated!']);
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => $msg]);
        }
    }

   public function approve($id)
    {

        $appData  = InventoryStockTransfer::where('id',$id)->first();

        if($appData->approve_status == 0)
        {
            $appDetailsData = InventoryStockTransferDetails::where('transfer_id',$id)->get();

            $customer 		= '';
            $serial_no 		= $appData->transfers_no;
            $ref_tbl_id 	= '';
            $warehouseId_fr	= $appData->from_warehouse_id;
            $warehouseId_to	= $appData->to_warehouse_id;

            $date 			= $appData->transfers_date;
            $ref_tbl		= '';
            $log_tbl 		= 'inventory_stock_transfers';
            $sup_cus_tabl 	= '';
            $log_type 		= StockTransactionType::$inventory_transfer;

            $qty_total      = $appData->transfers_total_qty;
            $price_total    = $appData->transfers_total_price;


            /**************************************** STOCK LOG INFO  FOR REMOVING STOCK *********************************/
            $stock_log_fr =[
                'stock_transection_logs_date'               => $date,
                'stock_transection_logs_type' 		        => $log_type ,
                'stock_transection_logs_number'             => $serial_no,
                'stock_transection_logs_ref_table_name'     => $ref_tbl,
                'stock_transection_logs_ref_table_id'       => $ref_tbl_id,

                'stock_transection_logs_table_name'         => $log_tbl,
                'stock_transection_logs_table_id' 	        => $id,

                'stock_trans_supp_cus_table_name'           => $sup_cus_tabl,
                'stock_trans_supp_cus_table_id'             => $customer,

                'stock_trans_tender'                        => '',

                'stock_trans_warehouse_id' 		            => $warehouseId_fr,

                'stock_trans_qty' 		                    => $qty_total,
                'stock_trans_total_price' 		            => $price_total,
            ];
            $mainStock_fr = StockTransectionLog::create($stock_log_fr);

            $Stockdetails = [];
            $details_tbl = 'inventory_stock_transfer_details';

            foreach($appDetailsData as $key => $row){
                $w_info = InventoryCurrentStock::where('inventory_current_stocks_warehouse_id', $warehouseId_fr)
                    ->where('inventory_current_stocks_product_id', $row->product_id)->first();

                if($w_info->inventory_stocks_current_qty - ($row->qty) >= 0)
                {
                    $Stockdetails[] = [
                        'log_id'            => $mainStock_fr->id,
                        'log_table_name'    => $details_tbl,
                        'log_table_id'        => $row->id,

                        'product_id'        => $row->product_id,
                        'warehouse_id'      => $warehouseId_fr,

                        'log_open_qty'      => $w_info->inventory_stocks_open_qty,
                        'log_entry_qty'     => $row->qty,

                        'log_current_qty'   => $w_info->inventory_stocks_current_qty,
                        'log_close_qty'     => '',

                        'log_unit_price'    => $row->unit_price,
                        'log_total_price'   => $row->total_price,

                        'add_sub'           => 0, // 0 for stock out
                        'entry_date'        => $date,
                        'created_by'        => Auth::user()->id,
                    ];
                }
                else{
                    return 2;

                }
            }

            StockTransectionLogDetails::insert($Stockdetails);

            /****************************************** REMOVE STOCK INFORMATION  **********************************************************/
            foreach($appDetailsData as $val){
                $checkExists = InventoryCurrentStock::where('inventory_current_stocks_product_id',$val->product_id)
                    ->where('inventory_current_stocks_warehouse_id',$warehouseId_fr)
                    ->first();

                if($checkExists){
                    $unit_price_fr = $checkExists->unit_price;
                    $total_price_fr = $checkExists->total_price;

                    $price_to_be_deduct = $val->qty * $unit_price_fr;
                    $amount_fr = $total_price_fr - $price_to_be_deduct;

                    InventoryCurrentStock::where('inventory_current_stocks_product_id',$val->product_id)
                        ->where('inventory_current_stocks_warehouse_id',$warehouseId_fr)
                        ->decrement('inventory_stocks_current_qty',  $val->qty );

                    InventoryCurrentStock::where('inventory_current_stocks_product_id',$val->product_id)
                        ->where('inventory_current_stocks_warehouse_id',$warehouseId_fr)
                        ->decrement('total_price',  round($amount_fr, 2) );
                }
            }





            /**************************************** STOCK LOG INFO  FOR ADDING STOCK *********************************/
            $stock_log_to =[
                'stock_transection_logs_date'               => $date,
                'stock_transection_logs_type' 		        => $log_type ,
                'stock_transection_logs_number'             => $serial_no,
                'stock_transection_logs_ref_table_name'     => $ref_tbl,
                'stock_transection_logs_ref_table_id'       => $ref_tbl_id,

                'stock_transection_logs_table_name'         => $log_tbl,
                'stock_transection_logs_table_id' 	        => $id,

                'stock_trans_supp_cus_table_name'           => $sup_cus_tabl,
                'stock_trans_supp_cus_table_id'             => $customer,

                'stock_trans_tender'                        => '',

                'stock_trans_warehouse_id' 		            => $warehouseId_to,

                'stock_trans_qty' 		                    => $qty_total,
                'stock_trans_total_price' 		            => $price_total,
            ];
            $mainStock_to = StockTransectionLog::create($stock_log_to);

            $Stockdetails_to = [];
            $details_tbl_to = 'inventory_stock_transfer_details';

            foreach($appDetailsData as $key => $row_to){
                $w_info_to = InventoryCurrentStock::where('inventory_current_stocks_warehouse_id', $warehouseId_to)
                    ->where('inventory_current_stocks_product_id', $row_to->product_id)->first();

                if($w_info_to){
                    $open_qty =$w_info_to->inventory_stocks_open_qty;
                }else{

                    $open_qty = 0;
                }

                if($w_info_to){
                    $current_qty =$w_info_to->inventory_stocks_current_qty;
                }else{
                    $current_qty = 0;
                }

                    $Stockdetails_to[] = [
                        'log_id'            => $mainStock_to->id,
                        'log_table_name'    => $details_tbl_to,
                        'log_table_id'      => $row_to->id,

                        'product_id'        => $row_to->product_id,
                        'warehouse_id'      => $warehouseId_to,

                        'log_open_qty'      => $open_qty,
                        'log_entry_qty'     => $row_to->qty,

                        'log_current_qty'   => $current_qty,
                        'log_close_qty'     => '',

                        'log_unit_price'    => $row_to->unit_price,
                        'log_total_price'   => $row_to->total_price,

                        'add_sub'           => 1, // 1 for stock IN
                        'entry_date'        => $date,
                        'created_by'        => Auth::user()->id,
                    ];
            }

            StockTransectionLogDetails::insert($Stockdetails_to);

            /****************************************** REMOVE STOCK INFORMATION  **********************************************************/
            foreach($appDetailsData as $val_to){
                $checkExists = InventoryCurrentStock::where('inventory_current_stocks_product_id',$val_to->product_id)
                    ->where('inventory_current_stocks_product_id',$warehouseId_to)
                    ->first();
                if($checkExists){
                    $unit_price_to = $checkExists->unit_price;
                    $total_price_to = $checkExists->total_price;

                    $price_to_be_add = $val_to->qty * $unit_price_to;
                    $amount_to = $total_price_to + $price_to_be_add;

                    InventoryCurrentStock::where('inventory_current_stocks_product_id',$val_to->product_id)
                        ->where('inventory_current_stocks_warehouse_id',$warehouseId_to)
                        ->increment('inventory_stocks_current_qty',  $val_to->qty );

                    InventoryCurrentStock::where('inventory_current_stocks_product_id',$val_to->product_id)
                        ->where('inventory_current_stocks_warehouse_id',$warehouseId_to)
                        ->increment('total_price',  round($amount_to,2) );

                }else{

                    $dataFormat_to =[
                        'inventory_current_stocks_warehouse_id' => $warehouseId_to,
                        'inventory_current_stocks_product_id'   => $val_to->product_id,
                        'inventory_stocks_open_qty'     => $val_to->qty,
                        'inventory_stocks_current_qty'  => $val_to->qty,
                        'unit_price'   => $val_to->unit_price,
                        'total_price'  => $val_to->total_price,
                        'created_by'   => Auth::user()->id,
                        'created_at'   =>  Carbon::now(),
                    ];
                    InventoryCurrentStock::create($dataFormat_to);
                }
            }


            /****************************************** APPROVE INFORMATION  **********************************************************/
            DB::table('inventory_stock_transfers')->where('id', $id)->update(
                array(
                    'approve_status'    => 1,
                    'approve_by'        => Auth::user()->id,
                    'approve_at'        => Carbon::now(),
                ));

        }

    }

    public function destroy($id)
    {
        try{

            InventoryStockTransferDetails::where('transfer_id',$id)->delete();
            DB::table('inventory_stock_transfers')->where('id', $id)->delete();
            return response()->json(['status' => 'success', 'message' => 'Stock Transfer successfully Deleted !']);
        }
        catch(\Exception $e){
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    private function dataFormat($request, $id)
    {
        $dataFormat = [];
        $count = count($request->details);
        for ($i = 0; $i < $count; $i++) {
            $dataFormat[$i] = [
                'transfer_id' => $id,
                'product_id'  => $request->details[$i]['product_id'],
                'remarks'     => $request->details[$i]['remarks'],
                'unit'        => $request->details[$i]['unit'],
                'unit_price'  => $request->details[$i]['unit_price'],
                'qty'         => $request->details[$i]['qty'],
                'total_price' => $request->details[$i]['total_price']
            ];
        }
        return $dataFormat;
    }


    public function listProduct($warehouseId){

        if($warehouseId == '__'){
            $productLists = '';
        }else{
            $productLists = DB::table('production_products as pr')
                            ->leftJoin('production_measure_units as mr', 'pr.measure_unit_id', '=', 'mr.id')
                            ->leftJoin('inventory_current_stocks as st', 'st.inventory_current_stocks_product_id','=','pr.id')
                            ->selectRaw('pr.*, mr.measure_unit,st.inventory_stocks_current_qty as c_qty')
                            ->where('pr.status', '1')
                            ->where('st.inventory_current_stocks_warehouse_id', $warehouseId)
                            ->orderBy('pr.product_name','ASC')
                            ->get();
        }
        return $productLists;
    }
}
