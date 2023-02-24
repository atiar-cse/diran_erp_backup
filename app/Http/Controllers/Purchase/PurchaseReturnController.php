<?php

namespace App\Http\Controllers\Purchase;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Model\Production\ProductionProducts;
use App\Model\Purchase\PurchaseWareHouse;
use App\Model\Purchase\PurchaseReturnType;
use App\Model\Purchase\PurchaseSupplierEntry;

use App\Model\Purchase\PurchaseReturn;
use App\Model\Purchase\PurchaseReturnDetails;

use App\Model\Inventory\InventoryCurrentStock;
use App\Model\Inventory\StockTransectionLog;
use App\Model\Inventory\StockTransectionLogDetails;
use App\Lib\Enumerations\StockTransactionType;

use App\Model\Accounts\AccountsPurchaseReturnVoucher;
use App\Model\Accounts\AccountsPurchaseReturnVoucherDetails;

use Illuminate\Support\Facades\Auth;
use DB;

class PurchaseReturnController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $query = DB::table('purchase_returns')
                ->leftJoin('purchase_supplier_entries', 'purchase_returns.po_rtn_supplier_id', '=', 'purchase_supplier_entries.id')
                ->leftJoin('purchase_ware_houses', 'purchase_returns.po_rtn_warehouse_id', '=', 'purchase_ware_houses.id')
                ->leftJoin('purchase_return_types', 'purchase_returns.po_rtn_issue_type_id', '=', 'purchase_return_types.id')
                ->leftJoin('users as ura', 'purchase_returns.created_by','=','ura.id')
                ->leftJoin('users as ured', 'purchase_returns.updated_by','=','ured.id')
                ->leftJoin('users as urea', 'purchase_returns.approve_by','=','urea.id')
                ->whereNull('purchase_returns.deleted_at')
                ->select(['purchase_returns.id AS id',
                    'purchase_returns.po_return_no',
                    'purchase_returns.po_return_date',
                    'purchase_returns.po_rtn_narration',
                    'purchase_returns.po_rtn_total_qty',
                    'purchase_returns.po_rtn_total_price',
                    'purchase_returns.created_by',
                    'purchase_returns.updated_by',
                    'purchase_returns.approve_by',
                    'purchase_returns.approve_status',
                    'purchase_supplier_entries.purchase_supplier_name',
                    'purchase_ware_houses.purchase_ware_houses_name',
                    'purchase_return_types.purchase_return_types_name',
                    'ura.user_name as ad_name',
                    'ured.user_name as ed_name',
                    'urea.user_name as ap_name'
                ]);

            return datatables()->of($query)->toJson();
        }

        $supplierLists  = PurchaseSupplierEntry::where('status', '1')->get();
        $warehouseLists = PurchaseWareHouse::where('status', '1')->get();
        $issueTypeLists = PurchaseReturnType::where('status', '1')->get();
        $productLists   = ProductionProducts::Select('production_products.*', 'production_measure_units.measure_unit')
            ->where('production_products.status', '1')
            // ->where('production_products.is_raw_material_status', '1')
            ->leftJoin('production_measure_units', 'production_products.measure_unit_id', '=', 'production_measure_units.id')
            ->get();

        return view('purchase.purchase_section.purchase_return',compact('supplierLists','warehouseLists',
            'issueTypeLists','productLists'));
    }



    public function store(Request $request)
    {
        date_default_timezone_set("Asia/Dhaka");

        $inv_no_like = 'PRTN';
        $rtn_val =InvStore($request->po_return_no, $inv_no_like,
            'purchase_returns','po_return_no');

        $request->merge(['po_return_no' => $rtn_val]) ;


        $this->validate($request, [
            'po_return_no'              => 'required|unique:purchase_returns,po_return_no',
            'po_rtn_supplier_id'        => 'required',
            'po_rtn_warehouse_id'       => 'required',
            'po_rtn_issue_type_id'      => 'required',

            'details.*.po_rtnd_product_id'      => 'required',
            'details.*.po_rtnd_qty'             => 'required',
            'details.*.po_rtnd_unit_price'      => 'required',
        ], [
            'details.*.po_rtnd_product_id.required'     => 'required',
            'details.*.po_rtnd_qty.required'            => 'required',
            'details.*.po_rtnd_unit_price.required'     => 'required',
        ]);

        if($request->po_return_date != ''){
            $date = str_replace('/', '-', $request->po_return_date);
            $date_val =date('Y-m-d', strtotime($date));
        }else{
           $date_val =date('Y-m-d');
        }


        $app= $request->approve;
        $data = [
            'po_return_no'              => $request->po_return_no,
            'po_return_date'            => $date_val,
            'po_rtn_supplier_id'        => $request->po_rtn_supplier_id,
            'po_rtn_warehouse_id'       => $request->po_rtn_warehouse_id,
            'po_rtn_issue_type_id'      => $request->po_rtn_issue_type_id,
            'po_rtn_narration'          => $request->po_rtn_narration,
            'po_rtn_total_qty'          => round($request->po_rtn_total_qty,2),
            'po_rtn_total_price'        => round($request->po_rtn_total_price,2),
            'created_by'                => Auth::user()->id,
        ];

        try {
            DB::beginTransaction();

            $result = PurchaseReturn::create($data);
            $details = $this->dataFormat($request, $result->id);
            PurchaseReturnDetails::insert($details);

            if($app ==1){
                $this->approve($result->id);

                if($request->updated_by == Null){
                    DB::table('purchase_returns')->where('id', $result->id)->update(array('updated_by' => Auth::user()->id));
                }

            }
            DB::commit();

            return response()->json(['status' => 'success', 'message' => "Purchase Return # " . $request->po_return_no .' Successfully Saved!']);
            //return response()->json(['status' => 'success', 'message' => 'Purchase Requisition successfully saved!']);
        } catch (\Exception $e) {
            //print($e->getMessage());
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    public function show($id)
    {
        try {
            $showData = PurchaseReturn::where('id',$id)->with('wh','sup','get_issue_type')->first();
            $showDetailsData = PurchaseReturnDetails::Select('purchase_return_details.*','production_products.product_name')
                ->leftJoin('production_products', 'purchase_return_details.po_rtnd_product_id', '=', 'production_products.id')
                ->where('purchase_return_details.po_rtnd_return_id',$id)
                ->get();

            $warehouse_id = $showData->wh->purchase_ware_houses_name;
            $supplier_id = $showData->sup->purchase_supplier_name;
            $return_type = $showData->get_issue_type->purchase_return_types_name;

            $data = [
                'id'    => $showData->id,
                'po_return_no'          => $showData->po_return_no,
                'po_return_date'        => date('d/m/Y', strtotime($showData->po_return_date)),
                'po_rtn_supplier_id'    => $supplier_id,
                'po_rtn_warehouse_id'   => $warehouse_id,
                'po_rtn_issue_type_id'  => $return_type,
                'po_rtn_narration'      => $showData->po_rtn_narration,
                //po_rtn_docs: '',
                'po_rtn_total_qty'      => round($showData->po_rtn_total_qty,2),
                'po_rtn_total_price'    => round($showData->po_rtn_total_price,2),

                'total_pricein_word'    => getCurrency($showData->po_rtn_total_price),
                'approve'               => $showData->approve_status,
                'details'               => $showDetailsData,
            ];
            return response()->json(['status'=>'success','data'=>$data]);
        } catch(\Exception $e){
            return response()->json(['status'=>'error','data'=>[]]);
        }
    }

    public function edit($id)
    {
        try {
            $editModeData = PurchaseReturn::FindOrFail($id);
            $editModeDetailsData = PurchaseReturnDetails::where('po_rtnd_return_id',$id)->get();
            $data = [
                'id'                    => $editModeData->id,
                'po_return_no'          => $editModeData->po_return_no,
                'po_return_date'        => date('d/m/Y', strtotime($editModeData->po_return_date)),
                'po_rtn_supplier_id'    => $editModeData->po_rtn_supplier_id,
                'po_rtn_warehouse_id'   => $editModeData->po_rtn_warehouse_id,
                'po_rtn_issue_type_id'  => $editModeData->po_rtn_issue_type_id,
                'po_rtn_narration'      => $editModeData->po_rtn_narration,
                'po_rtn_total_qty'      => round($editModeData->po_rtn_total_qty,2),
                'po_rtn_total_price'    => round($editModeData->po_rtn_total_price,2),
                'approve'               => $editModeData->approve_status,

                'deleteID'              => [],
                'details'               => $editModeDetailsData
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
            'po_return_no'              => 'required|unique:purchase_returns,po_return_no,'.$id.',id',
            'po_rtn_supplier_id'        => 'required',
            'po_rtn_warehouse_id'       => 'required',
            'po_rtn_issue_type_id'      => 'required',

            'details.*.po_rtnd_product_id'          => 'required',
            'details.*.po_rtnd_qty'                 => 'required',
            'details.*.po_rtnd_unit_price'          => 'required',
        ], [
            'details.*.po_rtnd_product_id.required'     => 'required',
            'details.*.po_rtnd_qty.required'            => 'required',
            'details.*.po_rtnd_unit_price.required'     => 'required',
        ]);


        if($request->po_return_date != ''){
            $date = str_replace('/', '-', $request->po_return_date);
            $date_val =date('Y-m-d', strtotime($date));
        }else{
            $date_val =date('Y-m-d');
        }

        $app= $request->approve;
        $data = [
            'po_return_no'          => $request->po_return_no,
            'po_return_date'        => $date_val,
            'po_rtn_supplier_id'    => $request->po_rtn_supplier_id,
            'po_rtn_warehouse_id'   => $request->po_rtn_warehouse_id,
            'po_rtn_issue_type_id'  => $request->po_rtn_issue_type_id,
            'po_rtn_narration'      => $request->po_rtn_narration,
            'po_rtn_total_qty'      => round($request->po_rtn_total_qty,2),
            'po_rtn_total_price'    => round($request->po_rtn_total_price, 2),
            'updated_by'            => Auth::user()->id,
        ];

        try {
            DB::beginTransaction();

            $editModeData = PurchaseReturn::FindOrFail($id);

            $editModeData->po_return_no         = $request->po_return_no;
            $editModeData->po_return_date       = $date_val;
            $editModeData->po_rtn_supplier_id   = $request->po_rtn_supplier_id;
            $editModeData->po_rtn_warehouse_id  = $request->po_rtn_warehouse_id;
            $editModeData->po_rtn_issue_type_id = $request->po_rtn_issue_type_id;
            $editModeData->po_rtn_narration     = $request->po_rtn_narration;
            $editModeData->po_rtn_total_qty     = $request->po_rtn_total_qty;
            $editModeData->po_rtn_total_price   = $request->po_rtn_total_price;

            if ($app!=1) {
                $editModeData->updated_by = Auth::user()->id;
            }

            $editModeData->save();

            //// details transection ////
            if (count($request->deleteID) > 0) {
                PurchaseReturnDetails::whereIn('id', $request->deleteID)->delete();
            }

            $dataFormat = [];
            $count = count($request->details);
            for ($i = 0; $i < $count; $i++) {
                if (isset($request->details[$i]['id']) && $request->details[$i]['id'] !='') {
                    $updateData = [
                        'po_rtnd_return_id'     => $id,
                        'po_rtnd_product_id'    => $request->details[$i]['po_rtnd_product_id'],
                        'po_rtnd_remarks'       => $request->details[$i]['po_rtnd_remarks'],
                        'po_rtnd_messure_unit'  => $request->details[$i]['po_rtnd_messure_unit'],
                        'po_rtnd_unit_price'    => $request->details[$i]['po_rtnd_unit_price'],
                        'po_rtnd_qty'           => round($request->details[$i]['po_rtnd_qty'],2),
                        'po_rtnd_total_price'   => round($request->details[$i]['po_rtnd_total_price'],2)
                    ];
                    PurchaseReturnDetails::where('id', $request->details[$i]['id'])->update($updateData);
                } else {
                    $dataFormat[$i] =[
                        'po_rtnd_return_id'     => $id,
                        'po_rtnd_product_id'    => $request->details[$i]['po_rtnd_product_id'],
                        'po_rtnd_remarks'       => $request->details[$i]['po_rtnd_remarks'],
                        'po_rtnd_messure_unit'  => $request->details[$i]['po_rtnd_messure_unit'],
                        'po_rtnd_unit_price'    => $request->details[$i]['po_rtnd_unit_price'],
                        'po_rtnd_qty'           => round($request->details[$i]['po_rtnd_qty'],2),
                        'po_rtnd_total_price'   => round($request->details[$i]['po_rtnd_total_price'],2),
                    ];
                }
            }

            if(count($dataFormat) > 0){
                PurchaseReturnDetails::insert($dataFormat);
            }

            if($app ==1){
                $this->approve($id);
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Purchase Return successfully updated!']);
        } catch (\Exception $e) {
            //print($e->getMessage());
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    public function destroy($id)
    {
        try{

            PurchaseReturnDetails::where('po_rtnd_return_id',$id)->delete();
            PurchaseReturn ::FindOrFail($id)->delete();
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Purchase Return successfully Deleted !']);
        } elseif ($bug == 1451) {
            return response()->json(['status' => 'error', 'message' => 'This data is used another table,can not delete data']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    public function approve($id)
    {
        $appData = PurchaseReturn::where('id', $id)->first();
        if ($appData->approve_status == 0) {

            $appDetailsData = PurchaseReturnDetails::where('po_rtnd_return_id', $id)->get();

            $date           = $appData->po_return_date;
            $log_type       = StockTransactionType::$purchase_return;
            $serial         = $appData->po_return_no;

            $ref_tbl        = '';
            $ref_tbl_id     = '';
            $log_tbl        = 'purchase_returns';

            $sup_cus_tabl   = 'purchase_supplier_entries';
            $supplier       = $appData->po_rtn_supplier_id;
            $warehouseId    = $appData->po_rtn_warehouse_id;
            $total_qty      = $appData->po_rtn_total_qty;
            $total_price    = $appData->po_rtn_total_price;


            /****************************************** INCREASE STOCK INFORMATION  **********************************************************/
            foreach ($appDetailsData as $val) {

                $checkExists = InventoryCurrentStock::where('inventory_current_stocks_product_id', $val->po_rtnd_product_id)
                    ->where('inventory_current_stocks_warehouse_id', $warehouseId)
                    ->first();
                if ($checkExists) {
                    InventoryCurrentStock::where('inventory_current_stocks_product_id', $val->po_rtnd_product_id)
                        ->where('inventory_current_stocks_warehouse_id', $warehouseId)
                        ->decrement('inventory_stocks_current_qty', $val->po_rtnd_qty);

                    $Ics=InventoryCurrentStock::where('inventory_current_stocks_product_id', $val->po_rtnd_product_id)
                        ->where('inventory_current_stocks_warehouse_id', $warehouseId)->first();

                    if($Ics->total_price != 0){
                        $unit_price= $Ics->total_price / $Ics->inventory_stocks_current_qty;
                        $total_inv_price= $Ics->total_price;
                    }else{
                        $unit_price = $val->po_rtnd_unit_price;
                        $total_inv_price= $unit_price * $Ics->inventory_stocks_current_qty;

                    }

                    InventoryCurrentStock::where('inventory_current_stocks_product_id', $val->po_rtnd_product_id)
                        ->where('inventory_current_stocks_warehouse_id', $warehouseId)
                        ->update(array(
                            'unit_price'    => round($unit_price, 2),
                            'total_price'   => round($total_inv_price, 2),
                        ));
                }
            }

            /**************************************** STOCK LOG INFO START ********************************************************/
            $stock_log = [
                'stock_transection_logs_date'           => $date,
                'stock_transection_logs_type'           => $log_type,
                'stock_transection_logs_number'         => $serial,
                'stock_transection_logs_ref_table_name' => $ref_tbl,
                'stock_transection_logs_ref_table_id'   => $ref_tbl_id,
                'stock_transection_logs_table_name'     => $log_tbl,
                'stock_transection_logs_table_id'       => $id,
                'stock_trans_supp_cus_table_name'       => $sup_cus_tabl,
                'stock_trans_supp_cus_table_id'         => $supplier,
                'stock_trans_warehouse_id'              => $warehouseId,
                'stock_trans_qty'                       => $total_qty,
                'stock_trans_total_price'               => $total_price,
                'created_by'                            => Auth::user()->id,
            ];
            $mainStock = StockTransectionLog::create($stock_log);

            $Stockdetails = [];
            $details_tbl = 'purchase_return_details';

            foreach($appDetailsData as $key => $row){
                $w_info = InventoryCurrentStock::where('inventory_current_stocks_warehouse_id', $warehouseId)
                    ->where('inventory_current_stocks_product_id', $row->po_rtnd_product_id)->first();

                $Stockdetails[] = [
                    'log_id'            => $mainStock->id,
                    'log_table_name'    => $details_tbl,
                    'log_table_id'      => $row->id,
                    'product_id'        => $row->po_rtnd_product_id,
                    'warehouse_id'      => $warehouseId,
                    'log_entry_qty'     => $row->po_rtnd_qty,
                    'log_open_qty'      => $w_info ? $w_info->inventory_stocks_open_qty : 0,
                    'log_current_qty'   => $w_info ? $w_info->inventory_stocks_current_qty : 0,
                    'log_close_qty'     => '',

                    'log_unit_price'    => $row->po_rtnd_unit_price,
                    'log_total_price'   => $row->po_rtnd_total_price,

                    'add_sub'           => 0,

                    'entry_date'        => $date,
                    'created_by'        => Auth::user()->id,
                ];
            }
            StockTransectionLogDetails::insert($Stockdetails);


            /****************************************** ACCOUNT INFORMATION  **********************************************************/
            $data_ac = [
                'vouchers_no'           => "PO-Rtn-$id",
                'purchase_return_id'    => $id,
                'purchase_return_date'  => $date,
                'supplier_id'           => $supplier,
                'payment_type'          => '1',
                'total_qty'             => $appData->po_rtn_total_qty,
                'total_price'           => $appData->po_rtn_total_price,
                'created_by'            => Auth::user()->id,
            ];

            $result_ac = AccountsPurchaseReturnVoucher::create($data_ac);


            /****************************************** ACCOUNT DETAILS INFORMATION  **************************************************/
            $dataFormatAc = [];
            $count = count($appDetailsData);
            for ($i = 0; $i < $count; $i++) {
                $dataFormatAc[$i] = [
                    'ac_pruchase_return_voucher_id' => $result_ac->id,
                    'product_id'                    => $appDetailsData[$i]['po_rtnd_product_id'],
                    'remarks'                       => $appDetailsData[$i]['po_rtnd_remarks'],
                    'm_unit'                        => $appDetailsData[$i]['po_rtnd_messure_unit'],
                    'unit_price'                    => $appDetailsData[$i]['po_rtnd_unit_price'],
                    'qty'                           => $appDetailsData[$i]['po_rtnd_qty'],
                    'total_price'                   => $appDetailsData[$i]['po_rtnd_total_price'],
                ];
            }
            AccountsPurchaseReturnVoucherDetails::insert($dataFormatAc);


            /****************************************** APPROVE INFORMATION  **********************************************************/
            DB::table('purchase_returns')->where('id', $id)->update(array(
                'approve_status'    => 1,
                'approve_by'        => Auth::user()->id,
                'approve_at'        => Carbon::now(),
            ));
        }
    }

    public function PoRtnNoGenerate(){
        $id = PurchaseReturn::withTrashed()->count();
        $currentId = $id+1;
        return 'PRTN'.date('Y').date('m').$currentId;
    }



    private function dataFormat($request, $id)
    {
        $dataFormat = [];
        $count = count($request->details);
        for ($i = 0; $i < $count; $i++) {
            $dataFormat[$i] = [
                'po_rtnd_return_id'     => $id,
                'po_rtnd_product_id'    => $request->details[$i]['po_rtnd_product_id'],
                'po_rtnd_remarks'       => $request->details[$i]['po_rtnd_remarks'],
                'po_rtnd_messure_unit'  => $request->details[$i]['po_rtnd_messure_unit'],
                'po_rtnd_unit_price'    => $request->details[$i]['po_rtnd_unit_price'],
                'po_rtnd_qty'           => round($request->details[$i]['po_rtnd_qty'],2),
                'po_rtnd_total_price'   => round($request->details[$i]['po_rtnd_total_price'],2)
            ];
        }
        return $dataFormat;
    }

}
