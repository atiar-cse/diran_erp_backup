<?php

namespace App\Http\Controllers\Sales;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Production\ProductionProducts;
use App\Model\Purchase\PurchaseWareHouse;
use App\Model\Sales\SalesInvoice;
use App\Model\Sales\SalesInvoiceDetails;
use App\Model\Sales\SalesCustomer;


use App\Model\Sales\SalesDeliveryChallan;
use App\Model\Sales\SalesDeliveryChallanDetails;

use App\Model\Inventory\InventoryCurrentStock;
use App\Model\Inventory\StockTransectionLog;
use App\Model\Inventory\StockTransectionLogDetails;
use App\Lib\Enumerations\StockTransactionType;

use App\Model\Accounts\AccountsSalesInvoiceVoucher;
use App\Model\Accounts\AccountsSalesInvoiceVoucherDetails;

use Illuminate\Support\Facades\Auth;
use DB;

class SalesDeliveryChallanController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $query = DB::table('sales_delivery_challans')
                ->leftJoin('sales_customers', function($join){
                    $join->on('sales_delivery_challans.sales_customer_id', '=', 'sales_customers.id');
                    $join->on('sales_delivery_challans.sales_customer_id','!=',DB::raw("''"));
                })
                ->leftJoin('purchase_ware_houses', 'sales_delivery_challans.sales_warehouse_id', '=', 'purchase_ware_houses.id')
                ->leftJoin('sales_invoices', 'sales_delivery_challans.sales_invoice_id', '=', 'sales_invoices.id')
                ->leftJoin('users as ura', 'sales_delivery_challans.created_by','=','ura.id')
                ->leftJoin('users as ured', 'sales_delivery_challans.updated_by','=','ured.id')
                ->leftJoin('users as urea', 'sales_delivery_challans.approve_by','=','urea.id')
                ->whereNull('sales_delivery_challans.deleted_at')
                ->select(['sales_delivery_challans.id AS id',
                    'sales_delivery_challans.sales_delivery_no',
                    'sales_delivery_challans.sales_delivery_date',
                    'sales_delivery_challans.sales_delivery_location',
                    'sales_delivery_challans.sales_delivery_tender',
                    'sales_delivery_challans.sales_delivery_total_qty',
                    'sales_delivery_challans.sales_delivery_total_price',
                    'sales_delivery_challans.created_by',
                    'sales_delivery_challans.updated_by',
                    'sales_delivery_challans.approve_by',
                    'sales_delivery_challans.approve_status',
                    'sales_customers.sales_customer_name',
                    'sales_invoices.sales_invoices_no',
                    'purchase_ware_houses.purchase_ware_houses_name',
                    'ura.user_name as ad_name',
                    'ured.user_name as ed_name',
                    'urea.user_name as ap_name'
                ]);

            return datatables()->of($query)->toJson();
        }


        $invoiceLists  = SalesInvoice::where('status', '1')->where('approve_status', '1')->where('challan_add_status', '0')->get();
        $warehouseLists  = PurchaseWareHouse::where('status', '1')->get();
        $customerLists  = SalesCustomer::where('status', '1')->get();

        return view('sales.sales_section.sales_challan_delivery',compact('invoiceLists','customerLists','warehouseLists'));
    }

    public function store(Request $request)
    {
        date_default_timezone_set("Asia/Dhaka");

        $inv_no_like = 'DC';
        $rtn_val =InvStore($request->sales_delivery_no, $inv_no_like,
            'sales_delivery_challans','sales_delivery_no');

        $request->merge(['sales_delivery_no' => $rtn_val]) ;


        $this->validate($request, [
            'sales_delivery_no' => 'required|unique:sales_delivery_challans,sales_delivery_no',
            'sales_delivery_date' => 'required',
            'sales_invoice_id' => 'required',
            'sales_customer_id' => 'required',
            'sales_warehouse_id' => 'required',
            'sales_delivery_location' => 'required',
            'sales_delivery_total_qty' => 'required',
            'sales_delivery_total_price' => 'required',

            'details.*.sales_delivery_details_product_id' => 'required',
            'details.*.sales_delivery_details_unit' => 'required',
            'details.*.sales_delivery_details_unit_price' => 'required',
            'details.*.sales_delivery_details_qty' => 'required',
            'details.*.sales_delivery_details_total_price' => 'required',
        ], [
            'details.*.sales_delivery_details_product_id.required' => 'required',
            'details.*.sales_delivery_details_unit.required' => 'required',
            'details.*.sales_delivery_details_unit_price.required' => 'required',
            'details.*.sales_delivery_details_qty.required' => 'required',
            'details.*.sales_delivery_details_total_price.required' => 'required',
        ]);

        if($request->sales_delivery_date!= ''){
            $date = str_replace('/', '-', $request->sales_delivery_date);
            $date_val =date('Y-m-d', strtotime($date));
        }else{
            $date_val =date('Y-m-d');
        }


        $customer = $request->sales_customer_id;
        $tender = $request->sales_delivery_tender;
        $serial = $request->sales_delivery_no;
        $ref_tbl_id = $request->sales_invoice_id;
        $warehouse_id=$request->sales_warehouse_id;

        $app= $request->approve;

        $data = [
            'sales_delivery_no' 				=> $serial,
            'sales_delivery_date' 				=> $date_val,
            'sales_invoice_id' 					=> $ref_tbl_id,
            'sales_customer_id' 				=> $customer,
            'sales_delivery_tender' 			=> $tender,
            'sales_warehouse_id' 				=> $warehouse_id,
            'sales_delivery_location'           => $request->sales_delivery_location,
            'sales_delivery_narration' 			=> $request->sales_delivery_narration,
            'sales_delivery_total_qty' 			=> $request->sales_delivery_total_qty,
            'sales_delivery_sub_total_price' 	=> $request->sales_delivery_sub_total_price,
            'sales_delivery_ati' 		        => $request->sales_delivery_ati,
            'sales_delivery_commission' 		=> $request->sales_delivery_commission,
            'sales_delivery_discount' 			=> $request->sales_delivery_discount,
            'sales_delivery_vat' 				=> $request->sales_delivery_vat,
            'sales_delivery_vat_type' 			=> $request->sales_delivery_vat_type,
            'sales_delivery_total_price' 		=> $request->sales_delivery_total_price,
            'created_by' 						=> Auth::user()->id,
        ];

        try {
            DB::beginTransaction();

            $result = SalesDeliveryChallan::create($data);
            $details = $this->dataFormat($request, $result->id);
            SalesDeliveryChallanDetails::insert($details);

            if($app ==1){
                $this->approve($result->id);

                if($request->updated_by == Null){
                    DB::table('sales_delivery_challans')->where('id', $result->id)->update(array('updated_by' => Auth::user()->id));
                }
            }

            DB::commit();
            //return response()->json(['status' => 'success', 'message' => 'Purchase Requisition successfully saved!']);
            return response()->json(['status' => 'success', 'message' => "Sales Delivery Challan # " . $request->sales_delivery_no .' Successfully Saved!']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    public function show($id)
    {
        try {
            $showData = SalesDeliveryChallan::with('get_customer','get_wh','get_inv')->FindOrFail($id);
            $showDetailsData = SalesDeliveryChallanDetails::with('get_chln','get_prod')
                        ->where('sales_delivery_id',$id)->get();

            $invoice_id     = $showData->get_inv->sales_invoices_no;
            $customer_id    = $showData->get_customer->sales_customer_name;
            $warehouse_id   = $showData->get_wh->purchase_ware_houses_name;

            $data = [
                'id'    => $showData->id,
                'sales_delivery_no'              => $showData->sales_delivery_no,
                'sales_delivery_date'            => date('d/m/Y', strtotime($showData->sales_delivery_date)),
                'sales_invoice_id'               => $invoice_id,
                'sales_customer_id'              => $customer_id,
                'sales_warehouse_id'             => $warehouse_id,
                'sales_delivery_tender'          => $showData->sales_delivery_tender,
                'sales_delivery_narration'       => $showData->sales_delivery_narration,
                'sales_delivery_location'        => $showData->sales_delivery_location,
                'sales_delivery_total_qty'       => $showData->sales_delivery_total_qty,
                'sales_delivery_sub_total_price' => $showData->sales_delivery_sub_total_price,
                'sales_delivery_commission'      => $showData->sales_delivery_commission,
                'sales_delivery_discount'        => $showData->sales_delivery_discount,
				'sales_delivery_ait'             => $showData->sales_delivery_ati,
                'sales_delivery_vat'             => $showData->sales_delivery_vat,
                'sales_delivery_vat_type'        => $showData->sales_delivery_vat_type,
                'sales_delivery_total_price'     => $showData->sales_delivery_total_price,
                'total_pricein_word'             => getCurrency($showData->sales_delivery_total_price),
                'details'                        => $showDetailsData,
            ];

            return response()->json(['status'=>'success','data'=>$data]);
        } catch(\Exception $e){
            return response()->json(['status'=>'error','data'=>[]]);
        }
    }

    public function showWithoutPrice($id){
        try {
            $showData = SalesDeliveryChallan::with('get_customer','get_wh','get_inv')->FindOrFail($id);
            $showDetailsData = SalesDeliveryChallanDetails::with('get_chln','get_prod')
                ->where('sales_delivery_id',$id)->get();

            $invoice_id     = $showData->get_inv->sales_invoices_no;
            $customer_id    = $showData->get_customer->sales_customer_name;
            $warehouse_id   = $showData->get_wh->purchase_ware_houses_name;


            $data = [
                'id'    => $showData->id,
                'sales_delivery_no'              => $showData->sales_delivery_no,
                'sales_delivery_date'            => date('d/m/Y', strtotime($showData->sales_delivery_date)),
                'sales_invoice_id'               => $invoice_id,
                'sales_customer_id'              => $customer_id,
                'sales_warehouse_id'             => $warehouse_id,
                'sales_delivery_tender'          => $showData->sales_delivery_tender,
                'sales_delivery_narration'       => $showData->sales_delivery_narration,
                'sales_delivery_location'        => $showData->sales_delivery_location,
                'sales_delivery_total_qty'       => $showData->sales_delivery_total_qty,
                'sales_delivery_sub_total_price' => $showData->sales_delivery_sub_total_price,
                'sales_delivery_commission'      => $showData->sales_delivery_commission,
                'sales_delivery_discount'        => $showData->sales_delivery_discount,
                'sales_delivery_ait'             => $showData->sales_delivery_ati,
                'sales_delivery_vat'             => $showData->sales_delivery_vat,
                'sales_delivery_vat_type'        => $showData->sales_delivery_vat_type,
                'sales_delivery_total_price'     => $showData->sales_delivery_total_price,
                'total_pricein_word'             => getCurrency($showData->sales_delivery_total_price),
                'details'                        => $showDetailsData,

            ];

            return response()->json(['status'=>'success','data'=>$data]);
        } catch(\Exception $e){
            return response()->json(['status'=>'error','data'=>[]]);
        }
    }

    public function edit($id)
    {
        try {
            $editModeData = SalesDeliveryChallan::FindOrFail($id);
            $editModeDetailsData = SalesDeliveryChallanDetails::where('sales_delivery_id',$id)->get();
            $dataFormat = [];

            foreach ($editModeDetailsData as $key => $value) {
                $salesInvoicelist  = SalesInvoiceDetails::where('sales_invoice_id', $editModeData->sales_invoice_id)
                    ->where('sales_invoice_details_product_id', $value->sales_delivery_details_product_id)
                    ->first();
                $qty = DB::table('inventory_current_stocks as st')
                    ->where('st.inventory_current_stocks_product_id',$value->sales_delivery_details_product_id)
                    ->where('st.inventory_current_stocks_warehouse_id',$editModeData->sales_warehouse_id)
                    ->first()->inventory_stocks_current_qty;

                $dataFormat[] = [
                'id'                                    =>  $value->id,
                'sales_delivery_id'                     => $value->sales_delivery_id,
                'sales_delivery_details_product_id'     => $value->sales_delivery_details_product_id,
                'sales_delivery_details_description'    => $value->sales_delivery_details_description,
                'sales_delivery_details_unit'           => $value->sales_delivery_details_unit,
                'sales_delivery_details_unit_price'     => $value->sales_delivery_details_unit_price,
                'current_qty'                           => $qty,
                'sales_delivery_details_qty'            => $value->sales_delivery_details_qty,
                'sales_delivery_details_pervious_qty'   => $value->sales_delivery_details_qty,
                'sales_delivery_details_order_qty'      => $salesInvoicelist->sales_invoice_details_qty,
                'sales_delivery_details_total_price'    => $value->sales_delivery_details_total_price,
                'sales_delivery_details_discount_type'  => $value->sales_delivery_details_discount_type,
                'sales_delivery_details_discount'       => $value->sales_delivery_details_discount,
                'sales_delivery_details_vat_sub'        => $value->sales_delivery_details_vat_sub,
                ];
            }
            $data = [
                'id'                                => $editModeData->id,
                'sales_delivery_no'                 => $editModeData->sales_delivery_no,
                'sales_delivery_date'               => date('d/m/Y', strtotime($editModeData->sales_delivery_date)),
                'sales_invoice_id'                  => $editModeData->sales_invoice_id,
                'sales_customer_id'                 => $editModeData->sales_customer_id,
                'sales_delivery_tender'             => $editModeData->sales_delivery_tender,
                'sales_warehouse_id'                => $editModeData->sales_warehouse_id,
                'sales_delivery_location'           => $editModeData->sales_delivery_location,
                'sales_delivery_narration'          => $editModeData->sales_delivery_narration,
                'sales_delivery_total_qty'          => $editModeData->sales_delivery_total_qty,
                'sales_delivery_sub_total_price'    => $editModeData->sales_delivery_sub_total_price,
                'sales_delivery_ati'                => $editModeData->sales_delivery_ati,
                'sales_delivery_commission'         => $editModeData->sales_delivery_commission,
                'sales_delivery_discount'           => $editModeData->sales_delivery_discount,
                'sales_delivery_vat'                => $editModeData->sales_delivery_vat,
                'sales_delivery_vat_type'           => $editModeData->sales_delivery_vat_type,
                'sales_delivery_total_price'        => $editModeData->sales_delivery_total_price,
                'approve'                           => '',
                'deleteID'                          => [],
                'details'                           => $dataFormat,
            ];
           $pid_list = ProductionProducts::Select('production_products.*', 'production_measure_units.measure_unit','inventory_current_stocks.inventory_stocks_current_qty')
               ->join('production_measure_units', 'production_products.measure_unit_id', '=', 'production_measure_units.id')
               ->leftJoin('inventory_current_stocks', 'inventory_current_stocks.inventory_current_stocks_product_id','=','production_products.id')
               ->where('production_products.status', '1')
               ->where('production_products.is_raw_material_status', '0')
               ->where('inventory_current_stocks.inventory_current_stocks_warehouse_id', $editModeData->sales_warehouse_id)
               ->get();
           $test_val = [
               'edit_mode' => $data,
               'pid_list' => $pid_list,
           ];
            return response()->json(['status'=>'success','data'=>$test_val]);
        } catch(\Exception $e){
            return response()->json(['status'=>'error','data'=>[]]);
        }
    }

    public function update(Request $request, $id)
    {
        date_default_timezone_set("Asia/Dhaka");
        $this->validate($request, [
            'sales_delivery_no'             => 'required|unique:sales_delivery_challans,sales_delivery_no,'.$id.',id',
            'sales_delivery_date'           => 'required',
            'sales_invoice_id'              => 'required',
            'sales_customer_id'             => 'required',
            'sales_warehouse_id'            => 'required',
            'sales_delivery_location'       => 'required',
            'sales_delivery_total_qty'      => 'required',
            'sales_delivery_total_price'    => 'required',

            'details.*.sales_delivery_details_product_id'   => 'required',
            'details.*.sales_delivery_details_unit'         => 'required',
            'details.*.sales_delivery_details_unit_price'   => 'required',
            'details.*.sales_delivery_details_qty'          => 'required',
            'details.*.sales_delivery_details_total_price'  => 'required',
        ], [
            'details.*.sales_delivery_details_product_id.required'  => 'required',
            'details.*.sales_delivery_details_unit.required'        => 'required',
            'details.*.sales_delivery_details_unit_price.required'  => 'required',
            'details.*.sales_delivery_details_qty.required'         => 'required',
            'details.*.sales_delivery_details_total_price.required' => 'required',
        ]);

        if($request->sales_delivery_date!=''){
            $date = str_replace('/', '-', $request->sales_delivery_date);
            $date_val =date('Y-m-d', strtotime($date));
        }else{
            $date_val =date('Y-m-d');
        }

        $customer       = $request->sales_customer_id;
        $tender         = $request->sales_delivery_tender;
        $serial         = $request->sales_delivery_no;
        $ref_tbl_id     = $request->sales_invoice_id;
        $warehouseId    = $request->sales_warehouse_id;

        $app= $request->approve;

        try {
            DB::beginTransaction();

            $editModeData = SalesDeliveryChallan::FindOrFail($id);

            $editModeData->sales_delivery_no              = $serial;
            $editModeData->sales_delivery_date            = $date_val;
            $editModeData->sales_invoice_id               = $ref_tbl_id;
            $editModeData->sales_customer_id              = $customer;
            $editModeData->sales_delivery_tender          = $tender;
            $editModeData->sales_warehouse_id             = $warehouseId;
            $editModeData->sales_delivery_location        = $request->sales_delivery_location;
            $editModeData->sales_delivery_narration       = $request->sales_delivery_narration;
            $editModeData->sales_delivery_total_qty       = $request->sales_delivery_total_qty;
            $editModeData->sales_delivery_sub_total_price = $request->sales_delivery_sub_total_price;
            $editModeData->sales_delivery_commission      = $request->sales_delivery_commission;
            $editModeData->sales_delivery_ati             = $request->sales_delivery_ati;
            $editModeData->sales_delivery_discount        = $request->sales_delivery_discount;
            $editModeData->sales_delivery_vat             = $request->sales_delivery_vat;
            $editModeData->sales_delivery_vat_type        = $request->sales_delivery_vat_type;
            $editModeData->sales_delivery_total_price     = $request->sales_delivery_total_price;


            if ($app!=1) {
                $editModeData->updated_by = Auth::user()->id;
            }

            $editModeData->save();


            /***************************************************** DETAILS TRANSECTION **********************************************************/

            if (count($request->deleteID) > 0) {
                SalesDeliveryChallanDetails::whereIn('id', $request->deleteID)->delete();
            }

            $dataFormat = [];
            $count = count($request->details);
            for ($i = 0; $i < $count; $i++) {
                if (isset($request->details[$i]['id']) && $request->details[$i]['id'] !='') {
                    $updateData = [
                        'sales_delivery_id'                     => $id,
                        'sales_delivery_details_product_id'     => $request->details[$i]['sales_delivery_details_product_id'],
                        'sales_delivery_details_description'    => $request->details[$i]['sales_delivery_details_description'],
                        'sales_delivery_details_unit'           => $request->details[$i]['sales_delivery_details_unit'],
                        'sales_delivery_details_unit_price'     => $request->details[$i]['sales_delivery_details_unit_price'],
                        'sales_delivery_details_qty'            => $request->details[$i]['sales_delivery_details_qty'],
                        'sales_delivery_details_total_price'    => $request->details[$i]['sales_delivery_details_total_price'],
                        'sales_delivery_details_discount_type'  => $request->details[$i]['sales_delivery_details_discount_type'],
                        'sales_delivery_details_discount'       => $request->details[$i]['sales_delivery_details_discount'],
                        'sales_delivery_details_vat_sub'        => $request->details[$i]['sales_delivery_details_vat_sub'],
                    ];
                    SalesDeliveryChallanDetails::where('id', $request->details[$i]['id'])->update($updateData);
                } else {
                    $dataFormat[$i] =[
                        'sales_delivery_id'                     => $id,
                        'sales_delivery_details_product_id'     => $request->details[$i]['sales_delivery_details_product_id'],
                        'sales_delivery_details_description'    => $request->details[$i]['sales_delivery_details_description'],
                        'sales_delivery_details_unit'           => $request->details[$i]['sales_delivery_details_unit'],
                        'sales_delivery_details_unit_price'     => $request->details[$i]['sales_delivery_details_unit_price'],
                        'sales_delivery_details_qty'            => $request->details[$i]['sales_delivery_details_qty'],
                        'sales_delivery_details_total_price'    => $request->details[$i]['sales_delivery_details_total_price'],
                        'sales_delivery_details_discount_type'  => $request->details[$i]['sales_delivery_details_discount_type'],
                        'sales_delivery_details_discount'       => $request->details[$i]['sales_delivery_details_discount'],
                        'sales_delivery_details_vat_sub'        => $request->details[$i]['sales_delivery_details_vat_sub'],
                    ];
                }
            }

            if(count($dataFormat) > 0){
                SalesDeliveryChallanDetails::insert($dataFormat);
            }

            if($app ==1){
                $this->approve($id);
            }
            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Sales Delivery successfully updated!']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    public function approve($id)
    {
        try {
            $appData =SalesDeliveryChallan::where('id',$id)->first();
            if($appData->approve_status == 0)
            {
                $appDetailsData = SalesDeliveryChallanDetails::where('sales_delivery_id',$id)->get();

                $customer 		    = $appData->sales_customer_id;
                $tender 		    = $appData->sales_delivery_tender;
                $serial 		    = $appData->sales_delivery_no;
                $ref_tbl_id 	    = $appData->sales_invoice_id;
                $warehouseId	    = $appData->sales_warehouse_id;

                $date 			    = $appData->sales_delivery_date;
                $ref_tbl		    = 'sales_invoices';
                $log_tbl 		    = 'sales_delivery_challans';
                $sup_cus_tabl 	    = 'sales_customers';
                $log_type 		    = StockTransactionType::$sales_delivery;
                $total_qty          = $appData->sales_delivery_total_qty;
                $total_chl_price    = $appData->sales_delivery_total_price;

                /****************************************** REMOVE STOCK INFORMATION  **********************************************************/
                foreach($appDetailsData as $val){
                    $checkExists = InventoryCurrentStock::where('inventory_current_stocks_product_id',$val->sales_delivery_details_product_id)
                        ->where('inventory_current_stocks_warehouse_id',$warehouseId)
                        ->first();
                    if($checkExists){
                        InventoryCurrentStock::where('inventory_current_stocks_product_id',$val->sales_delivery_details_product_id)
                            ->where('inventory_current_stocks_warehouse_id',$warehouseId)
                            ->decrement('inventory_stocks_current_qty',  $val->sales_delivery_details_qty);

                        $Ics=InventoryCurrentStock::where('inventory_current_stocks_product_id', $val->sales_delivery_details_product_id)
                            ->where('inventory_current_stocks_warehouse_id', $warehouseId)->first();

                        if($Ics->unit_price == '0' || $Ics->unit_price == ''){
                            $unitPrice = $val->sales_delivery_details_unit_price;
                        }else{
                            $unitPrice = $Ics->unit_price;
                        }

                        $total_price= $unitPrice * $Ics->inventory_stocks_current_qty;

                        InventoryCurrentStock::where('inventory_current_stocks_product_id', $val->sales_delivery_details_product_id)
                            ->where('inventory_current_stocks_warehouse_id', $warehouseId)
                            ->update(array(
                                'unit_price'    => round($unitPrice,2),
                                'total_price'   => round($total_price,2),
                            ));

                        ProductionProducts::where('id', $val->sales_delivery_details_product_id)
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
                    'stock_trans_total_price'               => $total_chl_price,
                    'created_by'                            => Auth::user()->id,
                ];
                $mainStock = StockTransectionLog::create($stock_log);

                $Stockdetails = [];
                $details_tbl = 'sales_delivery_challan_details';

                foreach($appDetailsData as $key => $row)
                {
                    //$w_info = InventoryCurrentStock::where('inventory_current_stocks_warehouse_id', $warehouseId)->where('inventory_current_stocks_product_id', $row->sales_delivery_details_product_id)->first();

                    $Stockdetails[] = [
                        'log_id'            => $mainStock->id,
                        'log_table_name'    => $details_tbl,
                        'log_table_id'      => $row->id,

                        'product_id'        => $row->sales_delivery_details_product_id,
                        'warehouse_id'      => $warehouseId,

                        'log_entry_qty'     => $row->sales_delivery_details_qty,
                        'log_open_qty'      => $Ics->inventory_stocks_current_qty + $row->sales_delivery_details_qty,
                        'log_current_qty'   => $Ics->inventory_stocks_current_qty,
                        'log_close_qty'     => $Ics->inventory_stocks_current_qty,

                        'log_unit_price'    => $row->sales_delivery_details_unit_price,
                        'log_total_price'   => $row->sales_delivery_details_total_price,

                        'add_sub'           => 0,

                        'entry_date'        => $date,
                        'created_by'        => Auth::user()->id,
                    ];
                }
                StockTransectionLogDetails::insert($Stockdetails);


                /****************************************** ACCOUNT INFORMATION  **********************************************************/
                $data_ac = [
                    'vouchers_no' 		=> "Ch-Inv-$id",
                    'sales_invoice_id' 	=> $ref_tbl_id,
                    'sales_challan_id' 	=> $id,
                    'sales_date' 		=> $date,
                    'customer_id' 		=> $customer,
                    'tender_no' 		=> $tender,
                    'payment_type' 		=> '1',
                    'total_qty' 		=> $appData->sales_delivery_total_qty,
                    'sub_total_price' 	=> $appData->sales_delivery_sub_total_price,
                    'commission' 		=> $appData->sales_delivery_commission,
                    'discount' 			=> $appData->sales_delivery_discount,
                    'vat' 				=> $appData->sales_delivery_vat,
                    'vat_type' 			=> $appData->sales_delivery_vat_type,
                    'total_price' 		=> $appData->sales_delivery_total_price,
                    'created_by' 		=> Auth::user()->id,
                ];
                $result_ac = AccountsSalesInvoiceVoucher::create($data_ac);


                /****************************************** ACCOUNT DETAILS INFORMATION  **************************************************/
                $dataFormatAc = [];
                $count = count($appDetailsData);
                for ($i = 0; $i < $count; $i++) {
                    $dataFormatAc[$i] = [
                        'ac_sales_voucher_id'   => $result_ac->id,//$result_ac->id
                        'product_id'            => $appDetailsData[$i]['sales_delivery_details_product_id'],
                        'remarks'               => $appDetailsData[$i]['sales_delivery_details_description'],
                        'm_unit'                => $appDetailsData[$i]['sales_delivery_details_unit'],
                        'unit_price'            => $appDetailsData[$i]['sales_delivery_details_unit_price'],
                        'discount_type'         => $appDetailsData[$i]['sales_delivery_details_discount_type'],
                        'discount'              => $appDetailsData[$i]['sales_delivery_details_discount'],
                        'vat_sub'               => $appDetailsData[$i]['sales_delivery_details_vat_sub'],
                        'qty'                   => $appDetailsData[$i]['sales_delivery_details_qty'],
                        'total_price'           => $appDetailsData[$i]['sales_delivery_details_total_price'],
                    ];
                }
                AccountsSalesInvoiceVoucherDetails::insert($dataFormatAc);

                /****************************************** APPROVE INFORMATION  **********************************************************/
                $sales_inv = DB::table('sales_invoices')->where('id', $appData->sales_invoice_id)->first();
                $challan_recive = DB::table("sales_delivery_challans")
                    ->select(DB::raw("SUM(sales_delivery_challans.sales_delivery_total_qty) as total_qty"))
                    ->where("sales_delivery_challans.sales_invoice_id",$appData->sales_invoice_id)
                    ->first();
                if($sales_inv->sales_invoices_total_qty <= $challan_recive->total_qty){
                    DB::table('sales_invoices')->where('id', $appData->sales_invoice_id)
                        ->update(array('challan_add_status' => 1));
                }
                DB::table('sales_delivery_challans')->where('id', $id)->update(array(
                    'approve_status'    => 1,
                    'approve_by'        => Auth::user()->id,
                    'approve_at'        => Carbon::now(),
                ));


                return response()->json(['status' => 'success', 'message' => 'Sales Challan Delivery successfully Approved!']);
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

            SalesDeliveryChallanDetails::where('sales_delivery_id',$id)->delete();
            SalesDeliveryChallan ::FindOrFail($id)->delete();
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Sales Delivery successfully Deleted !']);
        } elseif ($bug == 1451) {
            return response()->json(['status' => 'error', 'message' => 'This data is used another table,can not delete data']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }

    }

    public function salesInvoicelist($challan_no,$AddEditId,$id){
        $salesInv = DB::table('sales_invoices as inv')->select('inv.*')->where('inv.id', $id)->first();
        $contract = $salesInv->sales_invoices_contract_no;
        $warhouse = $salesInv->sales_invoices_warehouse_id;
        $customer = $salesInv->sales_invoices_customer_id;

        $salesInvoicelist  = SalesInvoiceDetails::where('sales_invoice_id', $id)->get();
        $dataFormat = [];
        $acl_amount = 0;
        $acl_qty = 0;
        foreach ($salesInvoicelist as $key => $value) {
            if($AddEditId == 0){
                $sum_prev =SalesDeliveryChallan::selectRaw('sales_delivery_challans.*,sum(sales_delivery_challan_details.sales_delivery_details_qty) as total')
                            ->join('sales_delivery_challan_details', 'sales_delivery_challans.id', '=', 'sales_delivery_challan_details.sales_delivery_id')
                            ->where('sales_delivery_challans.sales_invoice_id','=',$id )
                            ->where('sales_delivery_challan_details.sales_delivery_details_product_id' ,'=',$value->sales_invoice_details_product_id)
                            ->first();
                if($sum_prev->total){$total =$sum_prev->total;} else{$total='0';}
            }else{
                $sum_prev =SalesDeliveryChallan::selectRaw('sales_delivery_challans.*,sum(sales_delivery_challan_details.sales_delivery_details_qty) as total')
                    ->join('sales_delivery_challan_details', 'sales_delivery_challans.id', '=', 'sales_delivery_challan_details.sales_delivery_id')
                    ->where('sales_delivery_challans.id','!=',$AddEditId )
                    ->where('sales_delivery_challans.sales_invoice_id','=',$id )
                    ->where('sales_delivery_challan_details.sales_delivery_details_product_id' ,'=',$value->sales_invoice_details_product_id)
                    ->first();
                if($sum_prev->total){$total =$sum_prev->total;} else{$total='0';}
            }

            $qty = DB::table('inventory_current_stocks as st')
                    ->where('st.inventory_current_stocks_product_id',$value->sales_invoice_details_product_id)
                    ->where('st.inventory_current_stocks_warehouse_id',$warhouse)
                    ->first()->inventory_stocks_current_qty;
            $dataFormat[] = [
                'sales_delivery_details_product_id'     => $value->sales_invoice_details_product_id,
                'sales_delivery_details_description'    => $value->sales_invoice_details_description,
                'sales_delivery_details_unit'           => $value->sales_invoice_details_unit,
                'sales_delivery_details_unit_price'     => $value->sales_invoice_details_unit_price,
                'current_qty'                           => $qty,
                'sales_delivery_details_order_qty'      => $value->sales_invoice_details_qty,
                'sales_delivery_details_qty'            => ($value->sales_invoice_details_qty-$total),
                'sales_delivery_details_pervious_qty'   => $total,
                'sales_delivery_details_discount_type'  => 'Pct(%)',
                'sales_delivery_details_discount'       => 0,
                'sales_delivery_details_vat_sub'        => 'No Vat',
                'sales_delivery_details_total_price'    => $value->sales_invoice_details_total_price,
            ];
            $acl_qty = $acl_qty + ($value->sales_invoice_details_qty-$total);
            $acl_amount = $acl_amount + $value->sales_invoice_details_total_price;
        }

        $data = [
            'sales_delivery_no'                 => $challan_no,
            'sales_delivery_sub_total_price'    => $acl_amount,
            'sales_delivery_total_qty'          => $acl_qty ,
            'sales_delivery_commission'         => 0,
            'sales_delivery_ati'                => 0,
            'sales_delivery_discount'           => 0,
            'sales_delivery_vat'                => 0 ,
            'sales_delivery_date'               => date('d/m/Y'),
            'sales_invoice_id'                  => $id,
            'sales_customer_id'                 => $customer,
            'sales_delivery_tender'             => $contract,
            'sales_warehouse_id'                => $warhouse,
            'sales_delivery_narration'          => '',
            'sales_delivery_location'           => '',
            'sales_delivery_vat_type'           => 'Incl Vat',
            'sales_delivery_total_price'        => $acl_amount,
            'details'                           => $dataFormat,
        ];

        $productLists = ProductionProducts::Select('production_products.*', 'production_measure_units.measure_unit','inventory_current_stocks.inventory_stocks_current_qty')
            ->join('production_measure_units', 'production_products.measure_unit_id', '=', 'production_measure_units.id')
            ->leftJoin('inventory_current_stocks', 'inventory_current_stocks.inventory_current_stocks_product_id','=','production_products.id')
            ->where('production_products.status', '1')
            ->where('production_products.is_raw_material_status', '0')
            ->where('inventory_current_stocks.inventory_current_stocks_warehouse_id', $warhouse)
            ->get();

        $test_val = [
            'add_mode' => $data,
            'pid_list' => $productLists,
        ];
        return response()->json(['data'=>$test_val]);
    }

    public function SalesDeliveryChallanGenerate(){
        $id = SalesDeliveryChallan::withTrashed()->count();
        $currentId = $id+1;
        return 'DC'.date('Ym').$currentId;
    }

    public function listProduct($warehouseId){
        //DB::enableQueryLog();
        if($warehouseId == '__'){
            $productLists = ProductionProducts::Select('production_products.*', 'production_measure_units.measure_unit')
                ->where('production_products.status', '1')
                ->where('production_products.is_raw_material_status', '0')
                ->join('production_measure_units', 'production_products.measure_unit_id', '=', 'production_measure_units.id')
                ->get();
        }else{
            $productLists = ProductionProducts::Select('production_products.*', 'production_measure_units.measure_unit','inventory_current_stocks.inventory_stocks_current_qty')
                ->join('production_measure_units', 'production_products.measure_unit_id', '=', 'production_measure_units.id')
                ->leftJoin('inventory_current_stocks', 'inventory_current_stocks.inventory_current_stocks_product_id','=','production_products.id')
                ->where('production_products.status', '1')
                ->where('production_products.is_raw_material_status', '0')
                ->where('inventory_current_stocks.inventory_current_stocks_warehouse_id', $warehouseId)
                ->get();

        }
        //print_r(DB::getQueryLog($productLists));
        return $productLists;
    }

    private function dataFormat($request, $id)
    {
        $dataFormat = [];
        $count = count($request->details);
        for ($i = 0; $i < $count; $i++) {
            $dataFormat[$i] = [
                'sales_delivery_id'                     => $id,
                'sales_delivery_details_product_id'     => $request->details[$i]['sales_delivery_details_product_id'],
                'sales_delivery_details_description'    => $request->details[$i]['sales_delivery_details_description'],
                'sales_delivery_details_unit'           => $request->details[$i]['sales_delivery_details_unit'],
                'sales_delivery_details_unit_price'     => $request->details[$i]['sales_delivery_details_unit_price'],
                'sales_delivery_details_qty'            => $request->details[$i]['sales_delivery_details_qty'],
                'sales_delivery_details_total_price'    => $request->details[$i]['sales_delivery_details_total_price'],
                'sales_delivery_details_discount_type'  => $request->details[$i]['sales_delivery_details_discount_type'],
                'sales_delivery_details_discount'       => $request->details[$i]['sales_delivery_details_discount'],
                'sales_delivery_details_vat_sub'        => $request->details[$i]['sales_delivery_details_vat_sub'],
            ];
        }
        return $dataFormat;
    }

}
