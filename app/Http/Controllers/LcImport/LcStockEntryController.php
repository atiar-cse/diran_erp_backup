<?php

namespace App\Http\Controllers\LcImport;

use App\Model\Inventory\StockTransectionLogDetails;
use App\Model\LC\LcStockEntry;
use App\Model\LC\LcStockEntryDetails;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use App\Model\Production\ProductionProducts;
use App\Model\Production\ProductionMeasureUnit;
use App\Model\Purchase\PurchaseWareHouse;
use App\Model\LC\LcCommercialInvoiceEntry;
use DB;

use App\Model\Inventory\InventoryCurrentStock;
use App\Model\Inventory\StockTransectionLog;

use App\Model\Accounts\AccountsChartofAccounts;
use App\Model\Accounts\AccountsJournalEntry;
use App\Model\Accounts\AccountsJournalEntryDetails;
use App\Model\Accounts\AccountsMainCode;

use App\Lib\Enumerations\AccountsTransactionType;
use App\Lib\Enumerations\StockTransactionType;

class LcStockEntryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = DB::table('lc_stock_entries')
                ->leftJoin('lc_opening_sections', 'lc_stock_entries.lc_opening_info_id', '=', 'lc_opening_sections.id')
                ->leftJoin('lc_commercial_invoice_entries', 'lc_stock_entries.lc_commercial_invoice_id', '=', 'lc_commercial_invoice_entries.id')
                ->leftJoin('purchase_ware_houses', 'lc_stock_entries.warehouse_id', '=', 'purchase_ware_houses.id')
                ->leftJoin('purchase_supplier_entries', 'lc_stock_entries.supplier_id', '=', 'purchase_supplier_entries.id')
                ->leftJoin('users as ura', 'lc_stock_entries.created_by','=','ura.id')
                ->leftJoin('users as ured', 'lc_stock_entries.updated_by','=','ured.id')
                ->leftJoin('users as urea', 'lc_stock_entries.approve_by','=','urea.id')
                ->whereNull('lc_stock_entries.deleted_at')
                ->select(['lc_stock_entries.id AS id',
                    'lc_stock_entries.entry_date',
                    'lc_stock_entries.total_qty',
                    'lc_stock_entries.total_net_weight',
                    'lc_stock_entries.total_amount',
                    'lc_stock_entries.created_by',
                    'lc_stock_entries.updated_by',
                    'lc_stock_entries.approve_by',
                    'lc_stock_entries.approve_status',
                    'lc_opening_sections.lc_no',
                    'lc_commercial_invoice_entries.ci_no',
                    'purchase_ware_houses.purchase_ware_houses_name',
                    'purchase_supplier_entries.purchase_supplier_name',
                    'ura.user_name as ad_name',
                    'ured.user_name as ed_name',
                    'urea.user_name as ap_name'
                ]);

            return datatables()->of($query)->toJson();
        }

        $ciLists = LcCommercialInvoiceEntry::with('get_lc_no')->select('id','ci_no','lc_opening_info_id')
                                    ->where('status',1)
                                    ->where('approve_status',1)
                                    ->where('ci_landed_status',1)
                                    ->where('stock_enrty_status',0)
                                    ->get();
        $productLists = ProductionProducts::where('status',1)->selectRaw('id,product_name,product_code,measure_unit_id')->get();
        $measureUnitList= ProductionMeasureUnit::where('status', '1')->get();
        $warehouseLists   = PurchaseWareHouse::where('status', '1')->get();

        return view('lc.lc_section.lc_stock_entry',compact(
            'ciLists','productLists','measureUnitList','warehouseLists'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'lc_commercial_invoice_id' => 'required',
            'warehouse_id' => 'required',
            'entry_date' => 'required',
            'details.*.product_id' => 'required',
            'details.*.unit_price' => 'required|numeric',
            'details.*.qty' => 'required|numeric',
        ], [
            'lc_commercial_invoice_id.required' => 'The CI No field is required.',
            'details.*.product_id.required' => 'Required field.',
            'details.*.unit_price.required' => 'Required field.',
            'details.*.qty.required' => 'Required field.',
        ]);

        $input = $request->except('details');
        $input['entry_date'] = dateConvertFormtoDB($request->entry_date);
        $input['created_by'] = Auth::user()->id;

        $approval = $request->approve;

        try {
            DB::beginTransaction();

            $lcCI = LcCommercialInvoiceEntry::where('approve_status',1)
                                ->where('stock_enrty_status',0)
                                ->FindOrFail($request->lc_commercial_invoice_id);
                                    
            $result = LcStockEntry::create($input);
            $details = $this->dataFormat($request, $result->id);
            LcStockEntryDetails::insert($details);

            if($approval ==1){
                $this->approve($result->id);

                if($request->updated_by == Null){
                    DB::table('lc_stock_entries')->where('id', $result->id)->update(array('updated_by' => Auth::user()->id));
                }

                $lcCI->stock_enrty_status = 1; //Reset to 0, if the stock entry deleted
                $lcCI->save();
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'LC Stock Successfully saved!']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    public function show($id)
    {
        try {
            $editModeData = LcStockEntry::with('get_lc_no','get_ci_no','get_warehouse','get_supplier')->FindOrFail($id);
            $editModeDetailsData = LcStockEntryDetails::with('get_product','get_measure_unit')->where('lc_stock_entry_id',$id)->get();

            $data = [
                'entry_date' => dateConvertDBtoForm($editModeData->entry_date),
                'narration' => $editModeData->narration,
                'total_qty' => $editModeData->total_qty,
                'total_net_weight' => $editModeData->total_net_weight,
                'total_gross_weight' => $editModeData->total_gross_weight,
                'total_amount' => $editModeData->total_amount,
                'approve_status' => $editModeData->approve_status,
                'details'    => $editModeDetailsData,

                'get_lc_no'    => $editModeData->get_lc_no,
                'get_ci_no'    => $editModeData->get_ci_no,
                'get_warehouse'    => $editModeData->get_warehouse,
                'get_supplier'    => $editModeData->get_supplier,
            ];
            return response()->json(['status'=>'success','data'=>$data]);
        } catch(\Exception $e){
            return response()->json(['status'=>'error','data'=>[]]);
        }
    }

    public function edit($id)
    {
        try {
            $editModeData = LcStockEntry::FindOrFail($id);
            $editModeDetailsData = LcStockEntryDetails::where('lc_stock_entry_id',$id)->get();

            $data = [
                'id'    => $editModeData->id,
                'lc_commercial_invoice_id' => $editModeData->lc_commercial_invoice_id,
                'supplier_id' => $editModeData->supplier_id,
                'warehouse_id' => $editModeData->warehouse_id,
                'entry_date' => dateConvertDBtoForm($editModeData->entry_date),
                'narration' => $editModeData->narration,
                'total_qty' => $editModeData->total_qty,
                'total_net_weight' => $editModeData->total_net_weight,
                'total_gross_weight' => $editModeData->total_gross_weight,
                'total_amount' => $editModeData->total_amount,
                'status' => $editModeData->status,

                'deleteID' => [],
                'details'    => $editModeDetailsData,
            ];
            return response()->json(['status'=>'success','data'=>$data]);
        } catch(\Exception $e){
            return response()->json(['status'=>'error','data'=>[]]);
        }
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'lc_commercial_invoice_id' => 'required',
            'entry_date' => 'required',
            'details.*.product_id' => 'required',
            'details.*.unit_price' => 'required|numeric',
            'details.*.qty' => 'required|numeric',
        ], [
            'lc_commercial_invoice_id.required' => 'The CI No field is required.',
            'details.*.product_id.required' => 'Required field.',
            'details.*.unit_price.required' => 'Required field.',
            'details.*.qty.required' => 'Required field.',
        ]);

        $approval = $request->approve;

        try {
            DB::beginTransaction();

            $input = $request->except('details');
            $input['entry_date'] = dateConvertFormtoDB($request->entry_date);
            if($approval !=1){
                $input['updated_by'] = Auth::user()->id;
            }

            $editModeData = LcStockEntry::FindOrFail($id);
            $editModeData->update($input);

            /* Insert update and delete proforma-invoice details  */
            if (count($request->deleteID) > 0) {
                LcStockEntryDetails::whereIn('id', $request->deleteID)->delete();
            }
            $dataFormat = [];
            $count = count($request->details);
            for ($i = 0; $i < $count; $i++) {
                if (isset($request->details[$i]['id']) && $request->details[$i]['id'] !='') {
                    $updateData = [
                        'lc_stock_entry_id' => $id,
                        'product_id' => $request->details[$i]['product_id'],
                        'hs_code' => $request->details[$i]['hs_code'],
                        'unit_price' => $request->details[$i]['unit_price'],
                        'qty' => $request->details[$i]['qty'],
                        'measure_unit_id' => $request->details[$i]['measure_unit_id'],
                        'gross_weight' => $request->details[$i]['gross_weight'],
                        'net_weight' => $request->details[$i]['net_weight'],
                        'total_amount' => $request->details[$i]['total_amount'],
                    ];
                    LcStockEntryDetails::where('id', $request->details[$i]['id'])->update($updateData);
                } else {
                    $dataFormat[$i] =[
                        'lc_stock_entry_id' => $id,
                        'product_id' => $request->details[$i]['product_id'],
                        'hs_code' => $request->details[$i]['hs_code'],
                        'unit_price' => $request->details[$i]['unit_price'],
                        'qty' => $request->details[$i]['qty'],
                        'measure_unit_id' => $request->details[$i]['measure_unit_id'],
                        'gross_weight' => $request->details[$i]['gross_weight'],
                        'net_weight' => $request->details[$i]['net_weight'],
                        'total_amount' => $request->details[$i]['total_amount'],
                    ];
                }
            }

            if(count($dataFormat) > 0){
                LcStockEntryDetails::insert($dataFormat);
            }

            if($approval ==1){
                $this->approve($id);

                $lcCI = LcCommercialInvoiceEntry::where('approve_status',1)
                    ->where('stock_enrty_status',0)
                    ->FindOrFail($request->lc_commercial_invoice_id);
                $lcCI->stock_enrty_status = 1; //Reset to 0, if the stock entry deleted
                $lcCI->save();
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'LC Stock Entry successfully updated!']);
        } catch (\Exception $e) {
            DB::rollback();

            $mm = $e->getMessage();
            return response()->json(['status' => 'error', 'message' => $mm]);

        }
    }

    public function destroy($id)
    {
        try{
            LcStockEntryDetails::where('lc_stock_entry_id',$id)->delete();
            $lcStockEntry = LcStockEntry::FindOrFail($id);
            $lcStockEntry->delete();
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'LC Stock Entry successfully Deleted !']);
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
                'lc_stock_entry_id' => $id,
                'product_id' => $request->details[$i]['product_id'],
                'hs_code' => $request->details[$i]['hs_code'],
                'unit_price' => $request->details[$i]['unit_price'],
                'qty' => $request->details[$i]['qty'],
                'measure_unit_id' => $request->details[$i]['measure_unit_id'],
                'gross_weight' => $request->details[$i]['gross_weight'],
                'net_weight' => $request->details[$i]['net_weight'],
                'total_amount' => $request->details[$i]['total_amount'],
            ];
        }

        return $dataFormat;
    }

    public function approve($id)
    {
        $lcStockRow = LcStockEntry::with('get_ci_no')->FindOrFail($id);
        if ($lcStockRow->approve_status == 0) {

            $appDetailsData = LcStockEntryDetails::where('lc_stock_entry_id', $id)->get();

            $date = $lcStockRow->entry_date ? $lcStockRow->entry_date : date('Y-m-d') ;

            $log_type = StockTransactionType::$lc_stock_entry;
            $serial = $lcStockRow->get_ci_no->ci_no; //e.g. $lcStockRow->entry_no lc commercial no

            $ref_tbl = 'lc_commercial_invoice_entries';
            $ref_tbl_id = $lcStockRow->lc_commercial_invoice_id;
            $log_tbl = 'lc_stock_entries';

            $sup_cus_tabl = 'purchase_supplier_entries';
            $supplier = $lcStockRow->supplier_id;
            $warehouseId = $lcStockRow->warehouse_id;

            $total_qty      = $lcStockRow->total_qty;
            $total_price    =  $lcStockRow->total_amount;



            /****************************************** INCREASE STOCK INFORMATION  **********************************************************/
            foreach ($appDetailsData as $val) {
                $checkExists = InventoryCurrentStock::where('inventory_current_stocks_product_id', $val->product_id)
                    ->where('inventory_current_stocks_warehouse_id', $warehouseId)
                    ->first();

                if ($checkExists) {

                    InventoryCurrentStock::where('inventory_current_stocks_product_id', $val->product_id)
                        ->where('inventory_current_stocks_warehouse_id', $warehouseId)
                        ->increment('inventory_stocks_current_qty', $val->qty);

                    InventoryCurrentStock::where('inventory_current_stocks_product_id',$val->product_id)
                        ->where('inventory_current_stocks_warehouse_id',$warehouseId)
                        ->increment('total_price', $val->total_amount);

                    ProductionProducts::where('id', $val->product_id)
                        ->update(array(
                            'buying_price'=>$val->unit_price,
                        ));
                } else {
                    $stock_entry = [
                        'inventory_current_stocks_product_id'   => $val->product_id,
                        'inventory_current_stocks_warehouse_id' => $warehouseId,
                        'inventory_stocks_open_qty'             => $val->qty,
                        'inventory_stocks_current_qty'          => $val->qty,

                        'total_price'                           => $val->total_amount,
                        'created_by'                            => Auth::user()->id,
                    ];
                    InventoryCurrentStock::create($stock_entry);

                    ProductionProducts::where('id', $val->product_id)
                        ->update(array(
                            'buying_price'=>$val->unit_price,
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
            $details_tbl = 'lc_stock_entry_details';

            /****************************************** INCREASE STOCK INFORMATION  **********************************************************/
            foreach ($appDetailsData as $row) {

                $w_info = InventoryCurrentStock::where('inventory_current_stocks_warehouse_id', $warehouseId)
                    ->where('inventory_current_stocks_product_id', $row->product_id)->first();

                $Stockdetails[] = [
                    'log_id'            => $mainStock->id,
                    'log_table_name'    => $details_tbl,
                    'log_table_id'      => $row->id,

                    'product_id'        => $row->product_id,
                    'warehouse_id'      => $warehouseId,

                    'log_entry_qty'     => $row->qty,
                    'log_open_qty'      => $w_info->inventory_stocks_open_qty,
                    'log_current_qty'   => $w_info->inventory_stocks_current_qty,
                    'log_close_qty'     => '',

                    'log_unit_price'    => $row->unit_price,
                    'log_total_price'   => $row->total_amount,

                    'add_sub'           => 1,

                    'entry_date'        => $date,
                    'created_by'        => Auth::user()->id,
                ];
                }
            StockTransectionLogDetails::insert($Stockdetails);


            /****************************************** APPROVE INFORMATION  **********************************************************/
            $lcStockRow->approve_status = 1;
            $lcStockRow->approve_by = Auth::user()->id;
            $lcStockRow->approve_at = Carbon::now();
            $lcStockRow->save();
        }
    }

}
