<?php

namespace App\Http\Controllers\Inventory;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Model\Production\ProductionProducts;
use App\Model\Purchase\PurchaseWareHouse;

use App\Model\Inventory\InventoryProductDamage;
use App\Model\Inventory\InventoryProductDamageDetails;


use App\Model\Inventory\InventoryCurrentStock;
use App\Model\Inventory\StockTransectionLog;
use App\Model\Inventory\StockTransectionLogDetails;

use App\Lib\Enumerations\StockTransactionType;

use Illuminate\Support\Facades\Auth;
use DB;
use Carbon\Carbon;

class __InventoryProductDamageController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            if ($request->token_product_damage_gen==1) {
                return  $this->ProductDamageGenerate();
            }
            $query = DB::table('inventory_product_damages')
                ->leftJoin('purchase_ware_houses', 'inventory_product_damages.inventory_product_damages_warehouse_id', '=', 'purchase_ware_houses.id')
                ->whereNull('inventory_product_damages.deleted_at')
                ->select(['inventory_product_damages.id AS id',
                    'inventory_product_damages.inventory_product_damages_no',
                    'inventory_product_damages.inventory_product_damages_date',
                    'inventory_product_damages.inventory_product_damages_narration',
                    'inventory_product_damages.approve_status',
                    'purchase_ware_houses.purchase_ware_houses_name',

                ]);

            return datatables()->of($query)->toJson();
        }

        $productLists   = ProductionProducts::Select('production_products.*', 'production_measure_units.measure_unit')
            ->where('production_products.status', '1')
            ->leftJoin('production_measure_units', 'production_products.measure_unit_id', '=', 'production_measure_units.id')
            ->get();
        $warehouseLists   = PurchaseWareHouse::where('status', '1')->get();
        return view('inventory.inventory_product_damage',compact('productLists','warehouseLists'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'inventory_product_damages_no' => 'required',
            'inventory_product_damages_date' => 'required',
            'inventory_product_damages_warehouse_id' => 'required',

            'details.*.inventory_product_damage_details_product_id' => 'required',
            'details.*.inventory_product_damage_details_unit_price' => 'required',
            'details.*.inventory_product_damage_details_qty' => 'required',
        ], [
            'details.*.inventory_product_damage_details_product_id.required' => 'Required field.',
            'details.*.inventory_product_damage_details_unit_price.required' => 'Required field.',
            'details.*.inventory_product_damage_details_qty.required' => 'Required field.',
        ]);

        date_default_timezone_set("Asia/Dhaka");
        $date = str_replace('/', '-', $request->inventory_product_damages_date);
        $date_val =date('Y-m-d', strtotime($date));
        $data = [
            'inventory_product_damages_no' => $request->inventory_product_damages_no,
            'inventory_product_damages_date' => $date_val,
            'inventory_product_damages_warehouse_id' => $request->inventory_product_damages_warehouse_id,
            'inventory_product_damages_narration' => $request->inventory_product_damages_narration,
            'created_by' => Auth::user()->id,
        ];

        //dd($data);
        try {
            DB::beginTransaction();

            $result = InventoryProductDamage::create($data);
            $details = $this->dataFormat($request, $result->id);
            InventoryProductDamageDetails::insert($details);

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Product Damage successfully saved!']);
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
            $editModeData = InventoryProductDamage::FindOrFail($id);
            $editModeDetailsData = InventoryProductDamageDetails::where('inventory_product_damage_id',$id)->get();
            $data = [
                'id'    => $editModeData->id,
                'inventory_product_damages_no' => $editModeData->inventory_product_damages_no,
                'inventory_product_damages_date' => date('d/m/Y', strtotime($editModeData->inventory_product_damages_date)),
                'inventory_product_damages_warehouse_id' => $editModeData->inventory_product_damages_warehouse_id,
                'inventory_product_damages_narration' => $editModeData->inventory_product_damages_narration,
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
        $this->validate($request, [
            'inventory_product_damages_no' => 'required',
            'inventory_product_damages_date' => 'required',
            'inventory_product_damages_warehouse_id' => 'required',

            'details.*.inventory_product_damage_details_product_id' => 'required',
            'details.*.inventory_product_damage_details_unit_price' => 'required',
            'details.*.inventory_product_damage_details_qty' => 'required',
        ], [
            'details.*.inventory_product_damage_details_product_id.required' => 'Required field.',
            'details.*.inventory_product_damage_details_unit_price.required' => 'Required field.',
            'details.*.inventory_product_damage_details_qty.required' => 'Required field.',
        ]);

        date_default_timezone_set("Asia/Dhaka");
        $date = str_replace('/', '-', $request->inventory_product_damages_date);
        $date_val =date('Y-m-d', strtotime($date));
        $data = [
            'inventory_product_damages_no' => $request->inventory_product_damages_no,
            'inventory_product_damages_date' => $date_val,
            'inventory_product_damages_warehouse_id' => $request->inventory_product_damages_warehouse_id,
            'inventory_product_damages_narration' => $request->inventory_product_damages_narration,
            'updated_by' => Auth::user()->id,
        ];

        try {
            DB::beginTransaction();

            $editModeData = InventoryProductDamage::FindOrFail($id);
            $editModeData->update($data);


            /////// Stock log /////////
            $type=5; //For Pr damage
            $sl=$request->inventory_product_damages_no;
            $ref_tbl = '';
            $ref_tbl_id = 0;
            $main_tbl='inventory_product_damages';
            $main_tbl_id=$id;
            $supplier_tbl='';
            $supplier_tbl_id= 0;
            $warehouse_id=$request->inventory_product_damages_warehouse_id;
            $stock_log =$this->stockLog($date_val,$type,$sl,$ref_tbl,$ref_tbl_id, $main_tbl,$main_tbl_id,$supplier_tbl,$supplier_tbl_id,$warehouse_id);
            $where_clause =[
                'stock_transection_logs_table_name' => $main_tbl,
                'stock_transection_logs_table_id' => $main_tbl_id,
                'stock_trans_warehouse_id' => $warehouse_id
            ];
            StockTransectionLog::where($where_clause)->update($stock_log);


            //// details transection ////
            if (count($request->deleteID) > 0) {
                $deldata_array =InventoryProductDamageDetails::whereIn('id', $request->deleteID)->get();
                foreach($deldata_array as $val){
                    InventoryCurrentStock::where('inventory_current_stocks_product_id',$val['inventory_product_damage_details_product_id'])
                        ->where('inventory_current_stocks_warehouse_id',$warehouse_id)
                        ->increment('inventory_stocks_current_qty',  $val['inventory_product_damage_details_qty']);

                }
                InventoryProductDamageDetails::whereIn('id', $request->deleteID)->delete();
            }

            $dataFormat = [];
            $count = count($request->details);
            for ($i = 0; $i < $count; $i++) {
                if (isset($request->details[$i]['id']) && $request->details[$i]['id'] !='') {
                    $pervquantity=InventoryProductDamageDetails::where('inventory_product_damage_details_product_id', $request->details[$i]['inventory_product_damage_details_product_id'])
                        ->first();
                    InventoryCurrentStock::where('inventory_current_stocks_product_id',$pervquantity['inventory_product_damage_details_product_id'])
                        ->where('inventory_current_stocks_warehouse_id',$warehouse_id)
                        ->increment('inventory_stocks_current_qty',  $pervquantity['inventory_product_damage_details_qty']);

                    $updateData = [
                        'inventory_product_damage_id' => $id,
                        'inventory_product_damage_details_product_id' => $request->details[$i]['inventory_product_damage_details_product_id'],
                        'inventory_product_damage_details_remarks' => $request->details[$i]['inventory_product_damage_details_remarks'],
                        'inventory_product_damage_details_unit' => $request->details[$i]['inventory_product_damage_details_unit'],
                        'inventory_product_damage_details_unit_price' => $request->details[$i]['inventory_product_damage_details_unit_price'],
                        'inventory_product_damage_details_qty' => $request->details[$i]['inventory_product_damage_details_qty'],
                        'inventory_product_damage_details_total_price' => $request->details[$i]['inventory_product_damage_details_total_price']
                    ];
                    InventoryCurrentStock::where('inventory_current_stocks_product_id',$request->details[$i]['inventory_product_damage_details_product_id'])
                        ->where('inventory_current_stocks_warehouse_id',$warehouse_id)
                        ->decrement('inventory_stocks_current_qty',  $request->details[$i]['inventory_product_damage_details_qty']);

                    InventoryProductDamageDetails::where('id', $request->details[$i]['id'])->update($updateData);
                } else {
                    $dataFormat[$i] =[
                        'inventory_product_damage_id' => $id,
                        'inventory_product_damage_details_product_id' => $request->details[$i]['inventory_product_damage_details_product_id'],
                        'inventory_product_damage_details_remarks' => $request->details[$i]['inventory_product_damage_details_remarks'],
                        'inventory_product_damage_details_unit' => $request->details[$i]['inventory_product_damage_details_unit'],
                        'inventory_product_damage_details_unit_price' => $request->details[$i]['inventory_product_damage_details_unit_price'],
                        'inventory_product_damage_details_qty' => $request->details[$i]['inventory_product_damage_details_qty'],
                        'inventory_product_damage_details_total_price' => $request->details[$i]['inventory_product_damage_details_total_price']
                    ];
                }
            }

            if(count($dataFormat) > 0){
                InventoryProductDamageDetails::insert($dataFormat);
                $this->CheckStock($dataFormat,$warehouse_id);
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Product Damage successfully updated!']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    public function destroy($id)
    {
        try{
            $del_data =InventoryProductDamage::where('id',$id)->first();
            $del_details_data_array =InventoryProductDamageDetails::where('inventory_product_damages_id',$id)->get();
            foreach($del_details_data_array as $val){
                InventoryCurrentStock::where('inventory_current_stocks_product_id',$val['inventory_product_damage_details_product_id'])
                    ->where('inventory_current_stocks_warehouse_id',$del_data['inventory_product_damages_warehouse_id'])
                    ->increment('inventory_stocks_current_qty',  $val['inventory_product_damage_details_qty']);
            }

            ///////// stock log Delete //////////////////
            $main_tbl='inventory_product_damages';
            $main_tbl_id=$id;
            $warehouse_id=$del_data['inventory_product_damages_warehouse_id'];
            $where_clause =[
                'stock_transection_logs_table_name' => $main_tbl,
                'stock_transection_logs_table_id' => $main_tbl_id,
                'stock_trans_warehouse_id' => $warehouse_id
            ];
            StockTransectionLog::where($where_clause)->delete();

            InventoryProductDamageDetails::where('inventory_product_damages_id',$id)->delete();
            InventoryProductDamage ::FindOrFail($id)->delete();
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Product Damage successfully Deleted !']);
        } elseif ($bug == 1451) {
            return response()->json(['status' => 'error', 'message' => 'This data is used another table,can not delete data']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    private function ProductDamageGenerate(){
        $id = InventoryProductDamage::withTrashed()->count();
        $currentId = $id+1;
        return 'PD'.date('Ym').$currentId;
    }

    private function dataFormat($request, $id)
    {
        $dataFormat = [];
        $count = count($request->details);
        for ($i = 0; $i < $count; $i++) {
            $dataFormat[$i] = [
                'inventory_product_damage_id' => $id,
                'inventory_product_damage_details_product_id' => $request->details[$i]['inventory_product_damage_details_product_id'],
                'inventory_product_damage_details_remarks' => $request->details[$i]['inventory_product_damage_details_remarks'],
                'inventory_product_damage_details_unit' => $request->details[$i]['inventory_product_damage_details_unit'],
                'inventory_product_damage_details_unit_price' => $request->details[$i]['inventory_product_damage_details_unit_price'],
                'inventory_product_damage_details_qty' => $request->details[$i]['inventory_product_damage_details_qty'],
                'inventory_product_damage_details_total_price' => $request->details[$i]['inventory_product_damage_details_total_price']
            ];
        }
        return $dataFormat;
    }



    private function stockLog($date_val,$type,$sl,$ref_tbl,$ref_tbl_id, $main_tbl,$main_tbl_id,$supplier_tbl,$supplier_tbl_id,$warehouse_id){
        $data_trans_log = [];
        $data_trans_log =[
            'stock_transection_logs_date' => $date_val,
            'stock_transection_logs_type' => $type,//'0-pr-entry, 1-pr-return,2-delivery-challan, 3 -delicery return 4- stock adjust, 5 -Pr dmg'
            'stock_transection_logs_number' => $sl,//$sl,
            'stock_transection_logs_ref_table_name' => $ref_tbl,
            'stock_transection_logs_ref_table_id' => $ref_tbl_id,
            'stock_transection_logs_table_name' => $main_tbl,
            'stock_transection_logs_table_id' => $main_tbl_id,
            'stock_trans_supp_cus_table_name' => $supplier_tbl,
            'stock_trans_supp_cus_table_id' => $supplier_tbl_id,
            'stock_trans_warehouse_id' => $warehouse_id,
        ];

        return $data_trans_log;
    }

    private function CheckStock($ProductArray,$warehouseId){
        foreach($ProductArray as $val){
            InventoryCurrentStock::where('inventory_current_stocks_product_id',$val['inventory_product_damage_details_product_id'])
                    ->where('inventory_current_stocks_warehouse_id',$warehouseId)
                    ->decrement('inventory_stocks_current_qty',  $val['inventory_product_damage_details_qty']);

        }

    }

}
