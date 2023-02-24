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

class InventoryProductDamageController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            if ($request->token_product_damage_gen==1) {
                return  $this->ProductDamageGenerate();
            }
            $query = DB::table('inventory_product_damages')
                ->leftJoin('purchase_ware_houses', 'inventory_product_damages.inventory_product_damages_warehouse_id', '=', 'purchase_ware_houses.id')
                ->leftJoin('users as ura', 'inventory_product_damages.created_by','=','ura.id')
                ->leftJoin('users as ured', 'inventory_product_damages.updated_by','=','ured.id')
                ->leftJoin('users as urea', 'inventory_product_damages.approve_by','=','urea.id')
                ->whereNull('inventory_product_damages.deleted_at')
                ->select(['inventory_product_damages.id AS id',
                    'inventory_product_damages.inventory_product_damages_no',
                    'inventory_product_damages.inventory_product_damages_date',
                    'inventory_product_damages.inventory_product_damages_narration',
                    'inventory_product_damages.approve_status',
                    'purchase_ware_houses.purchase_ware_houses_name',
                    'inventory_product_damages.created_by',
                    'inventory_product_damages.updated_by',
                    'inventory_product_damages.approve_by',
                    'inventory_product_damages.approve_status',
                    'ura.user_name as ad_name',
                    'ured.user_name as ed_name',
                    'urea.user_name as ap_name'

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



    public function store(Request $request)
    {
        date_default_timezone_set("Asia/Dhaka");

        $inv_no_like = 'PD';
        $rtn_val =InvStore($request->inventory_product_damages_no, $inv_no_like,
            'inventory_product_damages','inventory_product_damages_no');

        $request->merge(['inventory_product_damages_no' => $rtn_val]) ;


        $this->validate($request, [
            'inventory_product_damages_no' => 'required',
            'inventory_product_damages_date' => 'required',
            'inventory_product_damages_warehouse_id' => 'required',

            'details.*.inventory_product_damage_details_product_id' => 'required',
            'details.*.inventory_product_damage_details_qty' => 'required',
        ], [
            'details.*.inventory_product_damage_details_product_id.required' => 'Required field.',
            'details.*.inventory_product_damage_details_qty.required' => 'Required field.',
        ]);


        $date = str_replace('/', '-', $request->inventory_product_damages_date);
        $date_val =date('Y-m-d', strtotime($date));
        $app            = $request->approve;
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

            if($app ==1){
                $this->approve($result->id);

                if($request->updated_by == Null){
                    DB::table('inventory_product_damages')->where('id', $result->id)->update(array('updated_by' => Auth::user()->id));
                }

            }else{
                DB::table('inventory_product_damages')->where('id', $result->id)->update(array('approve_status' => $app));
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => "Product Damage # " . $request->inventory_product_damages_no .' Successfully Saved!']);

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
                'approve' => $editModeData->approve_status,
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
            'inventory_product_damages_no' => 'required',
            'inventory_product_damages_date' => 'required',
            'inventory_product_damages_warehouse_id' => 'required',

            'details.*.inventory_product_damage_details_product_id' => 'required',
            'details.*.inventory_product_damage_details_qty' => 'required',
        ], [
            'details.*.inventory_product_damage_details_product_id.required' => 'Required field.',
            'details.*.inventory_product_damage_details_qty.required' => 'Required field.',
        ]);


        $date = str_replace('/', '-', $request->inventory_product_damages_date);
        $date_val =date('Y-m-d', strtotime($date));
        $warehouse_id   = $request->inventory_product_damages_warehouse_id;
        $app            = $request->approve;

        try {
            DB::beginTransaction();

            $editModeData = InventoryProductDamage::FindOrFail($id);

            $editModeData->inventory_product_damages_no               = $request->inventory_product_damages_no;
            $editModeData->inventory_product_damages_date             = $date_val;
            $editModeData->inventory_product_damages_warehouse_id     = $warehouse_id;
            $editModeData->inventory_product_damages_narration        = $request->inventory_product_damages_narration;
            if($app !=1){
                $editModeData->updated_by  = Auth::user()->id;
            }

            $editModeData->save();

            //// details transection ////
            if (count($request->deleteID) > 0) {
                InventoryProductDamageDetails::whereIn('id', $request->deleteID)->delete();
            }

            $dataFormat = [];
            $count = count($request->details);
            for ($i = 0; $i < $count; $i++) {
                if (isset($request->details[$i]['id']) && $request->details[$i]['id'] !='') {
                    $updateData = [
                        'inventory_product_damage_id' => $id,
                        'inventory_product_damage_details_product_id' => $request->details[$i]['inventory_product_damage_details_product_id'],
                        'inventory_product_damage_details_remarks' => $request->details[$i]['inventory_product_damage_details_remarks'],
                        'inventory_product_damage_details_unit' => $request->details[$i]['inventory_product_damage_details_unit'],
                        'inventory_product_damage_details_qty' => $request->details[$i]['inventory_product_damage_details_qty'],
                    ];
                    InventoryProductDamageDetails::where('id', $request->details[$i]['id'])->update($updateData);
                } else {
                    $dataFormat[$i] =[
                        'inventory_product_damage_id' => $id,
                        'inventory_product_damage_details_product_id' => $request->details[$i]['inventory_product_damage_details_product_id'],
                        'inventory_product_damage_details_remarks' => $request->details[$i]['inventory_product_damage_details_remarks'],
                        'inventory_product_damage_details_unit' => $request->details[$i]['inventory_product_damage_details_unit'],
                        'inventory_product_damage_details_qty' => $request->details[$i]['inventory_product_damage_details_qty'],
                    ];
                }
            }

            if(count($dataFormat) > 0){
                InventoryProductDamageDetails::insert($dataFormat);

            }

            if($app ==1 ){
                $this->approve($id);
            }else{
                DB::table('inventory_product_damages')->where('id', $id)->update(array('approve_status' => $app));
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Product Damage successfully updated!']);
        } catch (\Exception $e) {
            echo $e->getMessage();
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    public function destroy($id)
    {
        try{
            InventoryProductDamageDetails::where('inventory_product_damage_id',$id)->delete();
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

    private function approve($id)
    {
        $appData = InventoryProductDamage::where('id',$id)->first();

        if($appData->approve_status == 0) {
            $appDetailsData = InventoryProductDamageDetails::where('inventory_product_damage_id', $id)->get();

            $date           = $appData->inventory_product_damages_date;
            $log_type       = StockTransactionType::$inventory_damage;
            $serial_no      = $appData->inventory_product_damages_no;

            $ref_tbl        = '';
            $ref_tbl_id     = '';
            $log_tbl        = 'inventory_product_damages';
            $log_tbl_id     = $id;

            $sup_cus_tabl   = '';
            $supplier       = '';

            $warehouseId    = $appData->inventory_product_damages_warehouse_id;



            /****************************************** INCREASE STOCK INFORMATION  **********************************************************/

            $total_qty_inv=0;
            $total_price_inv=0;

            foreach ($appDetailsData as $val) {
                    //current qty
                    InventoryCurrentStock::where('inventory_current_stocks_product_id',$val->inventory_product_damage_details_product_id)
                        ->where('inventory_current_stocks_warehouse_id',$warehouseId)
                        ->decrement('inventory_stocks_current_qty',  $val->inventory_product_damage_details_qty);

                    $Ics=InventoryCurrentStock::where('inventory_current_stocks_product_id', $val->inventory_product_damage_details_product_id)
                        ->where('inventory_current_stocks_warehouse_id', $warehouseId)->first();
                    $total_price= $Ics->unit_price * $Ics->inventory_stocks_current_qty;

                    InventoryCurrentStock::where('inventory_current_stocks_product_id', $val->inventory_product_damage_details_product_id)
                        ->where('inventory_current_stocks_warehouse_id', $warehouseId)
                        ->update(array('total_price'=>$total_price));

                    $total_qty_inv=$total_qty_inv+$val->inventory_product_damage_details_qty;
                    $total_price_inv=$total_price_inv+$Ics->unit_price;

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
                'created_by'            => Auth::user()->id,
            ];
            $mainStock = StockTransectionLog::create($stock_log);


            //as $key => $value
            $Stockdetails = [];
            $details_tbl = 'inventory_product_damage_details';

            foreach($appDetailsData as $key => $row){
                $w_info = InventoryCurrentStock::where('inventory_current_stocks_warehouse_id', $warehouseId)
                    ->where('inventory_current_stocks_product_id', $row->inventory_product_damage_details_product_id)->first();

                $add_sub = '0';

                $Stockdetails[] = [
                    'log_id'            => $mainStock->id,

                    'log_table_name'    => $details_tbl,
                    'log_table_id'      => $row->id,
                    'product_id'        => $row->inventory_product_damage_details_product_id,
                    'warehouse_id'      => $warehouseId,

                    'log_entry_qty'     => $row->inventory_product_damage_details_qty,
                    'log_open_qty'      => $w_info->inventory_stocks_open_qty,
                    'log_current_qty'   => $w_info->inventory_stocks_current_qty,
                    'log_close_qty'     => '',

                    'log_unit_price'    => $w_info->unit_price,
                    'log_total_price'   => $w_info->total_price,

                    'add_sub'           => $add_sub,

                    'entry_date'        => $date,
                    'created_by'        => Auth::user()->id,

                ];
            }
            StockTransectionLogDetails::insert($Stockdetails);

            /****************************************** APPROVE INFORMATION  **********************************************************/
            DB::table('inventory_product_damages')->where('id', $id)->update(array(
                'approve_status' => 1,
                'approve_by'     => Auth::user()->id,
                'approve_at'     => Carbon::now(),
            ));

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
                'inventory_product_damage_details_qty' => $request->details[$i]['inventory_product_damage_details_qty'],
            ];
        }
        return $dataFormat;
    }

}
