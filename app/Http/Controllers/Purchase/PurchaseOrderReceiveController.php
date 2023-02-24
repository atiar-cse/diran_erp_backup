<?php

namespace App\Http\Controllers\Purchase;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Model\Production\ProductionProducts;
use App\Model\Purchase\PurchaseSupplierEntry;
use App\Model\Purchase\PurchaseWareHouse;
use App\Model\Purchase\PurchaseRequisition;
use App\Model\Purchase\PurchaseRequisitionDetails;
use App\Model\Purchase\PurchaseOrderReceive;
use App\Model\Purchase\PurchaseOrderReceiveDetails;
use App\Model\Purchase\PurchaseCostType;

use App\Model\Inventory\InventoryCurrentStock;
use App\Model\Inventory\StockTransectionLog;
use App\Model\Inventory\StockTransectionLogDetails;
use App\Lib\Enumerations\StockTransactionType;

use App\Model\Accounts\AccountsPurchaseOrderVoucher;
use App\Model\Accounts\AccountsPurchaseOrderVoucherDetails;

use Illuminate\Support\Facades\Auth;
use DB;
use Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class PurchaseOrderReceiveController extends Controller
{
    private $image_ext = ['jpg', 'jpeg', 'png', 'gif'];
    private $document_ext = ['doc', 'docx', 'pdf', 'odt','xlsx','txt'];

    public function index(Request $request)
    {        
        //DB::enableQueryLog();
        if ($request->ajax()) {

            $query = DB::table('purchase_order_receives')
                ->leftJoin('purchase_supplier_entries', 'purchase_order_receives.po_supplier_id', '=', 'purchase_supplier_entries.id')
                ->leftJoin('purchase_requisitions', 'purchase_order_receives.po_requisition_id', '=', 'purchase_requisitions.id')
                ->leftJoin('users as ura', 'purchase_order_receives.created_by','=','ura.id')
                ->leftJoin('users as ured', 'purchase_order_receives.updated_by','=','ured.id')
                ->leftJoin('users as urea', 'purchase_order_receives.approve_by','=','urea.id')
                ->whereNull('purchase_order_receives.deleted_at')
                ->select(['purchase_order_receives.id AS id',
                    'purchase_order_receives.po_order_no',
                    'purchase_order_receives.po_receive_date',
                    'purchase_order_receives.po_order_docs',
                    'purchase_order_receives.po_narration',
                    'purchase_order_receives.po_total_qty',
                    'purchase_order_receives.po_total_price',
                    'purchase_order_receives.created_by',
                    'purchase_order_receives.updated_by',
                    'purchase_order_receives.approve_by',
                    'purchase_order_receives.status',
                    'purchase_order_receives.approve_status',
                    'purchase_requisitions.purchase_requisition_no',
                    'purchase_supplier_entries.purchase_supplier_name',
                    'ura.user_name as ad_name',
                    'ured.user_name as ed_name',
                    'urea.user_name as ap_name'
                ]);

            return datatables()->of($query)->toJson();
        }

        $productLists  = ProductionProducts::Select('production_products.*', 'production_measure_units.measure_unit')
                                ->where('production_products.status', '1')
                                // ->where('production_products.is_raw_material_status', '1')
                                ->leftJoin('production_measure_units', 'production_products.measure_unit_id', '=', 'production_measure_units.id')
                                ->get();

        //print_r(DB::getQueryLog($productLists));
        $warehouseLists   = PurchaseWareHouse::where('status', '1')->get();
        $supplierLists    = PurchaseSupplierEntry::where('status', '1')->get();
        $costTypeLists    = PurchaseCostType::where('status', '1')->get();

        return view('purchase.purchase_section.purchase_receive',
            compact('productLists','supplierLists','warehouseLists','costTypeLists'));
    }

    public function store(Request $request)
    {
        date_default_timezone_set("Asia/Dhaka");

        $inv_no_like = 'GRN';
        $rtn_val =InvStore($request->po_order_no, $inv_no_like,
            'purchase_order_receives','po_order_no');

        $request->merge(['po_order_no' => $rtn_val]) ;

        if ($request->file){
            $max_size = (int)ini_get('upload_max_filesize') * 1000;
            $all_ext = implode(',', $this->allExtensions());

            $this->validate($request, [
                'file' => 'required|file|mimes:' . $all_ext . '|max:' . $max_size
            ]);

            $file = $request->file('file');
            $ext = $file->getClientOriginalExtension();
            $type = $this->getType($ext);
            $name = time().uniqid();
            $fileName = $name. '.' . $ext;
            $imagePath = 'purchase_oreder' . '/' . $type . '/';

            if (Storage::putFileAs($imagePath . '/', $file, $fileName)) {
                return response()->json(['file_path' => $imagePath.$fileName]);
            }else
            {
                return response()->json(['File upload error'], 500);
            }
        }

        $this->validate($request, [
            'po_order_no'               => 'required|unique:purchase_order_receives,po_order_no',
            'po_requisition_id'         => 'required',
            'po_warehouse_id'           => 'required',
            'po_supplier_id'            => 'required',

            'details.*.pod_product_id'  => 'required',
            'details.*.pod_qty'         => 'required',
            'details.*.pod_unit_price'  => 'required',
        ], [
            'details.*.pod_product_id.required'         => 'Required field.',
            'details.*.pod_qty.required'                => 'Required field.',
            'details.*.pod_unit_price.required'         => 'Required field.',
        ]);

        if($request->po_receive_date != ''){
            $date = str_replace('/', '-', $request->po_receive_date);
            $date_val =date('Y-m-d', strtotime($date));
        }else{
            $date_val =date('Y-m-d');
        }

        $app= $request->approve;
        $data = [
            'po_order_no'           => $request->po_order_no,
            'po_receive_date'       => $date_val,
            'po_narration'          => $request->po_narration,
            'po_warehouse_id'       => $request->po_warehouse_id,
            'po_order_docs'         => $request->po_order_docs,
            'po_supplier_id'        => $request->po_supplier_id,
            'po_requisition_id'     => $request->po_requisition_id,
            'po_total_qty'          => $request->po_total_qty,
            'po_total_price'        => $request->po_total_price,
            'created_by'            => Auth::user()->id,
        ];

        try {
            DB::beginTransaction();

            $result = PurchaseOrderReceive::create($data);
            $details = $this->dataFormat($request, $result->id);
            PurchaseOrderReceiveDetails::insert($details);

            if($app ==1){
                $this->approve($result->id);

                if($request->updated_by == Null){
                    DB::table('purchase_order_receives')->where('id', $result->id)->update(array('updated_by' => Auth::user()->id));
                }
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => "Purchase Order Req # " . $request->po_order_no .' Successfully Saved!']);
        } catch (\Exception $e) {
            echo $e->getMessage();
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => $msg]);
        }
    }

    public function show($id)
    {
        try {
            $showData = PurchaseOrderReceive::with('wh','sup','req')->where('id',$id)->first();
            $showDetailsData = PurchaseOrderReceiveDetails::with('get_po','get_product')
                                    ->where('purchase_order_receive_details.pod_order_id',$id)
                                    ->get();

            if($showData->po_supplier_id != null){
                $supplier = $showData->sup->purchase_supplier_name;
            }else{
                $supplier ='';
            }
            $requisiion = $showData->req->purchase_requisition_no;

            $data = [
                'id'    => $showData->id,
                'po_order_no'           => $showData->po_order_no,
                'po_supplier_id'        => $supplier,
                'po_requisition_id'     => $requisiion,
                'po_receive_date'       => date('d/m/Y', strtotime($showData->po_receive_date)),
                'po_narration'          => $showData->po_narration,
                'po_total_qty'          => $showData->po_total_qty,
                'po_total_price'        => $showData->po_total_price,
                'total_pricein_word'    => getCurrency($showData->po_total_price),
                'approve'               => $showData->approve_status,

                'details'    => $showDetailsData,
            ];
            return response()->json(['status'=>'success','data'=>$data]);
        } catch(\Exception $e){
            echo $e->getMessage();
            return response()->json(['status'=>'error','data'=>[]]);
        }
    }

    public function edit($id)
    {
        try {
            $editModeData = PurchaseOrderReceive::FindOrFail($id);
            $editModeDetailsData = PurchaseOrderReceiveDetails::where('pod_order_id',$id)->get();

            $dataFormat = [];
            foreach ($editModeDetailsData as $key => $value) {

                $pr_qty = DB::table("purchase_requisition_details as pr")
                            ->where("pr.pr_requisition_id",$editModeData->po_requisition_id)
                            ->where("pr.pr_product_id",$value->pod_product_id)
                            ->first();

                $dataFormat[] = [
                    'id'                => $value->id,
                    'pod_order_id'      => $value->pod_order_id,
                    'pod_product_id'    => $value->pod_product_id,
                    'pod_remarks'       => $value->pod_remarks,
                    'pod_unit'          => $value->pod_unit,
                    'pod_unit_price'    => $value->pod_unit_price,
                    'pod_qty'           => $value->pod_qty,
                    'pod_total_price'   => $value->pod_total_price,
                    'po_order_qty'      => $pr_qty? $pr_qty->pr_qty: 0,
                ];
            }

            $data = [
                'old_document_storage_link' =>  asset('/uploads/'.$editModeData->po_order_docs),
                'old_document'          =>  $editModeData->po_order_docs,
                'id'                    => $editModeData->id,
                'po_order_no'           => $editModeData->po_order_no,
                'po_supplier_id'        => $editModeData->po_supplier_id,
                'po_warehouse_id'       => $editModeData->po_warehouse_id,
                'po_requisition_id'     => $editModeData->po_requisition_id,
                'po_receive_date'       => date('d/m/Y', strtotime($editModeData->po_receive_date)),
                'po_narration'          => $editModeData->po_narration,
                'po_order_docs'         => $editModeData->po_order_docs,
                'po_total_qty'          => $editModeData->po_total_qty,
                'po_total_price'        => $editModeData->po_total_price,
                'approve'               => $editModeData->approve_status,
                'deleteID'              => [],
                'details'               => $dataFormat
            ];
            return response()->json(['status'=>'success','data'=>$data]);
        } catch(\Exception $e){
            echo $e->getMessage();
            return response()->json(['status'=>'error','data'=>[]]);
       }
    }

    public function update(Request $request, $id)
    {
        date_default_timezone_set("Asia/Dhaka");

        if ($request->hasFile('new_document')){
            if ($request->old_document_storage_link){
                unlink(public_path()."/uploads/".$request->old_document);
            }

            $max_size = (int)ini_get('upload_max_filesize') * 1000;
            $all_ext = implode(',', $this->allExtensions());

            $this->validate($request, [
                'new_document' => 'required|file|mimes:' . $all_ext . '|max:' . $max_size
            ]);

            $file = $request->file('new_document');
            $ext = $file->getClientOriginalExtension();
            $type = $this->getType($ext);
            $name = time().uniqid();
            $fileName = $name. '.' . $ext;

            $imagePath = 'purchase_oreder' . '/' . $type . '/';
            if (Storage::putFileAs($imagePath . '/', $file, $fileName)) {
                return response()->json(['file_path' => $imagePath.$fileName]);
            }else
            {
                return response()->json(['File upload error'], 500);
            }
        }

        $this->validate($request, [
            'po_order_no'                           => 'required|unique:purchase_order_receives,po_order_no,'.$id.',id',
            'po_requisition_id'                     => 'required',
            'po_warehouse_id'                       => 'required',
            'po_supplier_id'                        => 'required',
            'details.*.pod_product_id'              => 'required',
            'details.*.pod_qty'                     => 'required',
            'details.*.pod_unit_price'              => 'required',
        ], [
            'details.*.pod_product_id.required'     => 'Required field.',
            'details.*.pod_qty.required'            => 'Required field.',
            'details.*.pod_unit_price.required'     => 'Required field.',
        ]);

        if($request->po_receive_date != ''){
            $date = str_replace('/', '-', $request->po_receive_date);
            $date_val =date('Y-m-d', strtotime($date));
        }else{
            $date_val =date('Y-m-d');
        }

        $app= $request->approve;

        try {
            DB::beginTransaction();

            $editModeData = PurchaseOrderReceive::FindOrFail($id);

            $editModeData->po_order_no       = $request->po_order_no;
            $editModeData->po_receive_date   = $date_val;
            $editModeData->po_narration      = $request->po_narration;
            $editModeData->po_order_docs     = $request->po_order_docs;
            $editModeData->po_supplier_id    = $request->po_supplier_id;
            $editModeData->po_warehouse_id   = $request->po_warehouse_id;
            $editModeData->po_requisition_id = $request->po_requisition_id;
            $editModeData->po_total_qty      = $request->po_total_qty;
            $editModeData->po_total_price    = $request->po_total_price;

            if ($app!=1) {
                $editModeData->updated_by = Auth::user()->id;
            }

            $editModeData->save();

            //// details transection ////
            if (count($request->deleteID) > 0) {
                PurchaseOrderReceiveDetails::whereIn('id', $request->deleteID)->delete();
            }

            $dataFormat = [];
            $count = count($request->details);
            for ($i = 0; $i < $count; $i++) {
                if (isset($request->details[$i]['id']) && $request->details[$i]['id'] !='') {
                    $updateData = [
                        'pod_order_id'      => $id,
                        'pod_product_id'    => $request->details[$i]['pod_product_id'],
                        'pod_remarks'       => $request->details[$i]['pod_remarks'],
                        'pod_unit'          => $request->details[$i]['pod_unit'],
                        'pod_unit_price'    => $request->details[$i]['pod_unit_price'],
                        'pod_qty'           => $request->details[$i]['pod_qty'],
                        'pod_total_price'   => $request->details[$i]['pod_total_price']
                    ];
                    PurchaseOrderReceiveDetails::where('id', $request->details[$i]['id'])->update($updateData);
                } else {
                    $dataFormat[$i] =[
                        'pod_order_id'      => $id,
                        'pod_product_id'    => $request->details[$i]['pod_product_id'],
                        'pod_remarks'       => $request->details[$i]['pod_remarks'],
                        'pod_unit'          => $request->details[$i]['pod_unit'],
                        'pod_unit_price'    => $request->details[$i]['pod_unit_price'],
                        'pod_qty'           => $request->details[$i]['pod_qty'],
                        'pod_total_price'   => $request->details[$i]['pod_total_price']
                    ];
                }
            }

            if(count($dataFormat) > 0){
                PurchaseOrderReceiveDetails::insert($dataFormat);
            }

            if($app ==1){
                $this->approve($id);
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Purchase Order Receive successfully updated!']);
        } catch (\Exception $e) {
            echo $e->getMessage();
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    public function approve($id)
    {
       /******************************** Apporve Start ***********************************/
        $appData = DB::table('purchase_order_receives')->where('id',$id)->first();

        if($appData->approve_status == 0) {
            $appDetailsData = PurchaseOrderReceiveDetails::where('pod_order_id', $id)->get();

            $date           = $appData->po_receive_date;
            $log_type       = StockTransactionType::$purchase_entry;
            $serial         = $appData->po_order_no;

            $ref_tbl        = 'purchase_requisitions';
            $ref_tbl_id     = $appData->po_requisition_id;
            $log_tbl        = 'purchase_order_receives';

            $sup_cus_tabl   = 'purchase_supplier_entries';
            $supplier       = $appData->po_supplier_id;

            $warehouseId    = $appData->po_warehouse_id;
            $total_qty      = $appData->po_total_qty;
            $total_price    = $appData->po_total_price;
            $is_in_out      = 1; // 1= stock In

            /************************** INCREASE STOCK INFORMATION  *******************************/
            foreach ($appDetailsData as $val) {
                $checkExists = InventoryCurrentStock::where('inventory_current_stocks_product_id', $val->pod_product_id)
                                    ->where('inventory_current_stocks_warehouse_id', $warehouseId)
                                    ->first();
                if ($checkExists) {
                    InventoryCurrentStock::where('inventory_current_stocks_product_id', $val->pod_product_id)
                        ->where('inventory_current_stocks_warehouse_id', $warehouseId)
                        ->increment('inventory_stocks_current_qty', $val->pod_qty);

                    InventoryCurrentStock::where('inventory_current_stocks_product_id',$val->pod_product_id)
                        ->where('inventory_current_stocks_warehouse_id',$warehouseId)
                        ->increment('total_price', $val->pod_total_price);

                    //Calculate New Avg Inventory Unit Pricing
                    $new_total_qty = $checkExists->inventory_stocks_current_qty + $val->pod_qty;
                    $new_total_price = $checkExists->total_price + $val->pod_total_price;

                    if ($new_total_price > 0 && $new_total_qty > 0) {
                        $new_avg_unit_price = round( $new_total_price / $new_total_qty, 2);
                    } else {
                        $new_avg_unit_price = $val->pod_unit_price;
                    }
                    

                    InventoryCurrentStock::where('inventory_current_stocks_product_id', $val->pod_product_id)
                        ->where('inventory_current_stocks_warehouse_id', $warehouseId)
                        ->update(array(
                            'unit_price'    => $new_avg_unit_price,
                        ));

                    // ProductionProducts::where('id', $val->pod_product_id)
                    //     ->update(array('buying_price'=>$unit_price,'selling_price'=>$unit_price));

                } else {
                    $stock_entry = [
                        'inventory_current_stocks_product_id'   => $val->pod_product_id,
                        'inventory_current_stocks_warehouse_id' => $warehouseId,
                        'inventory_stocks_open_qty'             => $val->pod_qty,
                        'inventory_stocks_current_qty'          => $val->pod_qty,

                        'unit_price'                            => $val->pod_unit_price,
                        'total_price'                           => $val->pod_total_price,
                        'created_by'                            => Auth::user()->id,
                    ];
                    InventoryCurrentStock::create($stock_entry);

                    // ProductionProducts::where('id', $val->pod_product_id)
                    //     ->update(array(
                    //         'buying_price'=>$val->pod_unit_price,
                    //         'selling_price'=>$val->pod_unit_price));
                }
            }

            /**************************************** STOCK LOG INFO START ********************************************************/
            $stock_log = [
                'stock_transection_logs_date'               => $date,
                'stock_transection_logs_type'               => $log_type,
                'stock_transection_logs_number'             => $serial,
                'stock_transection_logs_ref_table_name'     => $ref_tbl,
                'stock_transection_logs_ref_table_id'       => $ref_tbl_id,
                'stock_transection_logs_table_name'         => $log_tbl,
                'stock_transection_logs_table_id'           => $id,
                'stock_trans_supp_cus_table_name'           => $sup_cus_tabl,
                'stock_trans_supp_cus_table_id'             => $supplier,
                'stock_trans_warehouse_id'                  => $warehouseId,
                'stock_trans_qty'                           => $total_qty,
                'stock_trans_total_price'                   => $total_price,
                'is_in_out'                                 => $is_in_out,
                'created_by'                                => Auth::user()->id,
            ];
            $mainStock = StockTransectionLog::create($stock_log);

            $Stockdetails = [];
            $details_tbl = 'purchase_order_receive_details';

            foreach($appDetailsData as $key => $row){
                $w_info = InventoryCurrentStock::where('inventory_current_stocks_warehouse_id', $warehouseId)
                    ->where('inventory_current_stocks_product_id', $row->pod_product_id)->first();

                $inventory_stocks_open_qty = 0;
                $inventory_stocks_current_qty = 0;
                $is_in_out = 1; // 1= stock In

                if ($w_info) {
                    $inventory_stocks_open_qty = $w_info->inventory_stocks_current_qty - $row->pod_qty;
                    //$inventory_stocks_open_qty = $w_info->inventory_stocks_open_qty;
                    $inventory_stocks_current_qty = $w_info->inventory_stocks_current_qty ;
                }

                $Stockdetails[] = [
                    'log_id'            => $mainStock->id,
                    'log_table_name'    => $details_tbl,
                    'log_table_id'      => $row->id,

                    'product_id'        => $row->pod_product_id,
                    'warehouse_id'      => $warehouseId,

                    'log_entry_qty'     => $row->pod_qty,
                    'log_open_qty'      => $inventory_stocks_open_qty,
                    'log_current_qty'   => $inventory_stocks_current_qty,
                    'log_close_qty'     => $inventory_stocks_current_qty,

                    'log_unit_price'    => $row->pod_unit_price,
                    'log_total_price'   => $row->pod_total_price,

                    'add_sub'           => 1,
                    'is_in_out'         => $is_in_out,

                    'entry_date'        => $date,
                    'created_by'        => Auth::user()->id,
                ];
            }
            StockTransectionLogDetails::insert($Stockdetails);

            /************************************* ACCOUNT INFORMATION  *****************************************/
            $data_ac = [
                'vouchers_no'       => "PO-StockE-$id" ,    //$this->PurchaseOrderVoucherGenerate(),
                'purchase_stock_id' => $id,
                'purchase_date'     => $date,
                'supplier_id'       => $supplier,
                'payment_type'      => '1',
                'total_qty'         => $appData->po_total_qty,
                'total_price'       => $appData->po_total_price,
                'created_by'        => Auth::user()->id,
            ];
            $result_ac = AccountsPurchaseOrderVoucher::create($data_ac);


            /*********************************** ACCOUNT DETAILS INFORMATION  *****************************************/
            $dataFormatAc = [];
            $count = count($appDetailsData);
            for ($i = 0; $i < $count; $i++) {
                $dataFormatAc[$i] = [
                    'ac_pruchase_stock_voucher_id'  => $result_ac->id,
                    'product_id'                    => $appDetailsData[$i]['pod_product_id'],
                    'remarks'                       => $appDetailsData[$i]['pod_remarks'],
                    'm_unit'                        => $appDetailsData[$i]['pod_unit'],
                    'unit_price'                    => $appDetailsData[$i]['pod_unit_price'],
                    'qty'                           => $appDetailsData[$i]['pod_qty'],
                    'total_price'                   => $appDetailsData[$i]['pod_total_price'],
                ];
            }
            AccountsPurchaseOrderVoucherDetails::insert($dataFormatAc);


            /****************************************** APPROVE INFORMATION  **********************************************************/
            $po_req = DB::table('purchase_requisitions')->where('id', $appData->po_requisition_id)->first();

            $qty_form_recive = DB::table("purchase_order_receives")
                ->select(DB::raw("SUM(purchase_order_receives.po_total_qty) as total_qty"))
                ->where("purchase_order_receives.po_requisition_id",$appData->po_requisition_id)
                ->first();
            if($po_req->purchase_requisition_total_qty <= $qty_form_recive->total_qty){
                DB::table('purchase_requisitions')->where('id', $appData->po_requisition_id)->update(array('stock_enrty_status' => 1));
            }

            DB::table('purchase_order_receives')->where('id', $id)->update(array(
                'approve_status'    => 1,
                'approve_by'        => Auth::user()->id,
                'approve_at'        => Carbon::now(),
            ));
        }
    }

    public function destroy($id)
    {
        try{
            PurchaseOrderReceiveDetails::where('pod_order_id',$id)->delete();
            PurchaseOrderReceive::FindOrFail($id)->delete();
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Purchase Order successfully Deleted !']);
        } elseif ($bug == 1451) {
            return response()->json(['status' => 'error', 'message' => 'This data is used another table,can not delete data']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    public function PoOrderGenerate(){
        $id = PurchaseOrderReceive::withTrashed()->count();
        $currentId = $id+1;
        return 'GRN'.date('Ym').$currentId;
    }

    public function porequisitionlist($id){

        $poPequisitionList  = PurchaseRequisitionDetails::where('pr_requisition_id', $id)->get();

        $dataFormat = [];
        foreach ($poPequisitionList as $key => $value) {

            $qty_form_recive = DB::table("purchase_order_receive_details")
                                ->leftJoin('purchase_order_receives', 'purchase_order_receive_details.pod_order_id', '=', 'purchase_order_receives.id')
                                ->where("purchase_order_receives.po_requisition_id",$id)
                                ->where("purchase_order_receive_details.pod_product_id",$value->pr_product_id)
                                ->first();

            if($qty_form_recive){
                $qty = $value->pr_qty - $qty_form_recive->pod_qty;
                $total_price=  $qty * $value->pr_unit_price;
            }else{
                $qty = $value->pr_qty;
                $total_price=  $qty * $value->pr_unit_price;
            }
            $dataFormat[] = [
                'pod_product_id'    => $value->pr_product_id,
                'pod_remarks'       => $value->pr_remarks,
                'pod_unit'          => $value->pr_unit,
                'pod_unit_price'    => $value->pr_unit_price,
                'pod_qty'           => $qty,
                'pod_total_price'   => $total_price,
                'po_order_qty'      => $value->pr_qty,
            ];
        }
        return $dataFormat;
    }

    private function dataFormat($request, $id)
    {
        $dataFormat = [];
        $count = count($request->details);
        for ($i = 0; $i < $count; $i++) {
            $dataFormat[$i] = [
                'pod_order_id'          => $id,
                'pod_product_id'        => $request->details[$i]['pod_product_id'],
                'pod_remarks'           => $request->details[$i]['pod_remarks'],
                'pod_unit'              => $request->details[$i]['pod_unit'],
                'pod_unit_price'        => $request->details[$i]['pod_unit_price'],
                'pod_qty'               => $request->details[$i]['pod_qty'],
                'pod_total_price'       => $request->details[$i]['pod_total_price']
            ];
        }

        return $dataFormat;
    }

    private function getType($ext)
    {
        if (in_array($ext, $this->image_ext)) {
            return 'image';
        }
        if (in_array($ext, $this->document_ext)) {
            return 'document';
        }
    }

    private function allExtensions()
    {
        return array_merge($this->image_ext, $this->document_ext);
    }
}
