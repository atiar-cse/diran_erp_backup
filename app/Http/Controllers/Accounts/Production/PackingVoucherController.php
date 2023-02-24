<?php

namespace App\Http\Controllers\Accounts\Production;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use App\Model\Production\ProductionPacking;
use App\Model\Production\ProductionPackingDetails;

use App\Model\Accounts\AccountsChartofAccounts;

use App\Repositories\CommonRepositories;
use App\Lib\Enumerations\AccountsTransactionType;
use App\Model\Accounts\AccountsJournalEntry;
use App\Model\Accounts\AccountsJournalEntryDetails;

class PackingVoucherController extends Controller
{
    protected $commonRepositories;
    public function __construct(CommonRepositories $commonRepositories)
    {
        $this->commonRepositories = $commonRepositories;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {

            $query = DB::table('production_packings')
                ->leftJoin('production_products', 'production_packings.finishing_product_id', '=', 'production_products.id')
                ->leftJoin('users as ura', 'production_packings.created_by','=','ura.id')
                ->leftJoin('users as ured', 'production_packings.updated_by','=','ured.id')
                ->leftJoin('users as urea', 'production_packings.approve_by','=','urea.id')
                ->whereNull('production_packings.deleted_at')
                ->where('production_packings.approve_status',1)
                ->select(['production_packings.id AS id',
                    'production_packings.packing_no',
                    'production_packings.packing_date',
                    'production_packings.narration',
                    'production_packings.total_rm_qty',
                    'production_packings.total_rm_price',                    
                    'production_packings.created_by',
                    'production_packings.updated_by',
                    'production_packings.approve_by',
                    'production_packings.account_status',
                    'production_products.product_name',
                    'ura.user_name as ad_name',
                    'ured.user_name as ed_name',
                    'urea.user_name as ap_name'
                ]);

            return datatables()->of($query)->toJson();
        }

        $chartofAccountList = AccountsChartofAccounts::where('status', '1')
                                        ->select('id','chart_of_accounts_title','chart_of_account_code')
                                        ->orderBy('chart_of_account_code','ASC')
                                        ->get();        
        return view('accounts.product_section.acc_packing_voucher',compact('chartofAccountList'));
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
            $editModeData = ProductionPacking::with('warehouse','finish_product')->FindOrFail($id);
            $editModeDetailsData = ProductionPackingDetails::with('product')->where('packing_section_id',$id)->get();

            $jr_data = AccountsJournalEntry::where('transaction_reference_no',"Massbody-$id")->first();
            
            $dr_coa_code = '';
            $dr_coa_title = '';
            $cr_coa_code = '';
            $cr_coa_title = '';
            if ($jr_data) {

                $jr_details = AccountsJournalEntryDetails::with('coa')
                                            ->where('journal_entry_id',$jr_data->id)
                                            ->get();
                foreach ($jr_details as $row) {
                    if ($row->debit_amount > 0) {
                        $dr_coa_code = $row->coa->chart_of_account_code;
                        $dr_coa_title = $row->coa->chart_of_accounts_title;
                    }
                    if ($row->credit_amount > 0) {
                        $cr_coa_code = $row->coa->chart_of_account_code;
                        $cr_coa_title = $row->coa->chart_of_accounts_title;                        
                    }
                }
            }

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
                'account_status' => $editModeData->account_status,

                'dr_coa_code' => $dr_coa_code,
                'dr_coa_title' => $dr_coa_title,
                'cr_coa_code' => $cr_coa_code,
                'cr_coa_title' => $cr_coa_title,
                'details'    => $editModeDetailsData
            ];
            return response()->json(['status'=>'success','data'=>$data]);
        } catch(\Exception $e){
            print_r($e->getMessage());
            return response()->json(['status'=>'error','data'=>[]]);
        }
    }

    public function edit($id)
    {
        try {
            $editModeData = ProductionPacking::with('warehouse','finish_product')->FindOrFail($id);
            $editModeDetailsData = ProductionPackingDetails::with('product')->where('packing_section_id',$id)->get();

            $debit_account_id = '';
            $credit_account_id = '';

            $jr_data = AccountsJournalEntry::where('transaction_reference_no',"Packing-$id")->first();
            if ($jr_data) {

                $jr_details = AccountsJournalEntryDetails::where('journal_entry_id',$jr_data->id)->get();
                foreach ($jr_details as $row) {
                    if ($row->debit_amount > 0) {
                        $debit_account_id = $row->char_of_account_id;
                    }
                    if ($row->credit_amount > 0) {
                        $credit_account_id = $row->char_of_account_id;
                    }
                }
            }

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
                'account_status' => $editModeData->account_status,

                'debit_account_id' => $debit_account_id,
                'credit_account_id' => $credit_account_id,
                'details'    => $editModeDetailsData
            ];
            return response()->json(['status'=>'success','data'=>$data]);
        } catch(\Exception $e){
            print_r($e->getMessage());
            return response()->json(['status'=>'error','data'=>[]]);
        }
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'total_rm_price' => 'required',
        ], [
            'total_rm_price.required' => 'Required field.',
        ]);

        $app = $request->approve_status;
        if ($app == 1) {
            $this->validate($request, [
                'debit_account_id' => 'required',
                'credit_account_id' => 'required',
            ]);
        }

        try {
            DB::beginTransaction();

            //#Journal - UpdateOrCreate
            $jr_data = AccountsJournalEntry::where('transaction_reference_no',"Packing-$id")->first();
            if ($jr_data) {

                $jr_data->narration  = $request->narration;
                $jr_data->save();

                $jr_details = AccountsJournalEntryDetails::where('journal_entry_id',$jr_data->id)->get();
                foreach ($jr_details as $row) {
                    if ($row->debit_amount > 0) {
                        $row->debit_amount = $request->total_rm_price;
                        $row->char_of_account_id = $request->debit_account_id;
                        $row->save();
                    }
                    if ($row->credit_amount > 0) {
                        $row->credit_amount = $request->total_rm_price;
                        $row->char_of_account_id = $request->credit_account_id;
                        $row->save();
                    }
                }

            } else {

                if ($request->filled('debit_account_id') && $request->filled('credit_account_id')) {
                    $this->journalDataEntry($request, $id);
                }
            }


            if($app == 1) {
                $this->approve($id);

                $jr_data = AccountsJournalEntry::where('transaction_reference_no',"Packing-$id")->first();
                $jr_data->approve_status = 1;
                $jr_data->save();

                $jr_details = AccountsJournalEntryDetails::where('journal_entry_id',$jr_data->id)->get();
                $this->commonRepositories->currentBalanceIncrementDecrement($jr_details);
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Successfully updated!']);
        } catch (\Exception $e) {
            DB::rollback();
            print_r($e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    private function journalDataEntry($request, $id){

        $approval = $request->approve;

        $jr_status = 0;
        if($approval ==1){
            $jr_status = 1;
        }
        $JournalData = [
            'transaction_reference_id'  => $id,
            'transaction_reference_no'  => "Packing-$id",
            'transaction_date'          => dateConvertFormtoDB($request->packing_date),
            'transaction_type'          => AccountsTransactionType::$ProductionPacking,
            'transaction_type_name'     => AccountsTransactionType::$ProductionPackingTitle,

            'narration'                 => $request->narration,
            'total_debit'               => $request->total_rm_price,
            'total_credit'              => $request->total_rm_price,
            'approve_status'            => $jr_status,
            'created_by'                => Auth::user()->id,
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

    public function approve($id)
    {
        $appData = ProductionPacking::FindOrFail($id);
        if ($appData->account_status == 0) {

            $appData->account_status = 1;
            $appData->acc_approve_by = Auth::user()->id;
            $appData->acc_approve_at = Carbon::now();
            $appData->save();
        }
    }
}
