<?php

namespace App\Http\Controllers\Inventory;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Lib\Enumerations\StockTransactionType;
use App\Model\Inventory\StockTransectionLog;
use App\Model\Inventory\StockTransectionLogDetails;

use App\Model\Purchase\PurchaseWareHouse;
use App\Model\Production\ProductionProducts;
use App\Model\Inventory\InventoryCurrentStock;

use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use DB;

class InventoryCurrentStockController extends Controller
{
    public function index(Request $request)
    {
        if ($request->filled('token_ics_qty_price') && $request->token_ics_qty_price == 'token_ics') {
            return $this->getInventoryStockQtyAvgPrice($request->warehouse_id, $request->product_id);
        }
        if ($request->filled('token_stock_info') && $request->token_stock_info == 'stock_info') {
            return $this->getInventoryStockInfo($request->warehouse_id);
        }

        $productLists   = ProductionProducts::where('status', '1')->get();
        $warehouseLists   = PurchaseWareHouse::where('status', '1')->get();
        return view('inventory.inventory_current_stock',compact( 'productLists','warehouseLists'));
    }

    public function inv_load_product($ware_house_id)
    {
        $productLists   = ProductionProducts::where('status', '1')
                                ->selectRaw('id,product_name,is_raw_material_status,buying_price,selling_price')
                                ->orderBy('id','ASC')
                                ->get();

        $dataFormat = [];
        foreach ($productLists as $key => $product) {
            $state = 0;
            $ware_house = InventoryCurrentStock::where('inventory_current_stocks_warehouse_id',$ware_house_id)
                                ->where('inventory_current_stocks_product_id',$product->id)
                                ->first();

            $stockId = $state = $inventory_stocks_open_qty = $inventory_stocks_current_qty = $total_price = 0;
            $unit_price = $product->is_raw_material_status == 1 ? $product->buying_price : $product->selling_price;

            if($ware_house){
                $stockId                        =   $ware_house->id;
                $inventory_stocks_open_qty      =   $ware_house->inventory_stocks_open_qty;
                $inventory_stocks_current_qty   =   $ware_house->inventory_stocks_current_qty;
                $unit_price                     =   $ware_house->unit_price;
                $total_price                    =   $ware_house->total_price;

                    $state = 1;

            }

            $dataFormat[] = [
                'id' => $stockId,
                'product_name' => $product->product_name,
                'inventory_current_stocks_product_id' => $product->id,
                'inventory_stocks_open_qty' => $inventory_stocks_open_qty,
                'inventory_stocks_current_qty' => $inventory_stocks_current_qty,
                'is_raw_material_status' => $product->is_raw_material_status,
                'unit_price' => $unit_price,
                'total_price' => $total_price,
                'inv_state' =>  $state,
            ];
        }
        return $dataFormat;
    }

    public function create()
    {
        //
    }

    function deleteFunction()
    {
        $products = StockTransectionLogDetails::where('log_table_name','inventory_current_stocks')->groupBy('product_id')->orderBy('product_id','ASC')->get();

        foreach ($products as $product)
        {
           $Details = StockTransectionLogDetails::where('product_id',$product->product_id)->where('log_table_name','inventory_current_stocks')->get();

           if($Details->count() > 1)
           {
               $DetailsLowestId = StockTransectionLogDetails::where('product_id',$product->product_id)->where('log_table_name','inventory_current_stocks')->orderBy('product_id','ASC')->first();

               DB::table('stock_transection_log_details')->where('product_id',$product->product_id)->where('log_table_name','inventory_current_stocks')->where('id','!=',$DetailsLowestId->id)->delete();
           }
        }

       return;
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'inventory_current_stocks_warehouse_id' => 'required',

            'product.*.inventory_stocks_open_qty' => 'required',
            'product.*.inventory_stocks_current_qty' => 'required',
        ], [
            'product.*.inventory_stocks_open_qty.required' => 'Required field.',
            'product.*.inventory_stocks_current_qty.required' => 'Required field.',
        ]);

        /*date_default_timezone_set("Asia/Dhaka");
        $date_val= date('Y-m-d');*/
        $warehouseId = $request->inventory_current_stocks_warehouse_id;

        try {
            DB::beginTransaction();

            $dataFormat = [];
            $serial_no = "OpenStock-".date('Ym').$warehouseId;
            $total_qty_inv=0;
            $total_price_inv=0;
            $count = count($request->product);

            for ($i = 0; $i < $count; $i++) {

                if (isset($request->product[$i]['id']) && $request->product[$i]['id'] !=0 && $request->product[$i]['inventory_stocks_current_qty']!=0 && $request->product[$i]['unit_price'] > 0) {
                    $total_price = $request->product[$i]['inventory_stocks_current_qty'] * $request->product[$i]['unit_price'];

                    $updateData = [
                        'inventory_current_stocks_warehouse_id' => $warehouseId,
                        'inventory_current_stocks_product_id'   => $request->product[$i]['inventory_current_stocks_product_id'],
                        'inventory_stocks_open_qty'             => $request->product[$i]['inventory_stocks_open_qty'],
                        'inventory_stocks_current_qty'          => $request->product[$i]['inventory_stocks_current_qty'],
                        'unit_price'                            => $request->product[$i]['unit_price'],
                        'total_price'                           => $total_price,
                        'updated_by'                            => Auth::user()->id,
                    ];
                    InventoryCurrentStock::where('id', $request->product[$i]['id'])->update($updateData);
                } else if($request->product[$i]['id'] == 0  && $request->product[$i]['inventory_stocks_open_qty']!=0 && $request->product[$i]['unit_price'] > 0) {

                    $total_price = $request->product[$i]['inventory_stocks_open_qty'] * $request->product[$i]['unit_price'];
                    $dataFormat[$i] =[
                        'inventory_current_stocks_warehouse_id' => $warehouseId,
                        'inventory_current_stocks_product_id'   => $request->product[$i]['inventory_current_stocks_product_id'],
                        'inventory_stocks_open_qty'             => $request->product[$i]['inventory_stocks_open_qty'],
                        'inventory_stocks_current_qty'          => $request->product[$i]['inventory_stocks_current_qty'],
                        'unit_price'                            => $request->product[$i]['unit_price'],
                        'total_price'                           => $total_price,
                        'created_by'                            => Auth::user()->id,
                        'created_at'                            =>  Carbon::now(),
                    ];
                }

                $total_qty_inv=$total_qty_inv+$request->product[$i]['inventory_stocks_current_qty'];
                $total_price_inv=$total_price_inv+$request->product[$i]['total_price'];
            }
            if(count($dataFormat) > 0){
                InventoryCurrentStock::insert($dataFormat);
            }

            /****************************************** STOCK LOG INFO START ********************************************************/
            //// we have to check open stock oi where house deya ase naki! thakle add or editkorte hobe

            $date = date('Y-m-d');
            $log_type   = StockTransactionType::$inventory_open;

            $ref_tbl = '';
            $ref_tbl_id = '';

            $log_tbl = 'inventory_current_stocks';
            $log_tbl_id = '0';
            $sup_cus_tabl = '';
            $supplier= '';
            $total_qty = $total_qty_inv;

            $mainStock = StockTransectionLog::where('stock_transection_logs_type',StockTransactionType::$inventory_open)
                ->where('stock_trans_warehouse_id',$warehouseId)->first();

            if($mainStock){
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
                    'stock_trans_qty'                       => $total_qty,
                    'stock_trans_total_price'               => $total_price_inv,
                    'updated_at'                => Auth::user()->id,
                ];
                StockTransectionLog::where('id',$mainStock->id)->update($stock_log);
            }else{
                $stock_log_ins = [
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
                    'stock_trans_qty'                       => $total_qty,
                    'stock_trans_total_price'               => $total_price_inv,
                    'created_by'                            => Auth::user()->id,
                ];
                $mainStock = StockTransectionLog::create($stock_log_ins);
            }

            $get_details_data = InventoryCurrentStock::where('inventory_current_stocks_warehouse_id',$warehouseId)
                ->get();
            $details_tbl = 'inventory_current_stocks';

            foreach($get_details_data as $key => $row){
                $log_tabl = StockTransectionLogDetails::where('warehouse_id',$warehouseId)
                    ->where('log_table_name',$details_tbl)->where('product_id',$row->inventory_current_stocks_product_id)->first();
                if($log_tabl){
                    $StockdetailsUpdate = [
                        'log_id'            => $mainStock->id,
                        'log_table_name'    => $details_tbl,
                        'log_table_id'      => $row->id,
                        'product_id'        => $row->inventory_current_stocks_product_id,
                        'warehouse_id'      => $warehouseId,

                        'log_open_qty'      => $row->inventory_stocks_open_qty,
                        'log_entry_qty'     => $row->inventory_stocks_current_qty,
                        'log_current_qty'   => $row->inventory_stocks_current_qty,
                        'log_close_qty'     => '',

                        'log_unit_price'    => $row->unit_price,
                        'log_total_price'   => $row->total_price,

                        'add_sub'           => 1,

                        'entry_date'    => $date,
                        'updated_by'    => Auth::user()->id,
                    ];
                    /*StockTransectionLogDetails::where('warehouse_id',$warehouseId)
                        ->where('log_table_name',$details_tbl)->where('product_id',$row->product_id)
                        ->update($StockdetailsUpdate);*/
                }else{
                    $Stockdetails = [
                        'log_id'            => $mainStock->id,
                        'log_table_name'    => $details_tbl,
                        'log_table_id'      => $row->id,
                        'product_id'        => $row->inventory_current_stocks_product_id,
                        'warehouse_id'      => $warehouseId,

                        'log_open_qty'      => $row->inventory_stocks_open_qty,
                        'log_entry_qty'     => $row->inventory_stocks_current_qty,
                        'log_current_qty'   => $row->inventory_stocks_current_qty,
                        'log_close_qty'     => '',

                        'log_unit_price'    => $row->unit_price,
                        'log_total_price'   => $row->total_price,

                        'add_sub'           => 1,

                        'entry_date'    => $date,
                        'created_by'    => Auth::user()->id,

                    ];
                    StockTransectionLogDetails::create($Stockdetails);
                }
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Stock successfully saved!']);
        } catch (\Exception $e) {
            echo $e->getMessage();
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
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }

    /**
     * @ Get Ajax Production Qty, Unit Price
     *
     * @param $warehouse_id, $product_id
     * @return Product with Stcok Information
     */
    public function getInventoryStockQtyAvgPrice($warehouse_id, $product_id)
    {
        $data = [];
        $data['stock_qty'] = 0;
        $data['unit_price'] = 0;

        $result = InventoryCurrentStock::where('inventory_current_stocks_warehouse_id', $warehouse_id)
                        ->where('inventory_current_stocks_product_id', $product_id)
                        ->First();
        if ($result) {
            $data['stock_qty'] = $result->inventory_stocks_current_qty;
            $data['unit_price'] = $result->unit_price;
        }

        return $data;
    }

    public function getInventoryStockInfo($warehouse_id)
    {
        $productList  = ProductionProducts::select('production_products.id','production_products.product_name',
                                'inventory_current_stocks.inventory_stocks_current_qty as current_stock_qty','inventory_current_stocks.unit_price')
                              ->leftJoin('inventory_current_stocks', 'inventory_current_stocks.inventory_current_stocks_product_id', '=', 'production_products.id')
                              ->where('production_products.status', '1')
                              ->where('inventory_current_stocks.status', '1')
                              ->where('inventory_current_stocks.inventory_current_stocks_warehouse_id', $warehouse_id)
                              ->get();

        return $productList;
    }
}
