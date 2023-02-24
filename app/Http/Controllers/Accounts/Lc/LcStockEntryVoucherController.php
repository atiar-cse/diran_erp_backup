<?php

namespace App\Http\Controllers\Accounts\Lc;

use App\Lib\Enumerations\AccountsTransactionType;
use App\Model\Accounts\AccountsChartofAccounts;
use App\Model\Accounts\AccountsJournalEntry;
use App\Model\Accounts\AccountsJournalEntryDetails;
use App\Model\Accounts\AccountsMainCode;
use App\Model\LC\LcCommercialInvoiceEntry;
use App\Model\LC\LcStockEntry;
use App\Model\LC\LcStockEntryDetails;
use App\Model\Production\ProductionMeasureUnit;
use App\Model\Production\ProductionProducts;
use App\Model\Purchase\PurchaseWareHouse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use DB;

class LcStockEntryVoucherController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = DB::table('lc_stock_entries')
                ->leftJoin('lc_opening_sections', 'lc_stock_entries.lc_opening_info_id', '=', 'lc_opening_sections.id')
                ->leftJoin('lc_commercial_invoice_entries', 'lc_stock_entries.lc_commercial_invoice_id', '=', 'lc_commercial_invoice_entries.id')
                ->leftJoin('purchase_ware_houses', 'lc_stock_entries.warehouse_id', '=', 'purchase_ware_houses.id')
                ->leftJoin('purchase_supplier_entries', 'lc_stock_entries.supplier_id', '=', 'purchase_supplier_entries.id')
                ->leftJoin('users', 'lc_stock_entries.created_by', '=', 'users.id')
                ->where('lc_stock_entries.approve_status',1)
                ->whereNull('lc_stock_entries.deleted_at')
                ->select(['lc_stock_entries.id AS id',
                    'lc_stock_entries.entry_date',
                    'lc_stock_entries.total_qty',
                    'lc_stock_entries.total_net_weight',
                    'lc_stock_entries.total_amount',
                    'lc_stock_entries.created_by',
                    'lc_stock_entries.updated_by',
                    'lc_stock_entries.approve_status',
                    'lc_stock_entries.account_status',
                    'lc_opening_sections.lc_no',
                    'lc_commercial_invoice_entries.ci_no',
                    'purchase_ware_houses.purchase_ware_houses_name',
                    'purchase_supplier_entries.purchase_supplier_name',
                    'users.user_name',
                ]);

            return datatables()->of($query)->toJson();
        }

        $ciLists = LcCommercialInvoiceEntry::with('get_lc_no')->select('id','ci_no','lc_opening_info_id')
            ->where('status',1)->where('approve_status',1)
            ->where('stock_enrty_status',0)->get();
        $productLists = ProductionProducts::where('status',1)->selectRaw('id,product_name,product_code,measure_unit_id')->get();
        $measureUnitList= ProductionMeasureUnit::where('status', '1')->get();
        $warehouseLists   = PurchaseWareHouse::where('status', '1')->get();
        $chartofAccountList = AccountsChartofAccounts::where('status', '1')->get();

        return view('accounts.lc_section.acc_lc_stock_entry',
            compact('ciLists','productLists','measureUnitList','warehouseLists','chartofAccountList'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        try {
            $editModeData = LcStockEntry::with('get_lc_no','get_ci_no','get_warehouse','get_supplier')->FindOrFail($id);
            $editModeDetailsData = LcStockEntryDetails::with('get_product','get_measure_unit')->where('lc_stock_entry_id',$id)->get();

            $jr_data = AccountsJournalEntry::where('transaction_reference_no',"LCStockE-$id")->First();
            if($jr_data){
                $narration = $jr_data->narration;
            }else{
                $narration = $editModeData->narration;
            }


            $data = [
                'entry_date' => dateConvertDBtoForm($editModeData->entry_date),
                'narration' => $narration,
                'total_qty' => $editModeData->total_qty,
                'total_net_weight' => $editModeData->total_net_weight,
                'total_gross_weight' => $editModeData->total_gross_weight,
                'total_amount' => $editModeData->total_amount,
                'approve_status' => $editModeData->account_status,
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



            $jr_data = AccountsJournalEntry::where('transaction_reference_no',"LCStockE-$id")->First();
            if($jr_data){
                $jr_details = AccountsJournalEntryDetails::where('journal_entry_id',$jr_data->id)->Get();
                $debit_account_id = '';
                $credit_account_id = '';
                foreach ($jr_details as $row) {
                    if ($row->debit_amount > 0) {
                        $debit_account_id = $row->char_of_account_id;
                    }
                    if ($row->credit_amount > 0) {
                        $credit_account_id = $row->char_of_account_id;
                    }
                }
                $narration = $jr_data->narration;
            }else{
                $debit_account_id = '';
                $credit_account_id = '';
                $narration = $editModeData->narration;
            }



            $data = [
                'id'    => $editModeData->id,
                'lc_commercial_invoice_id' => $editModeData->lc_commercial_invoice_id,
                'supplier_id' => $editModeData->supplier_id,
                'warehouse_id' => $editModeData->warehouse_id,
                'entry_date' => dateConvertDBtoForm($editModeData->entry_date),

                'total_qty' => $editModeData->total_qty,
                'total_net_weight' => $editModeData->total_net_weight,
                'total_gross_weight' => $editModeData->total_gross_weight,
                'total_amount' => $editModeData->total_amount,
                'status' => $editModeData->status,
                'narration' => $narration,


                'deleteID' => [],
                'details'    => $editModeDetailsData,

                'amount' => $editModeData->total_amount,
                'debit_account_id' => $debit_account_id,
                'credit_account_id' => $credit_account_id,
            ];
            return response()->json(['status'=>'success','data'=>$data]);
        } catch(\Exception $e){
            return response()->json(['status'=>'error','data'=>[]]);
        }
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'debit_account_id' => 'required',
            'credit_account_id' => 'required',
        ], [
            'debit_account_id.required' => 'Debit Account field is required.',
            'credit_account_id.required' => 'Creadit Account field is required.',
        ]);

        $approval = $request->approve;

        try {
            DB::beginTransaction();

            $debit_account_id = $request->debit_account_id;
            $credit_account_id = $request->credit_account_id;
            $amount  = $request->amount;


            $jr_data = AccountsJournalEntry::where('transaction_reference_no',"LCStockE-$id")->First();
            if($jr_data){
                $jr_details = AccountsJournalEntryDetails::where('journal_entry_id',$jr_data->id)->Get();
                foreach ($jr_details as $row) {
                    if ($row->debit_amount > 0) {
                        $row->char_of_account_id = $debit_account_id;
                        $row->save();
                    }
                    if ($row->credit_amount > 0) {
                        $row->char_of_account_id = $credit_account_id;
                        $row->save();
                    }
                }
            }
            else{
                $this->journalDataEntry($request, $id);
            }



            if($approval ==1){
                $this->approve($id);

                $jr_data = AccountsJournalEntry::where('transaction_reference_no',"LCStockE-$id")->First();
                $jr_data->approve_status = 1;
                $jr_data->save();

                debitAccountsBalanceIncDec($debit_account_id,$amount);
                creditAccountsBalanceIncDec($credit_account_id,$amount);
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
        //
    }

    public function approve($id)
    {
        $lcStockRow = LcStockEntry::with('get_ci_no')->FindOrFail($id);
        if ($lcStockRow->account_status == 0) {
            $lcStockRow->account_status = 1;
            $lcStockRow->updated_by = Auth::user()->id;
            $lcStockRow->save();
        }
    }

    private function journalDataEntry($request, $id){

        $approval = $request->approve;

        $jr_status = 0;
        if($approval ==1){
            $jr_status = 1;
        }

        $JournalData = [
            'transaction_reference_id' => $id,
            'transaction_reference_no' => "LCStockE-$id",
            'transaction_date' => dateConvertFormtoDB($request->entry_date),
            'transaction_type' => AccountsTransactionType::$lcStockEntry,
            'transaction_type_name' => AccountsTransactionType::$lcStockEntryTitle,
            'cost_center_id' => '',
            'branch_id' => '',
            'narration' => $request->narration,
            'total_debit' => $request->amount,
            'total_credit' => $request->amount,
            'approve_status' => $jr_status,
            'created_by' => Auth::user()->id,
        ];
        $result = AccountsJournalEntry::create($JournalData);

        $debitData =[
            'journal_entry_id' => $result->id,
            'char_of_account_id' => $request->debit_account_id,
            'debit_amount' => $request->amount,
        ];
        AccountsJournalEntryDetails::insert($debitData);

        $creditData =[
            'journal_entry_id' => $result->id,
            'char_of_account_id' => $request->credit_account_id,
            'credit_amount' => $request->amount,
        ];
        AccountsJournalEntryDetails::insert($creditData);

    }


}
