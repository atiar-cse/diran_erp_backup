<?php

namespace App\Http\Controllers\Accounts;

use App\Model\Accounts\AccountsBankAccount;
use App\Model\Accounts\CheckBookLeafGenDetails;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Model\Production\ProductionProducts;
use App\Model\Purchase\PurchaseSupplierEntry;

use App\Model\Purchase\PurchaseReturn;

use App\Model\Accounts\AccountsMainCode;
use App\Model\Accounts\AccountsCostCenter;
use App\Model\Accounts\AccountsChartofAccounts;
use App\Model\Accounts\AccountsJournalEntry;
use App\Model\Accounts\AccountsJournalEntryDetails;

use App\Model\Accounts\AccountsBankName;

use App\Model\Accounts\AccountsPurchaseReturnVoucher;
use App\Model\Accounts\AccountsPurchaseReturnVoucherDetails;

use Illuminate\Support\Facades\Auth;
use DB;

use App\Lib\Enumerations\AccountsTransactionType;

class PurchaseReturnVoucherController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {


            $query = DB::table('accounts_purchase_return_vouchers')
                ->leftJoin('purchase_returns', 'accounts_purchase_return_vouchers.purchase_return_id', '=', 'purchase_returns.id')
                ->leftJoin('purchase_supplier_entries', 'accounts_purchase_return_vouchers.supplier_id', '=', 'purchase_supplier_entries.id')
                ->leftJoin('users as ura', 'accounts_purchase_return_vouchers.created_by','=','ura.id')
                ->leftJoin('users as ured', 'accounts_purchase_return_vouchers.updated_by','=','ured.id')
                ->leftJoin('users as urea', 'accounts_purchase_return_vouchers.approve_by','=','urea.id')
                ->whereNull('accounts_purchase_return_vouchers.deleted_at')
                ->select(['accounts_purchase_return_vouchers.id AS id',
                    'accounts_purchase_return_vouchers.vouchers_no',
                    'accounts_purchase_return_vouchers.total_qty',
                    'accounts_purchase_return_vouchers.total_price',
                    'accounts_purchase_return_vouchers.purchase_return_date',
                    'accounts_purchase_return_vouchers.price_paid',
                    'accounts_purchase_return_vouchers.price_due',
                    'accounts_purchase_return_vouchers.approve_status',
                    'accounts_purchase_return_vouchers.created_by',
                    'accounts_purchase_return_vouchers.updated_by',
                    'accounts_purchase_return_vouchers.approve_by',
                    'purchase_returns.po_return_no',
                    'purchase_supplier_entries.purchase_supplier_name',
                    'ura.user_name as ad_name',
                    'ured.user_name as ed_name',
                    'urea.user_name as ap_name'
                ]);

            return datatables()->of($query)->toJson();
        }


        $productLists   = ProductionProducts::Select('production_products.*')
            ->where('production_products.status', '1')
            ->where('production_products.is_raw_material_status', '1')
            ->get();
        $returnLists   = PurchaseReturn::where('status', '1')->get();
        $supplierLists  = PurchaseSupplierEntry::where('status', '1')->get();
        // $costCenterLists  = AccountsCostCenter::where('status', '1')->get();

        $crchartAcLists = AccountsChartofAccounts::where('status', '1')->get();
        /*$drchartAcLists = AccountsChartofAccounts::where('status', '1')->where('chart_of_account_code','like', '3207%')
            ->orWhere('chart_of_account_code','like', '3208%')->get();*/

        $drchartAcLists = AccountsChartofAccounts::where('status', '1')->get();

        $bankLists = AccountsBankName::where('status', '1')->get();

        $chartofAccountBankWiseList = AccountsChartofAccounts::where('status', '1')
                                        ->where('chart_of_account_code','like','3207%')
                                        ->get();

        return view('accounts.accounts_section.purchase_return_voucher',compact( 'productLists','returnLists',
            'supplierLists','chartofAccountBankWiseList','drchartAcLists','crchartAcLists','bankLists'));
    }



    public function show($id)
    {
        try {

            $showData = AccountsPurchaseReturnVoucher::with('get_coa','get_check_leaf')->FindOrFail($id);

            if($showData->get_coa != null){
                $bankAccountNo = $showData->get_coa->chart_of_accounts_title . ' ('.$showData->get_coa->chart_of_account_code.')';
            }else{
                $bankAccountNo ='';
            }

            if($showData->cheque_leaf != null){
                $checkBookLeaf = $showData->get_check_leaf->leaf_number;
            }else{
                $checkBookLeaf ='';
            }

            $jrData = AccountsJournalEntry::where('transaction_reference_id',$id)
                                          ->where('transaction_reference_no','like','JPRV-%')
                                          ->first();

            $showDetailsData =AccountsJournalEntryDetails::with('coa')
                                         ->where('journal_entry_id',$jrData->id)
                                         ->get();

            $debitCreditDetails = AccountsJournalEntryDetails::with('coa')
                                        ->where('journal_entry_id',$jrData->id)
                                        ->first();

            if($showData->bank_id !=null){
                $bankName =AccountsBankName::where('id',$showData->bank_id)->first()->accounts_bank_names;
            }else{
                $bankName='';
            }


            $data = [
                'id'                  => $showData->id,
                'vouchers_no'         => $showData->vouchers_no,
                'purchase_return_date'=> date('d/m/Y', strtotime($showData->purchase_return_date)),
                'total_price'         => $showData->total_price,
                'narration'           => $showData->narration,

                'bank_name'           => $bankName,
                'payment_type'        => $showData->payment_type,
                'account_name'        => $showData->account_name,
                'account_no'          => $bankAccountNo,
                'cheque_date'         => dateConvertDBtoForm($showData->cheque_date),
                'cheque_leaf'         => $checkBookLeaf,
                'cheque_book'         => $showData->cheque_book,
                'cheque_remarks'      => $showData->cheque_remarks,

                'approve'             => $showData->approve_status,
                'total_amount_word'   => getCurrency($showData->total_price),
                'journal_main'        => $jrData,
                'get_details'         => $showDetailsData,
                'sub2_details'        => $debitCreditDetails,

            ];

            return response()->json(['status'=>'success','data'=>$data]);
        } catch(\Exception $e){
            return response()->json(['status'=>'error','data'=>[]]);
        }
    }

    public function edit($id)
    {
        try {
            $editModeData = AccountsPurchaseReturnVoucher::FindOrFail($id);
            $editModeDetailsData = AccountsPurchaseReturnVoucherDetails::where('ac_pruchase_return_voucher_id',$id)->get();

            $editCheckLeaf       = CheckBookLeafGenDetails::where('id',$editModeData->cheque_leaf)->get();
            $editModeAccountNo   = AccountsBankAccount::where('id',$editModeData->account_no)->get();

            if($editModeData->cheque_date!=''){
                $cheque_date =date('d/m/Y', strtotime($editModeData->cheque_date));
            }else{
                $cheque_date=date('d/m/Y');
            }

            if($editModeData->price_paid == null){$paid=$editModeData->total_price;}else{$paid=$editModeData->price_paid;}
            if($editModeData->price_due == null){$due=0;}else{$due=$editModeData->price_due;}

            $data = [
                'id'                     => $editModeData->id,
                'vouchers_no'            => $editModeData->vouchers_no,
                'purchase_return_id'     => $editModeData->purchase_return_id,
                'purchase_return_date'   => date('d/m/Y', strtotime($editModeData->purchase_return_date)),
                'supplier_id'            => $editModeData->supplier_id,

                'dr_chart_of_account_id' => $editModeData->dr_chart_of_account_id,
                'cr_chart_of_account_id' => $editModeData->cr_chart_of_account_id,
                'cost_center_id'         => $editModeData->cost_center_id,
                'narration'              => $editModeData->narration,

                'payment_type'           => $editModeData->payment_type,
                'bank_id'                => $editModeData->bank_id,
                'cheque_date'            => $cheque_date,
                'account_name'           => $editModeData->account_name,
                'account_no'             => $editModeData->account_no,
                'cheque_book'            => $editModeData->cheque_book,
                'cheque_leaf'            => $editModeData->cheque_leaf,
                'cheque_remarks'         => $editModeData->cheque_remarks,

                'total_qty'              => $editModeData->total_qty,
                'total_price'            => $editModeData->total_price,
                'price_paid'             => $paid,
                'price_due'              => $due,
                'approve_status'         => $editModeData->approve_status,

                'details'                => $editModeDetailsData,
            ];
            $result_data =[
                'data'       =>$data,
                'account_no' =>$editModeAccountNo,
                'cheque_leaf'=>$editCheckLeaf,
            ];
            return response()->json($result_data);
        } catch(\Exception $e){
            return response()->json(['status'=>'error',$result_data]);
        }
    }

    public function update(Request $request, $id)
    {
        date_default_timezone_set("Asia/Dhaka");
        $this->validate($request, [
            'dr_chart_of_account_id' => 'required',
            'cr_chart_of_account_id' => 'required',
            // 'cost_center_id' => 'required',
            'payment_type' => 'required',
            'price_paid' => 'required',
        ]);

        if($request->cheque_date!=''){
            $date = str_replace('/', '-', $request->cheque_date);
            $date_val =date('Y-m-d', strtotime($date));
        }else{
            $date_val='';
        }

        $approve = $request->approve_status;

        $total= $request->total_price;
        $paid= $request->price_paid;
        $due = $request->price_due;

        $jid = AccountsJournalEntry::count();
        $currentId = $jid+1;
        $journalRef='JPRV-'.date('Y').date('m').$currentId;

        if( $due<0 ){ $amount =$paid; }else{ $amount =$total; }
        $data_jr = [
            'transaction_reference_id' => $id,
            'transaction_reference_no' => $journalRef,
            'transaction_date' => dateConvertFormtoDB($request->purchase_return_date),
            'transaction_type' => AccountsTransactionType::$purchaseInvRtnVoucer,
            'transaction_type_name' => AccountsTransactionType::$purchaseInvRtnVoucerTitle,
            'cost_center_id' => $request->cost_center_id,

            'bank_id' => $request->bank_id,
            'bank_reference' => $request->account_no,

            'narration' => $request->narration,
            'total_debit' => $amount,
            'total_credit' => $amount,
            'approve_status' => 1,
            'created_by' => Auth::user()->id,
        ];

        /*$data = [
            'dr_chart_of_account_id' => $request->dr_chart_of_account_id,
            'cr_chart_of_account_id' => $request->cr_chart_of_account_id,
            'cost_center_id' => $request->cost_center_id,
            'payment_type' => $request->payment_type,
            'bank_id' => $request->bank_id,

            'cheque_date' => $date_val,
            'account_name' => $request->account_name,
            'account_no' => $request->account_no,
            'cheque_book' => $request->cheque_book,
            'cheque_leaf' => $request->cheque_leaf,
            'cheque_remarks' => $request->cheque_remarks,

            'narration' => $request->narration,
            'price_paid' => $request->price_paid,
            'price_due' => abs($request->price_due),
            'updated_by' => Auth::user()->id,
        ];*/


        try {
            DB::beginTransaction();


            $editModeData = AccountsPurchaseReturnVoucher::FindOrFail($id);

            $editModeData->dr_chart_of_account_id = $request->dr_chart_of_account_id;
            $editModeData->cr_chart_of_account_id = $request->cr_chart_of_account_id;
            $editModeData->payment_type           = $request->payment_type;
            $editModeData->bank_id                = $request->bank_id;
            $editModeData->cheque_date            = $date_val;
            $editModeData->account_name           = $request->account_name;
            $editModeData->account_no             = $request->account_no;
            $editModeData->cheque_book            = $request->cheque_book;
            $editModeData->cheque_leaf            = $request->cheque_leaf;
            $editModeData->cheque_remarks         = $request->cheque_remarks;
            $editModeData->narration              = $request->narration;
            $editModeData->price_paid             = $request->price_paid;
            $editModeData->price_due              = abs($request->price_due);

            if($approve!=1){
                $editModeData->updated_by = Auth::user()->id;
            }



            if ($editModeData->cheque_leaf || $request->cheque_leaf) {
                if ($editModeData->cheque_leaf != $request->cheque_leaf) {
                    /* Check if it was changed. Then reverse status of the old one */
                    $this->updateCheckLeafStatus('id',$editModeData->cheque_leaf,0); //0=Available to use
                }
                $this->updateCheckLeafStatus('id',$request->cheque_leaf,2); //2=In Draft
            }

            $editModeData->save();

            if($approve == 1 ) {

                DB::table('purchase_returns')->where('id', $editModeData->purchase_return_id)->update(['account_status' => 1]);


                $data_jr['approve_status'] = '1';
                $result_jr = AccountsJournalEntry::create($data_jr);

                $jr_details = $this->generateData($total, $due, $paid, $request, $result_jr->id);
                $details = $this->dataFormat($jr_details, $result_jr->id);
                AccountsJournalEntryDetails::insert($details);

                $this->currentBalanceIncrementDecrement($details);

              /////////////////////////// Approve By ////////////////////////////////////
                DB::table('accounts_purchase_return_vouchers')->where('id', $id)->update([
                    'approve_status' => 1,
                    'approve_by'     => Auth::user()->id,
                    'approve_at'     => Carbon::now(),
                ]);

                CheckBookLeafGenDetails::where('id',$editModeData->cheque_leaf)->update(['use_status'=> 1]);// Check Book Leaf user_status update

            }
            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Purchase Return Invoice successfully updated!']);
        }
        catch (\Exception $e) {

            print($e->getMessage());
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }


    public function currentBalanceIncrementDecrement($details){
        $count = count($details);
        for ($i = 0; $i < $count; $i++) {
            $code_find=AccountsChartofAccounts::findOrFail($details[$i]['char_of_account_id']);
            $code=substr($code_find['chart_of_account_code'],0,1);
            //echo $code.'<br/>'.$details[$i]['char_of_account_id'].'<br/>';
            $code_head=AccountsMainCode::where('main_code',$code)->first();
            //echo $code_head->main_code_title.'<br/>';

            if($code_head->main_code_title == 'Assets'){
                if($details[$i]['debit_amount'] =='' || $details[$i]['debit_amount'] ==0) {//cr
                    //echo 'cr ->dec';
                    AccountsChartofAccounts::where('id', $details[$i]['char_of_account_id'])
                        ->decrement('current_balance', $details[$i]['credit_amount']);
                }
                else{
                    //echo 'dr ->inc';
                    AccountsChartofAccounts::where('id', $details[$i]['char_of_account_id'])
                        ->increment('current_balance', $details[$i]['debit_amount']);
                }
            }
            elseif($code_head->main_code_title == 'Expense'){
                if($details[$i]['debit_amount'] =='' || $details[$i]['debit_amount'] ==0) {
                    //echo 'cr ->dec';
                    AccountsChartofAccounts::where('id', $details[$i]['char_of_account_id'])
                        ->decrement('current_balance', $details[$i]['credit_amount']);
                }
                else{
                    //echo 'dr ->inc';
                    AccountsChartofAccounts::where('id', $details[$i]['char_of_account_id'])
                        ->increment('current_balance', $details[$i]['debit_amount']);
                }
            }
            elseif($code_head->main_code_title == 'Income'){
                if($details[$i]['debit_amount'] =='' || $details[$i]['debit_amount'] ==0) {//cr
                    //echo 'cr ->inc';
                    AccountsChartofAccounts::where('id', $details[$i]['char_of_account_id'])
                        ->increment('current_balance', $details[$i]['credit_amount']);
                }
                else{
                    //echo 'dr ->dec';
                    AccountsChartofAccounts::where('id', $details[$i]['char_of_account_id'])
                        ->decrement('current_balance', $details[$i]['debit_amount']);
                }
            }
            elseif($code_head->main_code_title == 'Liabilities'){
                if($details[$i]['debit_amount'] =='' || $details[$i]['debit_amount'] ==0) {//cr
                    //echo 'cr ->inc';
                    AccountsChartofAccounts::where('id', $details[$i]['char_of_account_id'])
                        ->increment('current_balance', $details[$i]['credit_amount']);
                }
                else{
                    //echo 'dr ->dec';
                    AccountsChartofAccounts::where('id', $details[$i]['char_of_account_id'])
                        ->decrement('current_balance', $details[$i]['debit_amount']);
                }
            }
            else{ //Equity / Capital
                if($details[$i]['debit_amount'] =='' || $details[$i]['debit_amount'] ==0) {//cr
                    //echo 'cr ->inc';
                    AccountsChartofAccounts::where('id', $details[$i]['char_of_account_id'])
                        ->increment('current_balance', $details[$i]['credit_amount']);
                }
                else{
                    //echo 'dr ->dec';
                    AccountsChartofAccounts::where('id', $details[$i]['char_of_account_id'])
                        ->decrement('current_balance', $details[$i]['debit_amount']);
                }
            }
        }
    }

    public function generateData($total, $due, $paid, $request, $id){
        if($due == 0){//Paid all amount
            $dataFormat_jr[0] = [
                'journal_entry_id' => $id ,
                'char_of_account_id' => $request->dr_chart_of_account_id, //cash
                'remarks' => '',
                'debit_amount' => $request->price_paid,
                'credit_amount' => '',
            ];
            $dataFormat_jr[1] = [
                'journal_entry_id' => $id,
                'char_of_account_id' => $request->cr_chart_of_account_id, //purchase
                'remarks' => '',
                'debit_amount' => '',
                'credit_amount' => $request->price_paid,
            ];
        }
        else if($due != $total && $due>0){ //partial due
            $caid= PurchaseSupplierEntry::findOrFail($request->supplier_id);
            $dataFormat_jr[0] = [
                'journal_entry_id' => $id,
                'char_of_account_id' => $request->dr_chart_of_account_id, //cash
                'remarks' => '',
                'debit_amount' => $paid,
                'credit_amount' => '',
            ];
            $dataFormat_jr[1] = [
                'journal_entry_id' => $id,
                'char_of_account_id' => $caid->chart_ac_id, //supplier
                'remarks' => '',
                'debit_amount' => $due,
                'credit_amount' => '',
            ];
            $dataFormat_jr[2] = [
                'journal_entry_id' => $id,
                'char_of_account_id' => $request->cr_chart_of_account_id, //purchase
                'remarks' => '',
                'debit_amount' => '',
                'credit_amount' => $total,
            ];

        }
        else if($due == $total){//full due
            $caid2= PurchaseSupplierEntry::findOrFail($request->supplier_id);
            $dataFormat_jr[0] = [//customer er
                'journal_entry_id' => $id,
                'char_of_account_id' => $caid2->chart_ac_id, //supplier
                'remarks' => '',
                'debit_amount' => $total,
                'credit_amount' => '',
            ];
            $dataFormat_jr[1] = [
                'journal_entry_id' => $id,
                'char_of_account_id' => $request->cr_chart_of_account_id, //purchase
                'remarks' => '',
                'debit_amount' => '',
                'credit_amount' => $total,
            ];
        }
        else if($due != $total && $due<0){//advance Pay
            $caid= PurchaseSupplierEntry::findOrFail($request->supplier_id);
            $dataFormat_jr[0] = [
                'journal_entry_id' => $id,
                'char_of_account_id' => $request->dr_chart_of_account_id, //cash
                'remarks' => '',
                'debit_amount' => $paid,
                'credit_amount' => '',
            ];
            $dataFormat_jr[1] = [
                'journal_entry_id' => $id,
                'char_of_account_id' => $caid->chart_ac_id, //supplier
                'remarks' => '',
                'debit_amount' => '',
                'credit_amount' => abs($due),
            ];
            $dataFormat_jr[2] = [
                'journal_entry_id' => $id,
                'char_of_account_id' => $request->cr_chart_of_account_id, //purchase
                'remarks' => '',
                'debit_amount' => '',
                'credit_amount' => $total,
            ];
        }
        return $dataFormat_jr;

    }

    public function dataFormat($jr_details, $id)
    {
        $dataFormat = [];
        $count = count($jr_details);
        for ($i = 0; $i < $count; $i++) {
            $dataFormat[$i] = [
                'journal_entry_id' => $id,
                'char_of_account_id' => $jr_details[$i]['char_of_account_id'],
                'remarks' => $jr_details[$i]['remarks'],
                'debit_amount' => $jr_details[$i]['debit_amount'],
                'credit_amount' => $jr_details[$i]['credit_amount'],
            ];
        }
        return $dataFormat;
    }

    public function getPurchRetnAcctNumbList(Request $request){
        return  AccountsBankAccount::where('accounts_bank_id',$request->bank_id)->get();

    }
    public function getPurchRetnCheckLeafList(Request $request){
        return  CheckBookLeafGenDetails::where('account_number',$request->account_no)->where('use_status',0)->get();

    }

    private function updateCheckLeafStatus($column_name,$column_value,$status){
        CheckBookLeafGenDetails::where($column_name,$column_value)->update(['use_status'=> $status]);
    }
}
