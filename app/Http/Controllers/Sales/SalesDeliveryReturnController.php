<?php

namespace App\Http\Controllers\Sales;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Production\ProductionProducts;
use App\Model\Purchase\PurchaseWareHouse;
use App\Model\Purchase\PurchaseReturnType;
use App\Model\Sales\SalesCustomer;

use App\Model\Sales\SalesDeliveryReturn;
use App\Model\Sales\SalesDeliveryReturnDetails;

use App\Model\Inventory\InventoryCurrentStock;
use App\Model\Inventory\StockTransectionLog;
use App\Model\Inventory\StockTransectionLogDetails;
use App\Lib\Enumerations\StockTransactionType;

use App\Model\Accounts\AccountsSalesInvoiceReturnVoucher;
use App\Model\Accounts\AccountsSalesInvoiceReturnVoucherDetails;

use Illuminate\Support\Facades\Auth;
use DB;


class SalesDeliveryReturnController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $query = DB::table('sales_delivery_returns')
                ->leftJoin('sales_customers', 'sales_delivery_returns.sales_delivery_return_customer_id', '=', 'sales_customers.id')
                ->leftJoin('purchase_ware_houses', 'sales_delivery_returns.sales_delivery_return_warehouse_id', '=', 'purchase_ware_houses.id')
                ->leftJoin('purchase_return_types', 'sales_delivery_returns.sales_delivery_return_return_types_id', '=', 'purchase_return_types.id')
                ->leftJoin('users as ura', 'sales_delivery_returns.created_by','=','ura.id')
                ->leftJoin('users as ured', 'sales_delivery_returns.updated_by','=','ured.id')
                ->leftJoin('users as urea', 'sales_delivery_returns.approve_by','=','urea.id')
                ->whereNull('sales_delivery_returns.deleted_at')
                ->select(['sales_delivery_returns.id AS id',
                    'sales_delivery_returns.sales_delivery_return_no',
                    'sales_delivery_returns.sales_delivery_return_date',
                    'sales_delivery_returns.sales_delivery_return_narration',
                    'sales_delivery_returns.sales_delivery_return_total_qty',
                    'sales_delivery_returns.sales_delivery_return_total_price',
                    'sales_delivery_returns.created_by',
                    'sales_delivery_returns.updated_by',
                    'sales_delivery_returns.approve_by',
                    'sales_delivery_returns.approve_status',
                    'sales_customers.sales_customer_name',
                    'purchase_return_types.purchase_return_types_name',
                    'purchase_ware_houses.purchase_ware_houses_name',
                    'ura.user_name as ad_name',
                    'ured.user_name as ed_name',
                    'urea.user_name as ap_name'
                ]);

            return datatables()->of($query)->toJson();
        }

        $productLists   = ProductionProducts::Select('production_products.*', 'production_measure_units.measure_unit')
            ->where('production_products.status', '1')
            ->where('production_products.is_raw_material_status', '0')
            ->leftJoin('production_measure_units', 'production_products.measure_unit_id', '=', 'production_measure_units.id')
            ->get();
        $warehouseLists     = PurchaseWareHouse::where('status', '1')->get();
        $customerLists      = SalesCustomer::where('status', '1')->get();
        $returntypeLists    = PurchaseReturnType::where('status', '1')->get();

        return view('sales.sales_section.sales_delivery_return',compact('returntypeLists', 'productLists','warehouseLists','customerLists'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        date_default_timezone_set("Asia/Dhaka");

        $inv_no_like = 'DR';
        $rtn_val =InvStore($request->sales_delivery_return_no, $inv_no_like,
            'sales_delivery_returns','sales_delivery_return_no');

        $request->merge(['sales_delivery_return_no' => $rtn_val]) ;


        $this->validate($request, [
            'sales_delivery_return_no'              => 'required|unique:sales_delivery_returns,sales_delivery_return_no',
            'sales_delivery_return_date'            => 'required',
            'sales_delivery_return_return_types_id' => 'required',
            'sales_delivery_return_warehouse_id'    => 'required',
            'sales_delivery_return_customer_id'     => 'required',
            'sales_delivery_return_total_qty'       => 'required',
            'sales_delivery_return_total_price'     => 'required',
            'details.*.sales_delivery_return_details_product_id'    => 'required',
            'details.*.sales_delivery_return_details_unit'          => 'required',
            'details.*.sales_delivery_return_details_unit_price'    => 'required',
            'details.*.sales_delivery_return_details_qty'           => 'required',
            'details.*.sales_delivery_return_details_total_price'   => 'required',
        ], [
            'details.*.sales_delivery_return_details_product_id.required'   => 'required',
            'details.*.sales_delivery_return_details_unit.required'         => 'required',
            'details.*.sales_delivery_return_details_unit_price.required'   => 'required',
            'details.*.sales_delivery_return_details_qty.required'          => 'required',
            'details.*.sales_delivery_return_details_total_price.required'  => 'required',
        ]);

        if($request->sales_delivery_return_date!= ''){
            $date = str_replace('/', '-', $request->sales_delivery_return_date);
            $date_val =date('Y-m-d', strtotime($date));
        }else{
            $date_val =date('Y-m-d');
        }

        $app= $request->approve;

        $data = [
            'sales_delivery_return_no' 					=> $request->sales_delivery_return_no,
            'sales_delivery_return_date' 				=> $date_val,
            'sales_delivery_return_return_types_id' 	=> $request->sales_delivery_return_return_types_id,
            'sales_delivery_return_warehouse_id' 		=> $request->sales_delivery_return_warehouse_id,
            'sales_delivery_return_customer_id' 		=> $request->sales_delivery_return_customer_id,
            'sales_delivery_return_narration' 			=> $request->sales_delivery_return_narration,
            'sales_delivery_return_total_qty' 			=> $request->sales_delivery_return_total_qty,
            'sales_delivery_return_total_price' 		=> $request->sales_delivery_return_total_price,
            'created_by' 								=> Auth::user()->id,
        ];

        try {
            DB::beginTransaction();

            $result = SalesDeliveryReturn::create($data);
            $details = $this->dataFormat($request, $result->id);
            SalesDeliveryReturnDetails::insert($details);

            if($app ==1){
                $this->approve($result->id);

                if($request->updated_by == Null){
                    DB::table('sales_delivery_returns')->where('id', $result->id)->update(array('updated_by' => Auth::user()->id));
                }
            }

            DB::commit();
            //return response()->json(['status' => 'success', 'message' => 'Delivery Requisition successfully saved!']);
            return response()->json(['status' => 'success', 'message' => "Sales Delivery Return # " . $request->sales_delivery_return_no .' Successfully Saved!']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    public function show($id)
    {
        try {
            $showData = SalesDeliveryReturn::with('get_wh','get_customer','get_issue_type')->FindOrFail($id);
            $showDetailsData = SalesDeliveryReturnDetails::with('get_rtn','get_prod')
                ->where('sales_delivery_return_id',$id)
                ->get();

            $customer_id    = $showData->get_customer->sales_customer_name;
            $warehouse_id   = $showData->get_wh->purchase_ware_houses_name;
            $rtn_type_name  = $showData->get_issue_type->purchase_return_types_name;

            $data = [
                'id'                                        => $showData->id,
                'sales_delivery_return_no'                  => $showData->sales_delivery_return_no,
                'sales_delivery_return_date'                => date('d/m/Y', strtotime($showData->sales_delivery_return_date)),
                'sales_delivery_return_return_types_id'     => $rtn_type_name,
                'sales_delivery_return_warehouse_id'        => $warehouse_id,
                'sales_delivery_return_customer_id'         => $customer_id,
                'sales_delivery_return_narration'           => $showData->sales_delivery_return_narration,
                'sales_delivery_return_total_qty'           => $showData->sales_delivery_return_total_qty,
                'sales_delivery_return_total_price'         => $showData->sales_delivery_return_total_price,
                'total_pricein_word'                        => getCurrency($showData->sales_delivery_return_total_price),
                'details'    => $showDetailsData,
            ];
            return response()->json(['status'=>'success','data'=>$data]);
        } catch(\Exception $e){
            return response()->json(['status'=>'error','data'=>[$e->getMessage()]]);
        }
    }

    public function edit($id)
    {
        try {
            $editModeData = SalesDeliveryReturn::FindOrFail($id);
            $editModeDetailsData = SalesDeliveryReturnDetails::where('sales_delivery_return_id',$id)->get();
            $data = [
                'id'                                        => $editModeData->id,
                'sales_delivery_return_no'                  => $editModeData->sales_delivery_return_no,
                'sales_delivery_return_date'                => date('d/m/Y', strtotime($editModeData->sales_delivery_return_date)),
                'sales_delivery_return_return_types_id'     => $editModeData->sales_delivery_return_return_types_id,
                'sales_delivery_return_warehouse_id'        => $editModeData->sales_delivery_return_warehouse_id,
                'sales_delivery_return_customer_id'         => $editModeData->sales_delivery_return_customer_id,
                'sales_delivery_return_narration'           => $editModeData->sales_delivery_return_narration,
                'sales_delivery_return_total_qty'           => $editModeData->sales_delivery_return_total_qty,
                'sales_delivery_return_total_price'         => $editModeData->sales_delivery_return_total_price,
                'deleteID'                                  => [],
                'details'                                   => $editModeDetailsData
            ];
            return response()->json(['status'=>'success','data'=>$data]);
        } catch(\Exception $e){
            return response()->json(['status'=>'error','data'=>[$e->getMessage()]]);
        }
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'sales_delivery_return_no' 							    => 'required|unique:sales_delivery_returns,sales_delivery_return_no,'.$id.',id',
            'sales_delivery_return_date' 						    => 'required',
            'sales_delivery_return_return_types_id' 				=> 'required',
            'sales_delivery_return_warehouse_id'	 				=> 'required',
            'sales_delivery_return_customer_id'						=> 'required',
            'sales_delivery_return_total_qty' 						=> 'required',
            'sales_delivery_return_total_price' 					=> 'required',
            'details.*.sales_delivery_return_details_product_id' 	=> 'required',
            'details.*.sales_delivery_return_details_unit' 			=> 'required',
            'details.*.sales_delivery_return_details_unit_price' 	=> 'required',
            'details.*.sales_delivery_return_details_qty' 			=> 'required',
            'details.*.sales_delivery_return_details_total_price' 	=> 'required',
        ], [
            'details.*.sales_delivery_return_details_product_id.required' 	=> 'required',
            'details.*.sales_delivery_return_details_unit.required' 		=> 'required',
            'details.*.sales_delivery_return_details_unit_price.required' 	=> 'required',
            'details.*.sales_delivery_return_details_qty.required' 			=> 'required',
            'details.*.sales_delivery_return_details_total_price.required' 	=> 'required',
        ]);

        date_default_timezone_set("Asia/Dhaka");
        $date = str_replace('/', '-', $request->sales_delivery_return_date);
        $date_val =date('Y-m-d', strtotime($date));

        $app= $request->approve;


        /*$data = [
            'sales_delivery_return_no' 					=> $request->sales_delivery_return_no,
            'sales_delivery_return_date' 				=> $date_val,
            'sales_delivery_return_return_types_id' 	=> $request->sales_delivery_return_return_types_id,
            'sales_delivery_return_warehouse_id' 		=> $request->sales_delivery_return_warehouse_id,
            'sales_delivery_return_customer_id' 		=> $request->sales_delivery_return_customer_id,
            'sales_delivery_return_narration' 			=> $request->sales_delivery_return_narration,
            'sales_delivery_return_total_qty' 			=> $request->sales_delivery_return_total_qty,
            'sales_delivery_return_total_price' 		=> $request->sales_delivery_return_total_price,
            'updated_by' 								=> Auth::user()->id,
        ];*/


        try {
            DB::beginTransaction();

            $editModeData = SalesDeliveryReturn::FindOrFail($id);

            $editModeData->sales_delivery_return_no              = $request->sales_delivery_return_no;
            $editModeData->sales_delivery_return_date            = $date_val;
            $editModeData->sales_delivery_return_return_types_id = $request->sales_delivery_return_return_types_id;
            $editModeData->sales_delivery_return_warehouse_id    = $request->sales_delivery_return_warehouse_id;
            $editModeData->sales_delivery_return_customer_id     = $request->sales_delivery_return_customer_id;
            $editModeData->sales_delivery_return_narration       = $request->sales_delivery_return_narration;
            $editModeData->sales_delivery_return_total_qty       = $request->sales_delivery_return_total_qty;
            $editModeData->sales_delivery_return_total_price     = $request->sales_delivery_return_total_price;


            if ($app!=1) {
                $editModeData->updated_by = Auth::user()->id;
            }

            $editModeData->save();


            if (count($request->deleteID) > 0) {
                SalesDeliveryReturnDetails::whereIn('id', $request->deleteID)->delete();
            }

            $dataFormat = [];
            $count = count($request->details);
            for ($i = 0; $i < $count; $i++) {
                if (isset($request->details[$i]['id']) && $request->details[$i]['id'] !='') {
                    $updateData = [
                        'sales_delivery_return_id'                  => $id,
                        'sales_delivery_return_details_product_id'  => $request->details[$i]['sales_delivery_return_details_product_id'],
                        'sales_delivery_return_details_remarks'     => $request->details[$i]['sales_delivery_return_details_remarks'],
                        'sales_delivery_return_details_unit'        => $request->details[$i]['sales_delivery_return_details_unit'],
                        'sales_delivery_return_details_unit_price'  => $request->details[$i]['sales_delivery_return_details_unit_price'],
                        'sales_delivery_return_details_qty'         => $request->details[$i]['sales_delivery_return_details_qty'],
                        'sales_delivery_return_details_total_price' => $request->details[$i]['sales_delivery_return_details_total_price']
                    ];
                    SalesDeliveryReturnDetails::where('id', $request->details[$i]['id'])->update($updateData);
                } else {
                    $dataFormat[$i] =[
                        'ac_sales_return_voucher_id'                => $id,
                        'sales_delivery_return_details_product_id'  => $request->details[$i]['sales_delivery_return_details_product_id'],
                        'sales_delivery_return_details_remarks'     => $request->details[$i]['sales_delivery_return_details_remarks'],
                        'sales_delivery_return_details_unit'        => $request->details[$i]['sales_delivery_return_details_unit'],
                        'sales_delivery_return_details_unit_price'  => $request->details[$i]['sales_delivery_return_details_unit_price'],
                        'sales_delivery_return_details_qty'         => $request->details[$i]['sales_delivery_return_details_qty'],
                        'sales_delivery_return_details_total_price' => $request->details[$i]['sales_delivery_return_details_total_price']
                    ];
                }
            }

            if(count($dataFormat) > 0){
                SalesDeliveryReturnDetails::insert($dataFormat);
            }

            if($app ==1){

                $this->approve($id);
            }

            DB::commit();

            return response()->json(['status' => 'success', 'message' => 'Sales Delivery Return successfully updated!']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    public function approve($id)
    {
        try {
            $appData = DB::table('sales_delivery_returns')->where('id',$id)->first();



            if($appData->approve_status == 0)
            {

                $appDetailsData = SalesDeliveryReturnDetails::where('sales_delivery_return_id',$id)->get();

                $customer 		= $appData->sales_delivery_return_customer_id;
                $tender 		= '';
                $serial 		= $appData->sales_delivery_return_no;
                $ref_tbl_id 	= '';
                $warehouseId	= $appData->sales_delivery_return_warehouse_id;

                $date 			= $appData->sales_delivery_return_date;
                $ref_tbl		= '';
                $log_tbl 		= 'sales_delivery_returns';
                $sup_cus_tabl 	= 'sales_customers';
                $log_type 		= StockTransactionType::$sales_return;
                $total_qty      = $appData->sales_delivery_return_total_qty;
                $total_price    = $appData->sales_delivery_return_total_price;

                /****************************************** ADD STOCK INFORMATION  **********************************************************/
                foreach($appDetailsData as $val){
                    $checkExists = InventoryCurrentStock::where('inventory_current_stocks_product_id',$val->sales_delivery_return_details_product_id)
                        ->where('inventory_current_stocks_warehouse_id',$warehouseId)
                        ->first();

                    if($checkExists){
                        InventoryCurrentStock::where('inventory_current_stocks_product_id',$val->sales_delivery_return_details_product_id)
                            ->where('inventory_current_stocks_warehouse_id',$warehouseId)
                            ->increment('inventory_stocks_current_qty',  $val->sales_delivery_return_details_qty);

                        $Ics=InventoryCurrentStock::where('inventory_current_stocks_product_id', $val->sales_delivery_return_details_product_id)
                            ->where('inventory_current_stocks_warehouse_id', $warehouseId)->first();

                        if($Ics->unit_price == '0' || $Ics->unit_price == ''){
                            $unitPrice = $val->sales_delivery_return_details_unit_price;
                        }else{
                            $unitPrice = $Ics->unit_price;
                        }

                        $total_price= $unitPrice * $Ics->inventory_stocks_current_qty;

                        InventoryCurrentStock::where('inventory_current_stocks_product_id', $val->sales_delivery_return_details_product_id)
                            ->where('inventory_current_stocks_warehouse_id', $warehouseId)
                            ->update(array(
                                'unit_price'    => round($unitPrice,2),
                                'total_price'   => round($total_price,2),
                            ));
                        ProductionProducts::where('id', $val->sales_delivery_return_details_product_id)
                            ->update(array(
                                'buying_price'  =>  round($unitPrice,2),
                                'selling_price' =>  round($unitPrice,2)
                            ));
                    }
                }

                /**************************************** STOCK LOG INFO START ********************************************************/
                $stock_log =[
                    'stock_transection_logs_date' 			=> $date,
                    'stock_transection_logs_type' 			=> $log_type ,
                    'stock_transection_logs_number' 		=> $serial,
                    'stock_transection_logs_ref_table_name' => $ref_tbl,
                    'stock_transection_logs_ref_table_id' 	=> $ref_tbl_id,
                    'stock_transection_logs_table_name' 	=> $log_tbl,
                    'stock_transection_logs_table_id' 		=> $id,
                    'stock_trans_supp_cus_table_name' 		=> $sup_cus_tabl,
                    'stock_trans_supp_cus_table_id' 		=> $customer,
                    'stock_trans_tender' 					=> $tender,
                    'stock_trans_warehouse_id' 				=> $warehouseId,
                    'stock_trans_qty'                       => $total_qty,
                    'stock_trans_total_price'               => $total_price,
                    'created_by'                            => Auth::user()->id,
                ];
                $mainStock = StockTransectionLog::create($stock_log);

                $Stockdetails = [];
                $details_tbl = 'sales_delivery_return_details';

                foreach($appDetailsData as $key => $row){
                    $w_info = InventoryCurrentStock::where('inventory_current_stocks_warehouse_id', $warehouseId)
                        ->where('inventory_current_stocks_product_id', $row->sales_delivery_return_details_product_id)->first();

                    $Stockdetails[] = [
                        'log_id'            => $mainStock->id,
                        'log_table_name'    => $details_tbl,
                        'log_table_id'      => $row->id,

                        'product_id'        => $row->sales_delivery_return_details_product_id,
                        'warehouse_id'      => $warehouseId,

                        'log_entry_qty'     => $row->sales_delivery_return_details_qty,
                        'log_open_qty'      => $w_info->inventory_stocks_open_qty,
                        'log_current_qty'   => $w_info->inventory_stocks_current_qty,
                        'log_close_qty'     => '',

                        'log_unit_price'    => $row->sales_delivery_return_details_unit_price,
                        'log_total_price'   => $row->sales_delivery_return_details_total_price,

                        'add_sub'           => 1,

                        'entry_date'        => $date,
                        'created_by'        => Auth::user()->id,
                    ];
                }
                StockTransectionLogDetails::insert($Stockdetails);

                /****************************************** ACCOUNT INFORMATION  **********************************************************/
                $data_ac = [
                    'vouchers_no' 		=> "Sales-Rtn-$id",
                    'sales_return_id' 	=> $id,
                    'sales_return_date' => $date,
                    'customer_id' 		=> $customer,
                    'payment_type' 		=> '1',
                    'total_qty' 		=> $appData->sales_delivery_return_total_qty,
                    'total_price' 		=> $appData->sales_delivery_return_total_price,
                    'created_by' 		=> Auth::user()->id,
                ];

                $result_ac = AccountsSalesInvoiceReturnVoucher::create($data_ac);


                /****************************************** ACCOUNT DETAILS INFORMATION  **************************************************/
                $dataFormatAc = [];
                $count = count($appDetailsData);
                for ($i = 0; $i < $count; $i++) {
                    $dataFormatAc[$i] = [
                        'ac_sales_return_voucher_id' 	=> $result_ac->id,//$result_ac->id
                        'product_id' 					=> $appDetailsData[$i]['sales_delivery_return_details_product_id'],
                        'remarks' 						=> $appDetailsData[$i]['sales_delivery_return_details_remarks'],
                        'm_unit' 						=> $appDetailsData[$i]['sales_delivery_return_details_unit'],
                        'unit_price' 					=> $appDetailsData[$i]['sales_delivery_return_details_unit_price'],
                        'qty' 							=> $appDetailsData[$i]['sales_delivery_return_details_qty'],
                        'total_price' 					=> $appDetailsData[$i]['sales_delivery_return_details_total_price'],
                    ];
                }

               AccountsSalesInvoiceReturnVoucherDetails::insert($dataFormatAc);


                /****************************************** APPROVE INFORMATION  **********************************************************/
               DB::table('sales_delivery_returns')->where('id', $id)->update(array(
                    'approve_status'    => 1,
                    'approve_by'        => Auth::user()->id,
                    'approve_at'        => Carbon::now(),
                ));


                return response()->json(['status' => 'success', 'message' => 'Sales Delivery Return successfully Approved!']);
            }
            else{
                return response()->json(['status' => 'success', 'message' => 'Alredy Approved!']);
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            return response()->json(['status' => 'error', 'message' => $message]);
        }
    }

    public function destroy($id)
    {
        try{
            SalesDeliveryReturnDetails::where('sales_delivery_return_id',$id)->delete();
            SalesDeliveryReturn ::FindOrFail($id)->delete();
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Sales Delivery Return successfully Deleted !']);
        } elseif ($bug == 1451) {
            return response()->json(['status' => 'error', 'message' => 'This data is used another table,can not delete data']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    Public function SalesReturnGenerate(){
        $id = SalesDeliveryReturn::withTrashed()->count();
        $currentId = $id+1;
        return 'DR'.date('Ym').$currentId;
    }

    private function dataFormat($request, $id)
    {
        $dataFormat = [];
        $count = count($request->details);
        for ($i = 0; $i < $count; $i++) {
            $dataFormat[$i] = [
                'sales_delivery_return_id'                  => $id,
                'sales_delivery_return_details_product_id'  => $request->details[$i]['sales_delivery_return_details_product_id'],
                'sales_delivery_return_details_remarks'     => $request->details[$i]['sales_delivery_return_details_remarks'],
                'sales_delivery_return_details_unit'        => $request->details[$i]['sales_delivery_return_details_unit'],
                'sales_delivery_return_details_unit_price'  => $request->details[$i]['sales_delivery_return_details_unit_price'],
                'sales_delivery_return_details_qty'         => $request->details[$i]['sales_delivery_return_details_qty'],
                'sales_delivery_return_details_total_price' => $request->details[$i]['sales_delivery_return_details_total_price']
            ];
        }
        return $dataFormat;
    }

}
