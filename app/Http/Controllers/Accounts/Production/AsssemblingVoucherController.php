<?php

namespace App\Http\Controllers\Accounts\Production;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use App\Model\Production\ProductionAssembling;
use App\Model\Production\ProductionAssemblingDetails;

use App\Model\Accounts\AccountsChartofAccounts;

use App\Repositories\CommonRepositories;
use App\Lib\Enumerations\AccountsTransactionType;
use App\Model\Accounts\AccountsJournalEntry;
use App\Model\Accounts\AccountsJournalEntryDetails;

class AsssemblingVoucherController extends Controller
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
                ->where('production_assemblings.approve_status',1)
                ->select(['production_assemblings.id AS id',
                    'production_assemblings.assembling_no',
                    'production_assemblings.assembling_date',
                    'production_assemblings.narration',
                    'production_assemblings.trans_to_packing_qty',
                    'production_assemblings.created_by',
                    'production_assemblings.updated_by',
                    'production_assemblings.approve_by',
                    'production_assemblings.account_status',
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
        return view('accounts.product_section.acc_assembly_voucher',compact('chartofAccountList'));
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
            $editModeData = ProductionAssembling::with('warehouse')->with('finish_product')->FindOrFail($id);
            $editModeDetailsData = ProductionAssemblingDetails::with('product')->where('assembling_section_id',$id)->get();

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
                'assembling_no' => $editModeData->assembling_no,
                'assembling_date' => dateConvertDBtoForm($editModeData->assembling_date),
                'warehouse_id' => $editModeData->warehouse->purchase_ware_houses_name,
                'assembling_types' => $editModeData->assembling_types,
                'finishing_product_id' => $editModeData->finish_product->product_name,
                'trans_to_packing_qty' => $editModeData->trans_to_packing_qty,
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
            return response()->json(['status'=>'error','data'=>[]]);
        }
    }

    public function edit($id)
    {
        try {
            $editModeData = ProductionAssembling::with('warehouse')->with('finish_product')->FindOrFail($id);
            $editModeDetailsData = ProductionAssemblingDetails::with('product')->where('assembling_section_id',$id)->get();

            $debit_account_id = '';
            $credit_account_id = '';

            $jr_data = AccountsJournalEntry::where('transaction_reference_no',"Assembling-$id")->first();
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
                'assembling_no' => $editModeData->assembling_no,
                'assembling_date' => dateConvertDBtoForm($editModeData->assembling_date),
                'warehouse_id' => $editModeData->warehouse->purchase_ware_houses_name,
                'assembling_types' => $editModeData->assembling_types,
                'finishing_product_id' => $editModeData->finish_product->product_name,
                'trans_to_packing_qty' => $editModeData->trans_to_packing_qty,
                'unit_price' => $editModeData->unit_price,
                'total_price' => $editModeData->total_price,
                'narration' => $editModeData->narration,
                'total_rm_qty' => $editModeData->total_rm_qty,
                'total_rm_price' => $editModeData->total_rm_price,
                
                'debit_account_id' => $debit_account_id,
                'credit_account_id' => $credit_account_id,
                'details'    => $editModeDetailsData
            ];
            return response()->json(['status'=>'success','data'=>$data]);
        } catch(\Exception $e){
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
            $jr_data = AccountsJournalEntry::where('transaction_reference_no',"Assembling-$id")->first();
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

                $jr_data = AccountsJournalEntry::where('transaction_reference_no',"Assembling-$id")->first();
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
            'transaction_reference_no'  => "Assembling-$id",
            'transaction_date'          => dateConvertFormtoDB($request->assembling_date),
            'transaction_type'          => AccountsTransactionType::$ProductionAssemble,
            'transaction_type_name'     => AccountsTransactionType::$ProductionAssembleTitle,

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
        $appData = ProductionAssembling::FindOrFail($id);
        if ($appData->account_status == 0) {

            $appData->account_status = 1;
            $appData->acc_approve_by = Auth::user()->id;
            $appData->acc_approve_at = Carbon::now();
            $appData->save();
        }
    }
}
