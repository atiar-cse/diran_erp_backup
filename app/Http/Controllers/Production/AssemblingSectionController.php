<?php

namespace App\Http\Controllers\Production;

use App\Model\CompanyInfo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use App\Model\Production\ProductionProducts;

use App\Model\Production\ProductionAssemblingSetup;
use App\Model\Production\ProductionAssemblingSetupDetails;

use App\Model\Production\ProductionAssembling;
use App\Model\Production\ProductionAssemblingDetails;
use App\Model\Production\ProductionInventoryMaterial;

use App\Model\Production\ProductionCurrentStock;
use App\Model\Inventory\InventoryCurrentStock;

use App\Model\Inventory\StockTransectionLog;
use App\Model\Inventory\StockTransectionLogDetails;
use App\Model\Accounts\AccountsChartofAccounts;
use App\Model\Accounts\AccountsJournalEntry;
use App\Model\Accounts\AccountsJournalEntryDetails;
use App\Model\Accounts\AccountsMainCode;
use App\Model\Accounts\AccountsCostCenter;

use App\Model\Purchase\PurchaseWareHouse;
use DB;


use App\Lib\Enumerations\AccountsTransactionType;
use App\Lib\Enumerations\StockTransactionType;

use App\Repositories\CommonRepositories;

use App\Model\Production\ProductionTestingDetails;

class AssemblingSectionController extends Controller
{
    protected $commonRepositories;
    public function __construct(CommonRepositories $commonRepositories)
    {
        $this->commonRepositories = $commonRepositories;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {

            $query = DB::table('production_assemblings')
                ->leftJoin('production_products', 'production_assemblings.finishing_product_id', '=', 'production_products.id')
                ->leftJoin('users as ura', 'production_assemblings.created_by','=','ura.id')
                ->leftJoin('users as ured', 'production_assemblings.updated_by','=','ured.id')
                ->leftJoin('users as urea', 'production_assemblings.approve_by','=','urea.id')
                ->whereNull('production_assemblings.deleted_at')
                ->select(['production_assemblings.id AS id',
                    'production_assemblings.assembling_no',
                    'production_assemblings.assembling_date',
                    'production_assemblings.narration',
                    'production_assemblings.trans_to_packing_qty',
                    'production_assemblings.created_by',
                    'production_assemblings.updated_by',
                    'production_assemblings.approve_by',
                    'production_assemblings.approve_status',
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

        $finishProductList   = ProductionProducts::where('status', '1')->where('is_raw_material_status', '0')->get();

        $productList  = ProductionProducts::select('production_products.id','production_products.product_name','production_current_stocks.assembling_current_qty','production_current_stocks.assembling_receive_qty')
                        ->leftJoin('production_current_stocks', 'production_current_stocks.product_id', '=', 'production_products.id')
                        ->where('production_products.status', '1')
                        ->get();

        $warehouse   = PurchaseWareHouse::where('status', '1')->get();
        $costCenterLists  = AccountsCostCenter::where('status', '1')->get();
        return view('production.production_section.assembling_section',compact('finishProductList','productList','warehouse','costCenterLists'));
    }

    public function assemblingNoGenerate(){
        $id = ProductionAssembling::withTrashed()->count();
        $currentId = $id+1;
        return 'ASM-'.date('Ym').$currentId;
    }

    public function approve($id)
    {
        $appData = ProductionAssembling::where('id', $id)->first();
        if ($appData->approve_status == 0) {

            $appDetailsData = ProductionAssemblingDetails::where('assembling_section_id', $id)->get();
            $this->incrementDecrementAssmblingQTY($id,$appDetailsData);

            $inv_details = ProductionInventoryMaterial::where('assembling_section_id', $id)->get();
            $this->decrementInventoryQTY($appData, $inv_details);

            /**************************************** Approved ********************************************************/
            $appData->update(array(
                'approve_status'    => 1,
                'approve_by'        => Auth::user()->id,
                'approve_at'        => Carbon::now(),
            ));
        }
    }

    private function incrementDecrementAssmblingQTY($AssembleId,$appDetailsData){

        $appData = ProductionAssembling::where('id', $AssembleId)->first();

        $finishing_product_id   = $appData->finishing_product_id;
        $trans_to_packing_qty   = $appData->trans_to_packing_qty;

        $checkExists = ProductionCurrentStock::where('product_id',$finishing_product_id)->first();
        if($checkExists){

            ProductionCurrentStock::where('product_id', $finishing_product_id)
                ->increment('packing_receive_qty', $trans_to_packing_qty);
        }else{
            $testingQty= [
                'product_id' => $finishing_product_id,
                'packing_receive_qty' => $trans_to_packing_qty,
            ];
            ProductionCurrentStock::insert($testingQty);
        }


        foreach($appDetailsData as $val) {

            /* @ Details - Production Section */
            $checkProductionExists = ProductionCurrentStock::where('product_id',$val['product_id'])->first();
                if($checkProductionExists){

                    ProductionCurrentStock::where('product_id', $val['product_id'])
                        ->update(
                            [ 'assembling_current_qty' => $val['remain_qty']]
                        );
                    ProductionCurrentStock::where('product_id', $val['product_id'])
                        ->decrement('assembling_receive_qty', $val['trans_to_packing_qty']);
                    // ProductionCurrentStock::where('product_id', $val['product_id'])
                        // ->increment('assembling_receive_qty', $val['trans_to_store_qty']); //trans to Assembling

                }else{
                    $testingQty= [
                        'product_id' => $val['product_id'],
                        'assembling_current_qty' => $val['remain_qty'],
                        'assembling_receive_qty' => $val['receive_qty'],
                        // 'assembling_receive_qty' => $val['trans_to_store_qty'], //trans to Assembling
                    ];
                    ProductionCurrentStock::insert($testingQty);
                }

        }


    }

    private function decrementInventoryQTY($appData, $inv_details){
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
                      'stock_transection_logs_date' => $appData->assembling_date,
                      'is_in_out' => 2,
                      'stock_transection_logs_type' => StockTransactionType::$prodution_assemble,
                      'stock_transection_logs_number' => $appData->assembling_no,
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
                      'entry_date'        => $appData->assembling_date,
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

    private function journalDataEntry($request, $id){

        $approval = $request->approve;

        $jr_status = 0;
        if($approval ==1){
            $jr_status = 1;
        }
        $JournalData = [
            'transaction_reference_id' => $id, //production_requisition_for_rms id
            'transaction_reference_no' => "ReqforRm-$id",
            'transaction_date' => dateConvertFormtoDB($request->assembling_date),
            'transaction_type' => AccountsTransactionType::$ProductionAssemble,
            'transaction_type_name' => AccountsTransactionType::$ProductionAssembleitle,
            'cost_center_id' => $request->cost_center_id,
            'branch_id' => '',
            'narration' => $request->narration,
            'total_debit' => $request->total_rm_price,
            'total_credit' => $request->total_rm_price,
            'approve_status' => $jr_status,
            'created_by' => Auth::user()->id,
        ];
        $result = AccountsJournalEntry::create($JournalData);
        $debitData =[
            'journal_entry_id' => $result->id,
            'char_of_account_id' => $request->debit_account_id,
            'debit_amount' => $request->total_rm_price,
        ];

        AccountsJournalEntryDetails::insert($debitData);

        $creditData =[
            'journal_entry_id' => $result->id,
            'char_of_account_id' => $request->credit_account_id,
            'credit_amount' => $request->total_rm_price,
        ];

        AccountsJournalEntryDetails::insert($creditData);
    }

    public function store(Request $request)
    {
        $inv_no_like = 'ASM-';
        $rtn_val =InvStore($request->assembling_no, $inv_no_like,
            'production_assemblings','assembling_no');

        $request->merge(['assembling_no' => $rtn_val]) ;


        $this->validate($request, [
            'assembling_no' => 'required',
            'assembling_date' => 'required',
            // 'warehouse_id' => 'required',
            'assembling_types' => 'required',
            'unit_price' => 'required',
            'total_price' => 'required',
            // 'total_rm_qty' => 'required',
            // 'total_rm_price' => 'required',

            /*'details.*.product_id' => 'required',
            'details.*.current_qty' => 'required',
            'details.*.receive_qty' => 'required',
            'details.*.trans_to_packing_qty' => 'required',
            'details.*.unit_price' => 'required',
            'details.*.total_price' => 'required',*/
        ]/*, [
            'details.*.product_id.required' => 'Required field.',
            'details.*.current_qty.required' => 'Required field.',
            'details.*.receive_qty.required' => 'Required field.',
            'details.*.trans_to_packing_qty.required' => 'Required field.',
            'details.*.unit_price.required' => 'Required field.',
            'details.*.total_price.required' => 'Required field.',
        ]*/);

        $overhead_info = [
            'last_month_overhead_amt'  => $request->last_month_overhead_amt,
            'last_month_production_kg' => $request->last_month_production_kg,
            'overhead_per_kg'          => $request->overhead_per_kg,
        ];

        $data = [
            'assembling_no'        => $request->assembling_no,
            'assembling_date'      => dateConvertFormtoDB($request->assembling_date),
            'assembling_types'     => $request->assembling_types,
            'warehouse_id'         => $request->warehouse_id,
            'finishing_product_id' => $request->finishing_product_id,
            'trans_to_packing_qty' => $request->trans_to_packing_qty,
            'unit_price'           => $request->unit_price,
            'total_price'          => $request->total_price,
            'narration'            => $request->narration,
            'total_rm_qty'         => $request->total_rm_qty,
            'total_rm_price'       => $request->total_rm_price,
            'inv_total_qty'        => $request->inv_total_qty,
            'inv_total_amount'     => $request->inv_total_amount,
            'overhead_info'        => json_encode($overhead_info),
            'created_by'           => Auth::user()->id,
        ];

        try {
            DB::beginTransaction();

            $app = $request->approve_status;
            $result = ProductionAssembling::create($data);

            $details = $this->dataFormat($request, $result->id);
            ProductionAssemblingDetails::insert($details);

            $details = $this->dataFormatInventoryMaterials($request, $result->id);
            ProductionInventoryMaterial::insert($details);

            if($app == 1){
                $this->approve($result->id);

                if($request->updated_by == Null){
                    DB::table('production_assemblings')->where('id', $result->id)->update(array('updated_by' => Auth::user()->id));
                }
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => "Assembling # " . $request->assembling_no .' Successfully Saved!']);
        } catch (\Exception $e) {

            DB::rollback();
            $msg = $e->getMessage();
            return response()->json(['status' => 'error', 'message' => $msg]);
        }
    }

    public function show($id)
    {
        try {
            $editModeData = ProductionAssembling::with('finish_product', 'warehouse')->FindOrFail($id);
            $editModeDetailsData = ProductionAssemblingDetails::with('product')->where('assembling_section_id',$id)->get();

            $inv_details = ProductionInventoryMaterial::with('product')->where('assembling_section_id',$id)->get();

            $data = [
                'id'    => $editModeData->id,
                'assembling_no' => $editModeData->assembling_no,
                'assembling_date' => dateConvertDBtoForm($editModeData->assembling_date),
                'warehouse_id' => '',//$editModeData->warehouse->purchase_ware_houses_name,
                'assembling_types' => $editModeData->assembling_types,
                'finishing_product_id' => $editModeData->finish_product->product_name,
                'trans_to_packing_qty' => $editModeData->trans_to_packing_qty,
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
            $editModeData = ProductionAssembling::FindOrFail($id);
            $details = ProductionAssemblingDetails::where('assembling_section_id',$id)->get();

            $inv_details = ProductionInventoryMaterial::where('assembling_section_id',$id)->get();

            $data = [
                'id'    => $editModeData->id,
                'assembling_no' => $editModeData->assembling_no,
                'assembling_date' => dateConvertDBtoForm($editModeData->assembling_date),
                'warehouse_id' => $editModeData->warehouse_id,
                'assembling_types' => $editModeData->assembling_types,
                'finishing_product_id' => $editModeData->finishing_product_id,
                'trans_to_packing_qty' => $editModeData->trans_to_packing_qty,
                'unit_price' => $editModeData->unit_price,
                'total_price' => $editModeData->total_price,
                'narration' => $editModeData->narration,
                'total_rm_qty' => $editModeData->total_rm_qty,
                'total_rm_price' => $editModeData->total_rm_price,

                'inv_total_qty' => $editModeData->inv_total_qty,
                'inv_total_amount' => $editModeData->inv_total_amount,
                'approve_status' => '',
                'deleteID' => [],
                'details'    => $details,
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
            'assembling_no' => 'required',
            'assembling_date' => 'required',
            'warehouse_id' => 'required',
            'assembling_types' => 'required',
            'total_rm_qty' => 'required',
            'total_rm_price' => 'required',

            /*'details.*.product_id' => 'required',
            'details.*.current_qty' => 'required',
            'details.*.receive_qty' => 'required',
            'details.*.trans_to_packing_qty' => 'required',
            'details.*.unit_price' => 'required',
            'details.*.total_price' => 'required',*/
        ]/*, [
            'details.*.product_id.required' => 'Required field.',
            'details.*.current_qty.required' => 'Required field.',
            'details.*.receive_qty.required' => 'Required field.',
            'details.*.trans_to_packing_qty.required' => 'Required field.',
            'details.*.unit_price.required' => 'Required field.',
            'details.*.total_price.required' => 'Required field.',
        ]*/);

        $overhead_info = [
            'last_month_overhead_amt'  => $request->last_month_overhead_amt,
            'last_month_production_kg' => $request->last_month_production_kg,
            'overhead_per_kg'          => $request->overhead_per_kg,
        ];

        try {
            DB::beginTransaction();

            $app = $request->approve_status;

            $editModeData = ProductionAssembling::FindOrFail($id);

            $editModeData->assembling_no        = $request->assembling_no;
            $editModeData->assembling_date      = dateConvertFormtoDB($request->assembling_date);
            $editModeData->narration            = $request->narration;
            $editModeData->assembling_types     = $request->assembling_types;
            $editModeData->warehouse_id         = $request->warehouse_id;
            $editModeData->finishing_product_id = $request->finishing_product_id;
            $editModeData->trans_to_packing_qty = $request->trans_to_packing_qty;
            $editModeData->unit_price           = $request->unit_price;
            $editModeData->total_price          = $request->total_price;
            $editModeData->total_rm_qty         = $request->total_rm_qty;
            $editModeData->total_rm_price       = $request->total_rm_price;

            $editModeData->inv_total_qty        = $request->inv_total_qty;
            $editModeData->inv_total_amount     = $request->inv_total_amount;
            $editModeData->overhead_info        = json_encode($overhead_info);


            if ($app!=1) {
                $editModeData->updated_by = Auth::user()->id;
            }

            $editModeData->save();

            /* Insert after Delete  */
            ProductionAssemblingDetails::where('assembling_section_id', $id)->delete();
            ProductionInventoryMaterial::where('assembling_section_id', $id)->delete();

            $details = $this->dataFormat($request, $id);
            ProductionAssemblingDetails::insert($details);

            $inv_details = $this->dataFormatInventoryMaterials($request, $id);
            ProductionInventoryMaterial::insert($inv_details);

            if($app == 1){
                $this->approve($id);
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Data successfully updated!']);
        } catch (\Exception $e) {
            DB::rollback();
            $msg = $e->getMessage();
            return response()->json(['status' => 'error', 'message' => $msg]);
        }
    }

    public function destroy($id)
    {
        try{

            ProductionAssemblingDetails::where('assembling_section_id',$id)->delete();
            ProductionInventoryMaterial::where('assembling_section_id',$id)->delete();

            ProductionAssembling::FindOrFail($id)->delete();

            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Assembling successfully Deleted !']);
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
                'assembling_section_id'   => $id,
                'product_id'         => $request->details[$i]['product_id'],
                'remarks'            => $request->details[$i]['remarks'],
                'current_qty'        => $request->details[$i]['current_qty'],
                'receive_qty'        => $request->details[$i]['receive_qty'],
                'trans_to_packing_qty' => $request->details[$i]['trans_to_packing_qty'],
                'unit_price'         => $request->details[$i]['unit_price'],
                'overhead_price'     => $request->details[$i]['overhead_price'],
                'total_price'        => $request->details[$i]['total_price'],
                'remain_qty'         => $request->details[$i]['remain_qty'],
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
                'assembling_section_id'   => $id,
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

    public function getAssemblingSetupData(Request $request)
    {

        $allProducts   = ProductionProducts::where('production_products.status', '1')
                                ->leftJoin('inventory_current_stocks', 'inventory_current_stocks.inventory_current_stocks_product_id', '=', 'production_products.id')
                                ->leftJoin('production_current_stocks', 'production_current_stocks.product_id', '=', 'production_products.id')
                                //->where('inventory_current_stocks.inventory_current_stocks_warehouse_id', $request->warehouse_id)
                                // ->where('production_products.is_raw_material_status', '1')
                                ->selectRaw('production_products.id,production_products.product_name,production_products.buying_price,
                                            inventory_current_stocks.inventory_stocks_current_qty,
                                            production_current_stocks.semi_finished_stk_receive_qty
                                            ')
                                ->get();

        $dataFormat = [];
        foreach ($allProducts as $key => $value) {

            $testingProduct = ProductionTestingDetails::where('product_id',$value->id)
                                    ->orderBy('id','desc')
                                    ->First();
            $unitPrice = $testingProduct ? $testingProduct->unit_price : $value->buying_price;

            $dataFormat[] = [
                'id' => $value->id,
                'product_name' => $value->product_name,
                'remarks' => '',
                'semi_finished_stock_qty' =>  $value->semi_finished_stk_receive_qty,
                'inventory_stock_qty' =>  $value->inventory_stocks_current_qty,
                'count_qty' =>  1,

                'semi_finished_use_qty' => 0,
                'total_used_qty' => 0,
                'unit_price' => $unitPrice,
                'total_price' => 0,
            ];
        }

        return $dataFormat;
    }

    public function getPdfData($id)
    {
        $editModeData = ProductionAssembling::with('warehouse','finish_product')->FindOrFail($id);
        $editModeDetailsData = ProductionAssemblingDetails::with('product')->where('assembling_section_id',$id)->get();

        $data = [
            'id'                   => $editModeData->id,
            'assembling_no'        => $editModeData->assembling_no,
            'assembling_date'      => dateConvertDBtoForm($editModeData->assembling_date),
            'warehouse_id'         => $editModeData->warehouse->purchase_ware_houses_name,
            'assembling_types'     => $editModeData->assembling_types,
            'finishing_product_id' => $editModeData->finish_product->product_name,
            'trans_to_packing_qty' => $editModeData->trans_to_packing_qty,
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
            'company'   => $company,
            'assembling'=> $data,

        );

        $html = \View::make('production.production_section.product_pdf.assembling_sectionPDF')->with($data);

        $mpdf = new \Mpdf\Mpdf([
            'margin_left'   => 20,
            'margin_right'  => 15,
            'margin_top'    => 48,
            'margin_bottom' => 25,
            'margin_header' => 10,
            'margin_footer' => 10
        ]);

        $title = 'Assembling Section';
        $author = 'iVenture';
        if ($company) {
            $title .= ' - '.$company->name;
            $author = $company->name;
        }

        $mpdf->SetProtection(array('print'));
        $mpdf->SetTitle($title);
        $mpdf->SetAuthor($author);
        $mpdf->SetWatermarkText("Assembling Section");
        $mpdf->showWatermarkText = true;
        $mpdf->watermark_font = 'DejaVuSansCondensed';
        $mpdf->watermarkTextAlpha = 0.07;
        $mpdf->SetDisplayMode('fullpage');
        // $stylesheet = file_get_contents(public_path().'/css/mpdf.css');
        // $mpdf->WriteHTML($stylesheet,1);
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }
}
