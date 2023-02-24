<?php

namespace App\Http\Controllers\Production;

use App\Lib\Enumerations\StockTransactionType;
use App\Model\CompanyInfo;
use App\Model\Inventory\StockTransectionLog;
use App\Model\Inventory\StockTransectionLogDetails;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use App\Model\Production\ProductionPackingDetails;
use App\Model\Purchase\PurchaseWareHouse;
use App\Model\Production\ProductionPacking;
use App\Model\Production\ProductionProducts;
use App\Model\Production\ProductionInventoryMaterial;

use App\Model\Production\ProductionCurrentStock;
use App\Model\Inventory\InventoryCurrentStock;

use App\Model\Production\ProductionAssembling;
use DB;

class PackingSectionController extends Controller
{
    public function index(Request $request)
    {
        if ($request->filled('token_last_price') && $request->token_last_price == 'yes') {
            return $this->getInventoryOrSemiFinishedLastPrice($request->product_id);
        }

        if ($request->ajax()) {

            $query = DB::table('production_packings')
                ->leftJoin('production_products', 'production_packings.finishing_product_id', '=', 'production_products.id')
                ->leftJoin('users as ura', 'production_packings.created_by','=','ura.id')
                ->leftJoin('users as ured', 'production_packings.updated_by','=','ured.id')
                ->leftJoin('users as urea', 'production_packings.approve_by','=','urea.id')
                ->whereNull('production_packings.deleted_at')
                ->select(['production_packings.id AS id',
                    'production_packings.packing_no',
                    'production_packings.packing_date',
                    'production_packings.narration',
                    'production_packings.trans_to_store_qty',
                    'production_packings.total_price',
                    'production_packings.created_by',
                    'production_packings.updated_by',
                    'production_packings.approve_by',
                    'production_packings.approve_status',
                    'production_products.product_name',
                    'ura.user_name as ad_name',
                    'ured.user_name as ed_name',
                    'urea.user_name as ap_name'
                ]);

            return datatables()->of($query)->toJson();
        }

        /*-------- PDF Generate-----------------*/
        if ($request->filled('export')) {

            $file_type = $request->export;

            $data = $this->getPdfData($request->id);

            if ($file_type=='pdf') {

                $this->exportAsPdf($data);

            }else {
                dd('Something went wrong.');
            }
        }

        /*-------- PDF Generate End-----------------*/

        $finishProductList   = ProductionProducts::where('status', '1')
                                    ->where('is_raw_material_status', '0')
                                    ->select('id','product_name')
                                    ->get();
        $productList  = ProductionProducts::select('production_products.id','production_products.product_name','production_current_stocks.packing_current_qty','production_current_stocks.packing_receive_qty')
                        ->leftJoin('production_current_stocks', 'production_current_stocks.product_id', '=', 'production_products.id')
                        ->where('production_products.status', '1')
                        ->get();
        $warehouse   = PurchaseWareHouse::where('status', '1')->get();

        return view('production.production_section.packing_section',compact('finishProductList', 'productList' ,'warehouse'));
    }

    public function packingNoGenerate(){
        $id = ProductionPacking::withTrashed()->count();
        $currentId = $id+1;
        return 'PCK-'.date('Ym').$currentId;
    }

    public function approve($id)
    {
        $appData = ProductionPacking::where('id', $id)->first();
        if ($appData->approve_status == 0) {

            $this->incrementDecrementPackingQTY($id);

            $inv_details = ProductionInventoryMaterial::where('packing_section_id', $id)->get();
            $this->decrementInventoryQTY($appData, $inv_details);

            $appData->approve_status = 1;
            $appData->approve_by = Auth::user()->id;
            $appData->approve_at = Carbon::now();
            $appData->save();
        }
    }

    private function incrementDecrementPackingQTY($PackedId){

        $appData = ProductionPacking::where('id', $PackedId)->first();

        $warehouseId            = $appData->warehouse_id;

        /* @ Inventory Section [Increament] - Finished Product */
        if ($appData->packing_types == 'Complete') {

            $finishing_product_id   = $appData->finishing_product_id;
            $trans_to_store_qty   = $appData->trans_to_store_qty;

            $checkExists_finsish = InventoryCurrentStock::where('inventory_current_stocks_product_id',$finishing_product_id)
                                      ->where('inventory_current_stocks_warehouse_id',$warehouseId)
                                      ->first();

            if($checkExists_finsish){
                InventoryCurrentStock::where('inventory_current_stocks_product_id', $finishing_product_id)
                    ->where('inventory_current_stocks_warehouse_id', $warehouseId)
                    ->increment('inventory_stocks_current_qty', $trans_to_store_qty);

                InventoryCurrentStock::where('inventory_current_stocks_product_id', $finishing_product_id)
                    ->where('inventory_current_stocks_warehouse_id', $warehouseId)
                    ->increment('total_price', $appData->total_price);

                //Calculate New Avg Inventory Unit Pricing
                $new_total_qty      = $checkExists_finsish->inventory_stocks_current_qty + $trans_to_store_qty;
                $new_total_price    = $checkExists_finsish->total_price + $appData->total_price;
                $new_avg_unit_price = $new_total_qty != 0 ? round( $new_total_price / $new_total_qty, 2) : 0;

                InventoryCurrentStock::where('inventory_current_stocks_product_id', $finishing_product_id)
                    ->where('inventory_current_stocks_warehouse_id', $warehouseId)
                    ->update(array(
                        'unit_price'    => $new_avg_unit_price,
                    ));
            }else{
                $finsishQty= [
                    'inventory_current_stocks_product_id'   => $finishing_product_id,
                    'inventory_current_stocks_warehouse_id' => $warehouseId,
                    'inventory_stocks_current_qty'          => $trans_to_store_qty,

                    'unit_price'                            => $appData->unit_price,
                    'total_price'                           => $appData->total_price,
                    'created_by'                            => Auth::user()->id,
                ];
                InventoryCurrentStock::create($finsishQty);
            }
        }

        /* @ Details - Production Section */
        $appDetailsData = ProductionPackingDetails::where('packing_section_id', $PackedId)->get();

        foreach($appDetailsData as $val) {

            $checkProductionExists = ProductionCurrentStock::where('product_id',$val['product_id'])->first();
                if($checkProductionExists){

                    ProductionCurrentStock::where('product_id', $val['product_id'])
                        ->update(
                            [ 'packing_current_qty' => $val['remain_qty']]
                        );
                    ProductionCurrentStock::where('product_id', $val['product_id'])
                        ->decrement('packing_receive_qty', $val['receive_qty']);
                    // ProductionCurrentStock::where('product_id', $val['product_id'])
                        // ->increment('assembling_receive_qty', $val['trans_to_store_qty']); //trans to Assembling

                }else{
                    $testingQty= [
                        'product_id' => $val['product_id'],
                        'packing_current_qty' => $val['remain_qty'],
                        'packing_receive_qty' => $val['receive_qty'],
                        // 'assembling_receive_qty' => $val['trans_to_store_qty'], //trans to Assembling
                    ];
                    ProductionCurrentStock::insert($testingQty);
                }

            $this->InventoryStockLog($appData);

        }


    }

    private function decrementInventoryQTY($appData, $inv_details)
    {
        $a=0;
        $warehouseId = $appData->warehouse_id;
        /* @ Inventory Section [Decrement] */
        foreach($inv_details as $val) {
              $checkExists_ics = InventoryCurrentStock::where('inventory_current_stocks_product_id',$val['product_id'])
                  ->where('inventory_current_stocks_warehouse_id',$warehouseId)
                  ->first();

              if($checkExists_ics){
                  InventoryCurrentStock::where('inventory_current_stocks_product_id', $val['product_id'])
                      ->where('inventory_current_stocks_warehouse_id', $warehouseId)
                      ->decrement('inventory_stocks_current_qty', $val['qty']);

                  InventoryCurrentStock::where('inventory_current_stocks_product_id', $val['product_id'])
                      ->where('inventory_current_stocks_warehouse_id', $warehouseId)
                      ->decrement('total_price', $val['total_price']);

                  $stock_log = [
                      'stock_transection_logs_date' => $appData->packing_date,
                      'is_in_out' => 2,
                      'stock_transection_logs_type' => StockTransactionType::$prodution_packing,
                      'stock_transection_logs_number' => $appData->packing_no,
                      'stock_transection_logs_ref_table_name' => "production_inventory_materials",
                      'stock_transection_logs_ref_table_id' => $appData->id,
                      'stock_transection_logs_table_name' => "production_inventory_materials",
                      'stock_transection_logs_table_id' => $appData->id,
                      'stock_trans_supp_cus_table_name' => NUll,
                      'stock_trans_supp_cus_table_id' => NUll,
                      'stock_trans_warehouse_id' => $warehouseId,
                      'stock_trans_qty' => 0,
                      'created_by' => Auth::user()->id,
                      'stock_trans_total_price' => $appData->total_rm_price,
                  ];
                  $mainStock = StockTransectionLog::create($stock_log);

                  $Stockdetails[$a] = [
                      'log_id'            => $mainStock->id,
                      'is_in_out'         => 2,
                      'log_table_name'    => 'production_inventory_materials',
                      'log_table_id'      => $val['id'],
                      'product_id'        => $val['product_id'],
                      'warehouse_id'      => $warehouseId,
                      'log_entry_qty'     => $val['qty'],
                      'log_open_qty'      => $checkExists_ics->inventory_stocks_current_qty,
                      'log_current_qty'   => $checkExists_ics->inventory_stocks_current_qty - $val['qty'],
                      'log_close_qty'     => $checkExists_ics->inventory_stocks_current_qty - $val['qty'],
                      'entry_date'        => $appData->packing_date,
                      'created_by'        => Auth::user()->id,
                      'log_unit_price'    => $val['unit_price'],
                      'log_total_price'   => $val['total_price'],
                  ];

                  $a++;

              }
        }
        if (isset($Stockdetails))
            StockTransectionLogDetails::insert($Stockdetails);
    }

    private function InventoryStockLog($appData)
    {
        /**************************************** STOCK LOG INFO START ********************************************************/
        $stock_log = [
            'stock_transection_logs_date' => $appData->packing_date,
            'is_in_out' => 1,
            'stock_transection_logs_type' => StockTransactionType::$prodution_packing,
            'stock_transection_logs_number' => $appData->packing_no,
            'stock_transection_logs_ref_table_name' => "production_packings",
            'stock_transection_logs_ref_table_id' => $appData->id,
            'stock_transection_logs_table_name' => "production_packings",
            'stock_transection_logs_table_id' => $appData->id,
            'stock_trans_supp_cus_table_name' => NUll,
            'stock_trans_supp_cus_table_id' => NUll,
            'stock_trans_warehouse_id' => $appData->warehouse_id,
            'stock_trans_qty' => $appData->trans_to_store_qty,
            'created_by' => Auth::user()->id,
            'stock_trans_total_price' => $appData->total_rm_price,
        ];
        $mainStock = StockTransectionLog::create($stock_log);


        $Stockdetails[] = '';
        $details_tbl = 'production_packing_details';
        $appDetailsData = ProductionPackingDetails::where('packing_section_id', $appData->id)->get();
        $a=0;
        foreach($appDetailsData as $row)
        {
            $ICS = InventoryCurrentStock::where('inventory_current_stocks_product_id',$row->product_id)->first()->inventory_stocks_current_qty;

            $Stockdetails[$a] = [
                'log_id'            => $mainStock->id,
                'is_in_out'         => 1,
                'log_table_name'    => $details_tbl,
                'log_table_id'      => $row->id,
                'product_id'        => $row->product_id,
                'warehouse_id'      => $appData->warehouse_id,
                'log_entry_qty'     => $row->total_used_qty,
                'log_open_qty'      => $ICS - $row->total_used_qty,
                'log_current_qty'   => $ICS,
                'log_close_qty'     => $ICS,
                'entry_date'        => $appData->packing_date,
                'created_by'        => Auth::user()->id,
                'log_unit_price'    => $row->unit_price,
                'log_total_price'   => $row->total_price,
            ];
            $a++;
        }
        StockTransectionLogDetails::insert($Stockdetails);
    }

    public function store(Request $request)
    {
        $inv_no_like = 'PCK-';
        $rtn_val =InvStore($request->packing_no, $inv_no_like,
            'production_packings','packing_no');

        $request->merge(['packing_no' => $rtn_val]) ;

        $this->validate($request, [
            'packing_no' => 'required',
            'packing_date' => 'required',
            'warehouse_id' => 'required',
            'packing_types' => 'required',
            'total_rm_qty' => 'required',
            'total_rm_price' => 'required',

            'details.*.product_id' => 'required',
            'details.*.unit_price' => 'required',
            'details.*.current_qty' => 'required',
            'details.*.receive_qty' => 'required',
            // 'details.*.used_qty' => 'required',
            'details.*.total_used_qty' => 'required',
            'details.*.total_price' => 'required'
        ], [
            'details.*.product_id.required' => 'Required field.',
            'details.*.unit_price.required' => 'Required field.',
            'details.*.current_qty.required' => 'Required field.',
            'details.*.receive_qty.required' => 'Required field.',
            // 'details.*.used_qty.required' => 'Required field.',
            'details.*.total_used_qty.required' => 'Required field.',
            'details.*.total_price.required' => 'Required field.'
        ]);

        $overhead_info = [
            'last_month_overhead_amt'  => $request->last_month_overhead_amt,
            'last_month_production_kg' => $request->last_month_production_kg,
            'overhead_per_kg'          => $request->overhead_per_kg,
        ];

        $data = [
            'packing_no'           => $request->packing_no,
            'packing_date'         => dateConvertFormtoDB($request->packing_date),
            'warehouse_id'         => $request->warehouse_id,
            'packing_types'        => $request->packing_types,
            'finishing_product_id' => $request->finishing_product_id,
            'trans_to_store_qty'   => $request->trans_to_store_qty,
            'unit_price'           => $request->unit_price,
            'total_price'          => $request->total_price,
            'narration'            => $request->narration,
            'total_rm_qty'         => $request->total_rm_qty,
            'total_rm_price'       => $request->total_rm_price,

            'inv_total_qty'        => $request->inv_total_qty,
            'inv_total_amount'     => $request->inv_total_amount,

            'overhead_info'        => json_encode($overhead_info),
            'reject_overhead_amt'  => $this->calcOverheadRejectAmount($request->details),
            'reject_amt'           => $request->total_rm_price - $this->calcOverheadRejectAmount($request->details),
            'created_by'           => Auth::user()->id,
        ];

        try {
            DB::beginTransaction();

            $app = $request->approve_status;
            $result = ProductionPacking::create($data);

            $details = $this->dataFormat($request, $result->id);
            ProductionPackingDetails::insert($details);

            $details = $this->dataFormatInventoryMaterials($request, $result->id);
            ProductionInventoryMaterial::insert($details);

            if($app == 1){
                $this->approve($result->id);

                if($request->updated_by == Null){
                    DB::table('production_packings')->where('id', $result->id)->update(array('updated_by' => Auth::user()->id));
                }
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => "Packing # " . $request->packing_no .' Successfully Saved!']);
        } catch (\Exception $e) {
            print_r($e->getMessage());
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    public function show($id)
    {
        try {
            $editModeData = ProductionPacking::with('warehouse','finish_product')->FindOrFail($id);
            $editModeDetailsData = ProductionPackingDetails::with('product')->where('packing_section_id',$id)->get();

            $inv_details = ProductionInventoryMaterial::with('product')->where('packing_section_id',$id)->get();

            $data = [
                'id'    => $editModeData->id,
                'packing_no' => $editModeData->packing_no,
                'packing_date' => dateConvertDBtoForm($editModeData->packing_date),
                'warehouse_id' => $editModeData->warehouse->purchase_ware_houses_name,
                'packing_types' => $editModeData->packing_types,
                'finishing_product_id' => $editModeData->finish_product->product_name ,
                'trans_to_store_qty' => $editModeData->trans_to_store_qty,
                'unit_price' => $editModeData->unit_price,
                'total_price' => $editModeData->total_price,
                'narration' => $editModeData->narration,
                'total_rm_qty' => $editModeData->total_rm_qty,
                'total_rm_price' => $editModeData->total_rm_price,

                'inv_total_qty' => $editModeData->inv_total_qty,
                'inv_total_amount' => $editModeData->inv_total_amount,
                'deleteID' => [],
                'details'    => $editModeDetailsData,
                'inv_details'    => $inv_details
            ];
            return response()->json(['status'=>'success','data'=>$data]);
        } catch(\Exception $e){
            return response()->json(['status'=>'error','data'=>[]]);
        }
    }

    public function edit($id)
    {
        try {
            $editModeData = ProductionPacking::FindOrFail($id);
            $editModeDetailsData = ProductionPackingDetails::where('packing_section_id',$id)->get();

            $inv_details = ProductionInventoryMaterial::where('packing_section_id',$id)->get();

            $data = [
                'id'    => $editModeData->id,
                'packing_no' => $editModeData->packing_no,
                'packing_date' => dateConvertDBtoForm($editModeData->packing_date),
                'warehouse_id' => $editModeData->warehouse_id,
                'packing_types' => $editModeData->packing_types,
                'finishing_product_id' => $editModeData->finishing_product_id,
                'trans_to_store_qty' => $editModeData->trans_to_store_qty,
                'unit_price' => $editModeData->unit_price,
                'total_price' => $editModeData->total_price,
                'narration' => $editModeData->narration,
                'total_rm_qty' => $editModeData->total_rm_qty,
                'total_rm_price' => $editModeData->total_rm_price,

                'inv_total_qty' => $editModeData->inv_total_qty,
                'inv_total_amount' => $editModeData->inv_total_amount,
                'approve_status' => '',
                'deleteID' => [],
                'details'    => $editModeDetailsData,
                'inv_details'    => $inv_details,
            ];
            return response()->json(['status'=>'success','data'=>$data]);
        } catch(\Exception $e){
            $msg = $e->getMessage();
            return response()->json(['status'=>'error','data'=>$msg]);
        }
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'packing_no' => 'required',
            'packing_date' => 'required',
            'warehouse_id' => 'required',
            'packing_types' => 'required',

            'total_rm_qty' => 'required',
            'total_rm_price' => 'required',

            'details.*.product_id' => 'required',
            'details.*.unit_price' => 'required',
            'details.*.current_qty' => 'required',
            'details.*.receive_qty' => 'required',
            // 'details.*.used_qty' => 'required',
            'details.*.total_used_qty' => 'required',
            'details.*.total_price' => 'required'
        ], [

            'details.*.product_id.required' => 'Required field.',
            'details.*.unit_price.required' => 'Required field.',
            'details.*.current_qty.required' => 'Required field.',
            'details.*.receive_qty.required' => 'Required field.',
            // 'details.*.used_qty.required' => 'Required field.',
            'details.*.total_used_qty.required' => 'Required field.',
            'details.*.total_price.required' => 'Required field.'
        ]);

        $overhead_info = [
            'last_month_overhead_amt'  => $request->last_month_overhead_amt,
            'last_month_production_kg' => $request->last_month_production_kg,
            'overhead_per_kg'          => $request->overhead_per_kg,
        ];

        try {
            DB::beginTransaction();

            $app = $request->approve_status;


            $editModeData = ProductionPacking::FindOrFail($id);

            $editModeData->packing_no           = $request->packing_no;
            $editModeData->packing_date         = dateConvertFormtoDB($request->packing_date);
            $editModeData->narration            = $request->narration;
            $editModeData->packing_types        = $request->packing_types;
            $editModeData->warehouse_id         = $request->warehouse_id;
            $editModeData->finishing_product_id = $request->finishing_product_id;
            $editModeData->trans_to_store_qty   = $request->trans_to_store_qty;
            $editModeData->unit_price           = $request->unit_price;
            $editModeData->total_price          = $request->total_price;
            $editModeData->total_rm_qty         = $request->total_rm_qty;
            $editModeData->total_rm_price       = $request->total_rm_price;

            $editModeData->inv_total_qty        = $request->inv_total_qty;
            $editModeData->inv_total_amount     = $request->inv_total_amount;

            $editModeData->overhead_info        = json_encode($overhead_info);
            $editModeData->reject_overhead_amt  = $this->calcOverheadRejectAmount($request->details);
            $editModeData->reject_amt           = $request->total_rm_price - $this->calcOverheadRejectAmount($request->details);

            if ($app!=1) {
                $editModeData->updated_by = Auth::user()->id;
            }

            $editModeData->save();

            /* Insert after Delete  */
            ProductionPackingDetails::where('packing_section_id', $id)->delete();
            ProductionInventoryMaterial::where('packing_section_id', $id)->delete();

            $details = $this->dataFormat($request, $id);
            ProductionPackingDetails::insert($details);

            $inv_details = $this->dataFormatInventoryMaterials($request, $id);
            ProductionInventoryMaterial::insert($inv_details);

            if($app == 1){
                $this->approve($id);
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Packing successfully updated!']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    public function destroy($id)
    {
        try{

            ProductionPackingDetails::where('packing_section_id',$id)->delete();
            ProductionInventoryMaterial::where('packing_section_id',$id)->delete();

            ProductionPacking::FindOrFail($id)->delete();

            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Packing successfully Deleted !']);
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
                'packing_section_id' => $id,
                'product_id'         => $request->details[$i]['product_id'],
                'remarks'            => $request->details[$i]['remarks'],
                'unit_price'         => $request->details[$i]['unit_price'],
                'current_qty'        => $request->details[$i]['current_qty'],
                'receive_qty'        => $request->details[$i]['receive_qty'],
                // 'used_qty'           => $request->details[$i]['used_qty'],
                'total_used_qty'     => $request->details[$i]['total_used_qty'],
                'overhead_price'     => $request->details[$i]['overhead_price'],
                'total_price'        => $request->details[$i]['total_price'],
                'remain_qty'        => $request->details[$i]['remain_qty'],
            ];
        }

        return $dataFormat;
    }

    public function dataFormatInventoryMaterials($request, $id)
    {
        $dataFormat = [];
        $count = count($request->inv_details);
        for ($i = 0; $i < $count; $i++) {
            $dataFormat[$i] = [
                'packing_section_id'   => $id,
                'product_id'              => $request->inv_details[$i]['product_id'],
                'remarks'                 => $request->inv_details[$i]['remarks'],
                'current_stock_qty'       => $request->inv_details[$i]['current_stock_qty'],
                'qty'                     => $request->inv_details[$i]['qty'],
                'unit_price'              => $request->inv_details[$i]['unit_price'],
                'total_price'             => $request->inv_details[$i]['total_price'],
            ];
        }

        return $dataFormat;
    }

    public function calcOverheadRejectAmount($details)
    {
        $reject_overhead_amt = 0;
        $count = count($details);
        for ($i = 0; $i < $count; $i++) {

            $reject_overhead_amt += $details[$i]['overhead_price'];
        }

        return $reject_overhead_amt;
    }

    public function getPdfData($id)
    {
        $editModeData = ProductionPacking::with('warehouse','finish_product')->FindOrFail($id);
        $editModeDetailsData = ProductionPackingDetails::with('product')->where('packing_section_id',$id)->get();
        $data = [
            'id'                   => $editModeData->id,
            'packing_no'           => $editModeData->packing_no,
            'packing_date'         => dateConvertDBtoForm($editModeData->packing_date),
            'warehouse_id'         => $editModeData->warehouse->purchase_ware_houses_name,
            'packing_types'        => $editModeData->packing_types,
            'finishing_product_id' => $editModeData->finish_product->product_name ,
            'trans_to_store_qty'   => $editModeData->trans_to_store_qty,
            'unit_price'           => $editModeData->unit_price,
            'total_price'          => $editModeData->total_price,
            'narration'            => $editModeData->narration,
            'total_rm_qty'         => $editModeData->total_rm_qty,
            'total_rm_price'       => $editModeData->total_rm_price,
            'details'              => $editModeDetailsData
        ];

        return $data;

    }

    public function exportAsPdf($data)
    {

        $company = CompanyInfo::Find(1);

        $data = array(
            'company'=> $company,
            'packing'=> $data,

        );

        $html = \View::make('production.production_section.product_pdf.packing_sectionPDF')->with($data);

        $mpdf = new \Mpdf\Mpdf([
            'margin_left'   => 20,
            'margin_right'  => 15,
            'margin_top'    => 48,
            'margin_bottom' => 25,
            'margin_header' => 10,
            'margin_footer' => 10
        ]);

        $title = 'Packing Section';
        $author = 'iVenture';
        if ($company) {
            $title .= ' - '.$company->name;
            $author = $company->name;
        }

        $mpdf->SetProtection(array('print'));
        $mpdf->SetTitle($title);
        $mpdf->SetAuthor($author);
        $mpdf->SetWatermarkText("Packing Section");
        $mpdf->showWatermarkText = true;
        $mpdf->watermark_font = 'DejaVuSansCondensed';
        $mpdf->watermarkTextAlpha = 0.07;
        $mpdf->SetDisplayMode('fullpage');
        // $stylesheet = file_get_contents(public_path().'/css/mpdf.css');
        // $mpdf->WriteHTML($stylesheet,1);
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }

    /**
     * @ Get Ajax Last Production Info - Unit Price with Qty Check
     *
     * @param $product_id
     * @return Product row with last price
     */
    public function getInventoryOrSemiFinishedLastPrice($product_id) //Incomplete
    {
        $data = [];
        $data['unit_price'] = 1000;

        /*$product = ProductionAssembling::where('product_id',$product_id)
                        ->where('approve_status',1)
                        ->leftJoin('production_assembling_details', 'production_assembling_details.assembling_section_id','=','production_assemblings.id')
                        ->select('production_assemblings.unit_price')
                        ->orderBy('production_assemblings.assembling_date','DESC')
                        ->First();*/

        $product = ProductionProducts::where('id',$product_id)->first();

        if ($product) {

            $data['unit_price'] = $product->buying_price;
        }
        else
        {
            $data['unit_price'] = ProductionProducts::where('id',$product_id)->first()->buying_price;
        }

        return $data;
    }
}
