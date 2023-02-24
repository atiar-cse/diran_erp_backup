<?php

namespace App\Http\Controllers\Inventory;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Model\Production\ProductionProducts;
use App\Model\Production\ProductionMeasureUnit;
use App\Model\Purchase\PurchaseWareHouse;

use App\Model\Inventory\InventoryStockAdjust;
use App\Model\Inventory\InventoryStockAdjustDetails;

use App\Model\Inventory\InventoryCurrentStock;
use App\Model\Inventory\StockTransectionLog;

use Illuminate\Support\Facades\Auth;
use DB;

class __InventoryStockAdjustController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {




            $query = DB::table('inventory_stock_adjusts')
                ->leftJoin('purchase_ware_houses', 'inventory_stock_adjusts.inventory_stock_adjusts_warehouse_id', '=', 'purchase_ware_houses.id')
               // ->join('users', 'inventory_stock_adjusts.created_by', '=', 'users.id')
                ->whereNull('inventory_stock_adjusts.deleted_at')
                ->select(['inventory_stock_adjusts.id AS id',
                    'inventory_stock_adjusts.inventory_stock_adjusts_no',
                    'inventory_stock_adjusts.inventory_stock_adjusts_date',
                    'inventory_stock_adjusts.inventory_stock_adjusts_narration',
                    'inventory_stock_adjusts.inventory_stock_adjusts_total_qty',
                    'inventory_stock_adjusts.inventory_stock_adjusts_total_price',
                    'inventory_stock_adjusts.created_by',
                    'inventory_stock_adjusts.updated_by',
                    'purchase_ware_houses.purchase_ware_houses_name',
                    //'users.user_name',
                ]);

            return datatables()->of($query)->toJson();
        }

        $StockAdjustGenerate = $this->StockAdjustGenerate();
        $EnumVal =$this->EnumVal();
        $productLists   = ProductionProducts::Select('production_products.*', 'production_measure_units.measure_unit')
            ->where('production_products.status', '1')
            ->leftJoin('production_measure_units', 'production_products.measure_unit_id', '=', 'production_measure_units.id')
            ->get();
        $warehouseLists   = PurchaseWareHouse::where('status', '1')->get();
        return view('inventory.inventory_stock_adjust',compact('StockAdjustGenerate', 'EnumVal', 'productLists','warehouseLists'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {

        date_default_timezone_set("Asia/Dhaka");
        $this->validate($request, [
            'inventory_stock_adjusts_no' => 'required',
            'inventory_stock_adjusts_date' => 'required',
            'inventory_stock_adjusts_warehouse_id' => 'required',
            'inventory_stock_adjusts_total_qty' => 'required',
            'inventory_stock_adjusts_total_price' => 'required',

            'details.*.inventory_stock_adjust_details_product_id' => 'required',
            'details.*.inventory_stock_adjust_rule' => 'required',
            'details.*.inventory_stock_adjust_details_unit_price' => 'required',
            'details.*.inventory_stock_adjust_details_qty' => 'required',
            'details.*.inventory_stock_adjust_details_total_price' => 'required',
        ], [
            'details.*.inventory_stock_adjust_details_product_id.required' => 'Required field.',
            'details.*.inventory_stock_adjust_rule.required' => 'Required field.',
            'details.*.inventory_stock_adjust_details_unit_price.required' => 'Required field.',
            'details.*.inventory_stock_adjust_details_qty.required' => 'Required field.',
            'details.*.inventory_stock_adjust_details_total_price.required' => 'Required field.',
        ]);


        if($request->inventory_stock_adjusts_date != ''){
            $date = str_replace('/', '-', $request->inventory_stock_adjusts_date);
            $date_val =date('Y-m-d', strtotime($date));
        }else{
            $date_val =date('Y-m-d');
        }

        $warehouse_id   = $request->inventory_stock_adjusts_warehouse_id;
        $app            = $request->approve_status;

        $data = [
            'inventory_stock_adjusts_no'                => $request->inventory_stock_adjusts_no,
            'inventory_stock_adjusts_date'              => $date_val,
            'inventory_stock_adjusts_warehouse_id'      => $request->inventory_stock_adjusts_warehouse_id,

            'inventory_stock_adjusts_narration'         => $request->inventory_stock_adjusts_narration,

            'inventory_stock_adjusts_total_qty'         => $request->inventory_stock_adjusts_total_qty,
            'inventory_stock_adjusts_total_price'       => $request->inventory_stock_adjusts_total_price,

            'created_by'                                => Auth::user()->id,
        ];

        //dd($data);
        try {
            DB::beginTransaction();

            $result = InventoryStockAdjust::create($data);
            $details = $this->dataFormat($request, $result->id);
            InventoryStockAdjustDetails::insert($details);


            if($app ==1){
                $this->approve($result->id);

                if($request->updated_by == Null){
                    DB::table('inventory_stock_adjusts')->where('id', $result->id)->update(array('updated_by' => Auth::user()->id));
                }

            }else{
                DB::table('inventory_stock_adjusts')->where('id', $result->id)->update(array('approve_status' => $app));
            }


            /*$type=4; //For StockAdjust
            $sl=$request->inventory_stock_adjusts_no;
            $ref_tbl = '';
            $ref_tbl_id = 0;
            $main_tbl='inventory_stock_adjusts';
            $main_tbl_id=$result->id;
            $supplier_tbl='';
            $supplier_tbl_id= 0;
            $warehouse_id=$request->inventory_stock_adjusts_warehouse_id;
            $stock_log =$this->stockLog($date_val,$type,$sl,$ref_tbl,$ref_tbl_id, $main_tbl,$main_tbl_id,$supplier_tbl,$supplier_tbl_id,$warehouse_id);
            StockTransectionLog::create($stock_log);



            $this->CheckStock($details,$warehouse_id);*/

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Stock Adjust successfully saved!']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        try {
            $editModeData = InventoryStockAdjust::FindOrFail($id);
            $editModeDetailsData = InventoryStockAdjustDetails::where('inventory_stock_adjust_id',$id)->get();
            $data = [
                'id'    => $editModeData->id,
                'inventory_stock_adjusts_no' => $editModeData->inventory_stock_adjusts_no,
                'inventory_stock_adjusts_date' => date('d/m/Y', strtotime($editModeData->inventory_stock_adjusts_date)),
                'inventory_stock_adjusts_warehouse_id' => $editModeData->inventory_stock_adjusts_warehouse_id,
                'inventory_stock_adjusts_narration' => $editModeData->inventory_stock_adjusts_narration,
                'inventory_stock_adjusts_total_qty' => $editModeData->inventory_stock_adjusts_total_qty,
                'inventory_stock_adjusts_total_price' => $editModeData->inventory_stock_adjusts_total_price,
                'deleteID' => [],
                'details'    => $editModeDetailsData
            ];
            return response()->json(['status'=>'success','data'=>$data]);
        } catch(\Exception $e){
            return response()->json(['status'=>'error','data'=>[]]);
        }
    }

    public function update(Request $request, $id)
    {
        date_default_timezone_set("Asia/Dhaka");
        $this->validate($request, [
            'inventory_stock_adjusts_no' => 'required',
            'inventory_stock_adjusts_date' => 'required',
            'inventory_stock_adjusts_warehouse_id' => 'required',
            'inventory_stock_adjusts_total_qty' => 'required',
            'inventory_stock_adjusts_total_price' => 'required',

            'details.*.inventory_stock_adjust_details_product_id' => 'required',
            'details.*.inventory_stock_adjust_rule' => 'required',
            'details.*.inventory_stock_adjust_details_unit_price' => 'required',
            'details.*.inventory_stock_adjust_details_qty' => 'required',
            'details.*.inventory_stock_adjust_details_total_price' => 'required',
        ], [
            'details.*.inventory_stock_adjust_details_product_id.required' => 'Required field.',
            'details.*.inventory_stock_adjust_rule.required' => 'Required field.',
            'details.*.inventory_stock_adjust_details_unit_price.required' => 'Required field.',
            'details.*.inventory_stock_adjust_details_qty.required' => 'Required field.',
            'details.*.inventory_stock_adjust_details_total_price.required' => 'Required field.',
        ]);


        if($request->inventory_stock_adjusts_date != ''){
            $date = str_replace('/', '-', $request->inventory_stock_adjusts_date);
            $date_val =date('Y-m-d', strtotime($date));
        }else{
            $date_val =date('Y-m-d');
        }

        $warehouse_id   = $request->inventory_stock_adjusts_warehouse_id;
        $app            = $request->approve_status;

        try {
            DB::beginTransaction();

            $editModeData = InventoryStockAdjust::FindOrFail($id);
            $editModeData->inventory_stock_adjusts_no               = $request->inventory_stock_adjusts_no;
            $editModeData->inventory_stock_adjusts_date             = $date_val;
            $editModeData->inventory_stock_adjusts_warehouse_id     = $warehouse_id;
            $editModeData->inventory_stock_adjusts_narration        = $request->narration;
            $editModeData->inventory_stock_adjusts_total_qty        = $request->inventory_stock_adjusts_total_qty;
            $editModeData->inventory_stock_adjusts_total_price      = $request->inventory_stock_adjusts_total_price;
            if($app !=1){
                $editModeData->updated_by  = Auth::user()->id;
            }

            $editModeData->save();


            /*$type=4; //For StockAdjust
            $sl=$request->inventory_stock_adjusts_no;
            $ref_tbl = '';
            $ref_tbl_id = 0;
            $main_tbl='inventory_stock_adjusts';
            $main_tbl_id=$id;
            $supplier_tbl='';
            $supplier_tbl_id= 0;
            $warehouse_id=$request->inventory_stock_adjusts_warehouse_id;
            $stock_log =$this->stockLog($date_val,$type,$sl,$ref_tbl,$ref_tbl_id, $main_tbl,$main_tbl_id,$supplier_tbl,$supplier_tbl_id,$warehouse_id);
            $where_clause =[
                'stock_transection_logs_table_name' => $main_tbl,
                'stock_transection_logs_table_id' => $main_tbl_id,
                'stock_trans_warehouse_id' => $warehouse_id
            ];
            StockTransectionLog::where($where_clause)->update($stock_log);*/



            //// details transection ////
            /*if (count($request->deleteID) > 0) {
                $deldata_array =InventoryStockAdjustDetails::whereIn('id', $request->deleteID)->get();
                foreach($deldata_array as $val){
                    $rule = $val['inventory_stock_adjust_rule'];
                    if($rule =='Inc'){
                        InventoryCurrentStock::where('inventory_current_stocks_product_id',$val['inventory_stock_adjust_details_product_id'])
                            ->where('inventory_current_stocks_warehouse_id',$warehouse_id)
                            ->decrement('inventory_stocks_current_qty',  $val['inventory_stock_adjust_details_qty']);
                    }else{
                        InventoryCurrentStock::where('inventory_current_stocks_product_id',$val['inventory_stock_adjust_details_product_id'])
                            ->where('inventory_current_stocks_warehouse_id',$warehouse_id)
                            ->increment('inventory_stocks_current_qty',  $val['inventory_stock_adjust_details_qty']);
                    }
                }
                InventoryStockAdjustDetails::whereIn('id', $request->deleteID)->delete();
            }*/

            $dataFormat = [];
            $count = count($request->details);
            for ($i = 0; $i < $count; $i++) {
                if (isset($request->details[$i]['id']) && $request->details[$i]['id'] !='') {
                    /*$pervquantity=InventoryStockAdjustDetails::where('inventory_stock_adjust_details_product_id', $request->details[$i]['inventory_stock_adjust_details_product_id'])
                        ->first();

                    $rule = $pervquantity['inventory_stock_adjust_rule'];
                    if($rule =='Inc'){
                        InventoryCurrentStock::where('inventory_current_stocks_product_id',$pervquantity['inventory_stock_adjust_details_product_id'])
                            ->where('inventory_current_stocks_warehouse_id',$warehouse_id)
                            ->decrement('inventory_stocks_current_qty',  $pervquantity['inventory_stock_adjust_details_qty']);
                    }else{
                        InventoryCurrentStock::where('inventory_current_stocks_product_id',$pervquantity['inventory_stock_adjust_details_product_id'])
                            ->where('inventory_current_stocks_warehouse_id',$warehouse_id)
                            ->increment('inventory_stocks_current_qty', $pervquantity['inventory_stock_adjust_details_qty']);
                    }*/
                    $updateData = [
                        'inventory_stock_adjust_id' => $id,
                        'inventory_stock_adjust_details_product_id' => $request->details[$i]['inventory_stock_adjust_details_product_id'],
                        'inventory_stock_adjust_details_remarks' => $request->details[$i]['inventory_stock_adjust_details_remarks'],
                        'inventory_stock_adjust_rule' => $request->details[$i]['inventory_stock_adjust_rule'],
                        'inventory_stock_adjust_details_unit' => $request->details[$i]['inventory_stock_adjust_details_unit'],
                        'inventory_stock_adjust_details_unit_price' => $request->details[$i]['inventory_stock_adjust_details_unit_price'],
                        'inventory_stock_adjust_details_qty' => $request->details[$i]['inventory_stock_adjust_details_qty'],
                        'inventory_stock_adjust_details_total_price' => $request->details[$i]['inventory_stock_adjust_details_total_price']
                    ];
                    /*if($rule =='Inc'){
                        InventoryCurrentStock::where('inventory_current_stocks_product_id',$request->details[$i]['inventory_stock_adjust_details_product_id'])
                            ->where('inventory_current_stocks_warehouse_id',$warehouse_id)
                            ->increment('inventory_stocks_current_qty',  $request->details[$i]['inventory_stock_adjust_details_qty']);
                    }else{
                        InventoryCurrentStock::where('inventory_current_stocks_product_id',$request->details[$i]['inventory_stock_adjust_details_product_id'])
                            ->where('inventory_current_stocks_warehouse_id',$warehouse_id)
                            ->decrement('inventory_stocks_current_qty', $request->details[$i]['inventory_stock_adjust_details_qty']);
                    }*/
                    InventoryStockAdjustDetails::where('id', $request->details[$i]['id'])->update($updateData);
                } else {
                    $dataFormat[$i] =[
                        'inventory_stock_adjust_id' => $id,
                        'inventory_stock_adjust_details_product_id' => $request->details[$i]['inventory_stock_adjust_details_product_id'],
                        'inventory_stock_adjust_details_remarks' => $request->details[$i]['inventory_stock_adjust_details_remarks'],
                        'inventory_stock_adjust_rule' => $request->details[$i]['inventory_stock_adjust_rule'],
                        'inventory_stock_adjust_details_unit' => $request->details[$i]['inventory_stock_adjust_details_unit'],
                        'inventory_stock_adjust_details_unit_price' => $request->details[$i]['inventory_stock_adjust_details_unit_price'],
                        'inventory_stock_adjust_details_qty' => $request->details[$i]['inventory_stock_adjust_details_qty'],
                        'inventory_stock_adjust_details_total_price' => $request->details[$i]['inventory_stock_adjust_details_total_price']
                    ];
                }
            }

            if(count($dataFormat) > 0){
                InventoryStockAdjustDetails::insert($dataFormat);
                //$this->CheckStock($dataFormat,$warehouse_id);
            }

            if($app ==1 ){
                $this->approve($id);
            }else{
                DB::table('inventory_stock_adjusts')->where('id', $id)->update(array('approve_status' => $app));
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Stock Damage successfully updated!']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    public function destroy($id)
    {
        try{
            $del_data =InventoryStockAdjust::where('id',$id)->first();
            $del_details_data_array =InventoryStockAdjustDetails::where('inventory_stock_adjust_id',$id)->get();
            foreach($del_details_data_array as $val){
                $rule = $val['inventory_stock_adjust_rule'];
                if($rule =='Inc'){
                    InventoryCurrentStock::where('inventory_current_stocks_product_id',$val['inventory_stock_adjust_details_product_id'])
                        ->where('inventory_current_stocks_warehouse_id',$del_data['inventory_stock_adjusts_warehouse_id'])
                        ->decrement('inventory_stocks_current_qty',  $val['inventory_stock_adjust_details_qty']);
                }else{
                    InventoryCurrentStock::where('inventory_current_stocks_product_id',$val['inventory_stock_adjust_details_product_id'])
                        ->where('inventory_current_stocks_warehouse_id',$del_data['inventory_stock_adjusts_warehouse_id'])
                        ->increment('inventory_stocks_current_qty',  $val['inventory_stock_adjust_details_qty']);
                }
            }


            ///////// stock log Delete //////////////////
            $main_tbl='inventory_stock_adjusts';
            $main_tbl_id=$id;
            $warehouse_id=$del_data['inventory_stock_adjusts_warehouse_id'];
            $where_clause =[
                'stock_transection_logs_table_name' => $main_tbl,
                'stock_transection_logs_table_id' => $main_tbl_id,
                'stock_trans_warehouse_id' => $warehouse_id
            ];
            StockTransectionLog::where($where_clause)->delete();


            //////////// Main data delete ////////
            InventoryStockAdjustDetails::where('inventory_stock_adjust_id',$id)->delete();
            InventoryStockAdjust ::FindOrFail($id)->delete();
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Stock Adjust successfully Deleted !']);
        } elseif ($bug == 1451) {
            return response()->json(['status' => 'error', 'message' => 'This data is used another table,can not delete data']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }








    private function approve($id)
    {
        $appData = InventoryStockAdjust::where('id',$id)->first();

        if($appData->approve_status == 0) {
            $appDetailsData = InventoryStockAdjustDetails::where('adjust_id', $id)->get();

            $date           = $appData->inventory_stock_adjusts_date;
            $log_type       = StockTransactionType::$inventory_adjust;
            $serial_no      = $appData->inventory_stock_adjusts_no;

            $ref_tbl        = '';
            $ref_tbl_id     = '';
            $log_tbl        = 'inventory_stock_adjusts';
            $log_tbl_id     = $id;

            $sup_cus_tabl   = '';
            $supplier       = '';

            $warehouseId    = $appData->inventory_stock_adjusts_warehouse_id;



            /****************************************** INCREASE STOCK INFORMATION  **********************************************************/

            $total_qty_inv=0;
            $total_price_inv=0;

            foreach ($appDetailsData as $val) {
                $rule = $val->adjust_rule;
                if($rule =='Inc'){
                    //current qty
                    InventoryCurrentStock::where('inventory_current_stocks_product_id',$val->inventory_stock_adjust_details_product_id)
                        ->where('inventory_current_stocks_warehouse_id',$warehouseId)
                        ->increment('inventory_stocks_current_qty',  $val->inventory_stock_adjust_details_qty);

                    //current total_price
                    InventoryCurrentStock::where('inventory_current_stocks_product_id', $val->inventory_stock_adjust_details_product_id)
                        ->where('inventory_current_stocks_warehouse_id', $warehouseId)
                        ->increment('total_price',$val->inventory_stock_adjust_details_total_price);


                    /*$Ics=InventoryCurrentStock::where('inventory_current_stocks_product_id', $val->inventory_stock_adjust_details_product_id)
                        ->where('inventory_current_stocks_warehouse_id', $warehouseId)->first();

                    $price_unit=ceil($Ics->total_price / $Ics->inventory_stocks_current_qty);

                    InventoryCurrentStock::where('inventory_current_stocks_product_id', $val->inventory_stock_adjust_details_product_id)
                        ->where('inventory_current_stocks_warehouse_id', $warehouseId)
                        ->update(array('unit_price'=>$price_unit));

                    DB::table('production_products')->where('id', $val->inventory_stock_adjust_details_product_id)
                        ->update(array('buying_price' => $price_unit,'selling_price' => $price_unit));*/

                    $total_qty_inv=$total_qty_inv+$val->inventory_stock_adjust_details_qty;
                    $total_price_inv=$total_price_inv+$val->inventory_stock_adjust_details_total_price;

                }else{
                    //current qty
                    InventoryCurrentStock::where('inventory_current_stocks_product_id',$val->inventory_stock_adjust_details_product_id)
                        ->where('inventory_current_stocks_warehouse_id',$warehouseId)
                        ->decrement('inventory_stocks_current_qty',  $val->inventory_stock_adjust_details_qty);

                    //current total_price
                    InventoryCurrentStock::where('inventory_current_stocks_product_id',$val->inventory_stock_adjust_details_product_id)
                        ->where('inventory_current_stocks_warehouse_id',$warehouseId)
                        ->decrement('total_price',$val->inventory_stock_adjust_details_total_price);

                    /*$Ics=InventoryCurrentStock::where('product_id', $val->product_id)
                        ->where('warehouse_id', $warehouseId)->first();

                    $price_unit=ceil($Ics->total_price / $Ics->current_qty);

                    InventoryCurrentStock::where('product_id', $val->product_id)
                        ->where('warehouse_id', $warehouseId)
                        ->update(array('unit_price'=>$price_unit));

                    DB::table('production_products')->where('id', $val->product_id)
                        ->update(array('buying_price' => $price_unit,'selling_price' => $price_unit));*/

                    $total_qty_inv=$total_qty_inv-$val->inventory_stock_adjust_details_qty;
                    $total_price_inv=$total_price_inv-$val->inventory_stock_adjust_details_total_price;
                }
            }

            /**************************************** STOCK LOG INFO START ********************************************************/
            $stock_log = [
                'stock_transection_logs_date'           => $date,
                'stock_transection_logs_type'           => $log_type,
                'stock_transection_logs_number'         => $serial_no,
                'stock_transection_logs_ref_table_name' => $ref_tbl,
                'stock_transection_logs_ref_table_id'   => $ref_tbl_id,
                'stock_transection_logs_table_name'     => $log_tbl,
                'stock_transection_logs_table_id'       => $log_tbl_id,
                'stock_trans_supp_cus_table_name'       => $sup_cus_tabl,
                'stock_trans_supp_cus_table_id'         => $supplier,
                'stock_trans_warehouse_id'              => $warehouseId,
                'stock_trans_qty'                       => $total_qty_inv,
                'stock_trans_total_price'               => $total_price_inv,


                /*'logs_date'             => $date,
                'logs_type'             => $log_type,
                'logs_number'           => $serial_no,
                'logs_ref_table_name'   => $ref_tbl,
                'logs_ref_table_id'     => $ref_tbl_id,
                'logs_table_name'       => $log_tbl,
                'logs_table_id'         => $id,
                'supp_cus_table_name'   => $sup_cus_tabl,
                'supp_cus_table_id'     => $supplier,
                'warehouse_id'          => $warehouseId,
                'logs_qty'              => $total_qty,
                'logs_total_price'      => $total_price,*/
                'created_by'            => Auth::user()->id,
            ];
            $mainStock = StockTransectionLog::create($stock_log);


            //as $key => $value
            $Stockdetails = [];
            $details_tbl = 'inventory_stock_adjust_details';

            foreach($appDetailsData as $key => $row){
                $w_info = InventoryCurrentStock::where('inventory_current_stocks_warehouse_id', $warehouseId)
                    ->where('inventory_current_stocks_product_id', $row->inventory_stock_adjust_details_product_id)->first();

                $add_sub = '';
                if($row->adjust_rule == 'Inc'){
                    $add_sub = 1;
                }
                else{
                    $add_sub = 0;
                }

                $Stockdetails[] = [
                    'log_id'            => $mainStock->id,
                    'd_table_name'      => $details_tbl,
                    'd_table_id'        => $row->id,
                    'product_id'        => $row->inventory_stock_adjust_details_product_id,
                    'warehouse_id'      => $warehouseId,
                    'entry_qty'         => $row->inventory_stock_adjust_details_qty,
                    'open_qty'          => $w_info->inventory_stocks_open_qty,
                    'current_qty'       => $w_info->inventory_stocks_current_qty,
                    'close_qty'         => '',


                    'log_unit_price'    => $row->inventory_stock_adjust_details_unit_price,
                    'log_total_price'   => $row->inventory_stock_adjust_details_total_price,

                    'add_sub'           => $add_sub,

                    'entry_date'        => $date,
                    'created_by'        => Auth::user()->id,

                ];
            }
            StockTransectionLogDetails::insert($Stockdetails);

            /****************************************** APPROVE INFORMATION  **********************************************************/
            DB::table('inventory_stock_adjusts')->where('id', $id)->update(array(
                'approve_status' => 1,
                'approve_by'     => Auth::user()->id,
                'approve_at'     => Carbon::now(),
            ));

        }
    }




    private function StockAdjustGenerate(){
        $id = InventoryStockAdjust::withTrashed()->count();
        $currentId = $id+1;
        return 'STADJ'.date('Y').date('m').$currentId;
    }

    private function EnumVal(){
        $table='inventory_stock_adjust_details';
        $name='inventory_stock_adjust_rule';
        $type = DB::select( DB::raw('SHOW COLUMNS FROM '.$table.' WHERE Field = "'.$name.'"') )[0]->Type;
        preg_match('/^enum\((.*)\)$/', $type, $matches);
        $enum = array();
        foreach(explode(',', $matches[1]) as $value){
            $v = trim( $value, "'" );
            $enum[] = $v;
        }
        return $enum;
    }

    private function dataFormat($request, $id)
    {
        $dataFormat = [];
        $count = count($request->details);
        for ($i = 0; $i < $count; $i++) {
            $dataFormat[$i] = [
                'inventory_stock_adjust_id'                     => $id,
                'inventory_stock_adjust_details_product_id'     => $request->details[$i]['inventory_stock_adjust_details_product_id'],
                'inventory_stock_adjust_details_remarks'        => $request->details[$i]['inventory_stock_adjust_details_remarks'],
                'inventory_stock_adjust_rule'                   => $request->details[$i]['inventory_stock_adjust_rule'],
                'inventory_stock_adjust_details_unit'           => $request->details[$i]['inventory_stock_adjust_details_unit'],
                'inventory_stock_adjust_details_unit_price'     => $request->details[$i]['inventory_stock_adjust_details_unit_price'],
                'inventory_stock_adjust_details_qty'            => $request->details[$i]['inventory_stock_adjust_details_qty'],
                'inventory_stock_adjust_details_total_price'    => $request->details[$i]['inventory_stock_adjust_details_total_price']
            ];
        }
        return $dataFormat;
    }


    private function CheckStock($ProductArray,$warehouseId){
        foreach($ProductArray as $val){
            $rule = $val['inventory_stock_adjust_rule'];
            if($rule =='Inc'){
                InventoryCurrentStock::where('inventory_current_stocks_product_id',$val['inventory_stock_adjust_details_product_id'])
                    ->where('inventory_current_stocks_warehouse_id',$warehouseId)
                    ->increment('inventory_stocks_current_qty',  $val['inventory_stock_adjust_details_qty']);
            }else{
                InventoryCurrentStock::where('inventory_current_stocks_product_id',$val['inventory_stock_adjust_details_product_id'])
                    ->where('inventory_current_stocks_warehouse_id',$warehouseId)
                    ->decrement('inventory_stocks_current_qty',  $val['inventory_stock_adjust_details_qty']);
            }
        }

    }

}
