<?php

namespace App\Http\Controllers\Accounts;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Model\Production\ProductionProducts;
use App\Model\Sales\SalesCustomer;

use App\Model\Sales\SalesInvoice;
use App\Model\Sales\SalesDeliveryChallan;


use App\Model\Accounts\AccountsMainCode;
use App\Model\Accounts\AccountsCostCenter;
use App\Model\Accounts\AccountsChartofAccounts;

use App\Model\Accounts\AccountsJournalEntry;
use App\Model\Accounts\AccountsJournalEntryDetails;

use App\Model\Accounts\AccountsBankName;
use App\Model\Accounts\AccountsBankBranch;
use App\Model\Accounts\AccountsBankAccount;
use App\Model\Accounts\CheckBookLeafGenDetails;

use App\Model\Accounts\AccountsSalesInvoiceVoucher;
use App\Model\Accounts\AccountsSalesInvoiceVoucherDetails;

use Illuminate\Support\Facades\Auth;
use DB;

use App\Lib\Enumerations\AccountsTransactionType;

class SalesInvoiceVoucherController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $query = DB::table('accounts_sales_invoice_vouchers')
                ->leftJoin('sales_delivery_challans', 'accounts_sales_invoice_vouchers.sales_challan_id', '=', 'sales_delivery_challans.id')
                ->leftJoin('sales_invoices', 'accounts_sales_invoice_vouchers.sales_invoice_id', '=', 'sales_invoices.id')
                ->leftJoin('sales_customers', 'accounts_sales_invoice_vouchers.customer_id', '=', 'sales_customers.id')
                ->leftJoin('users as ura', 'accounts_sales_invoice_vouchers.created_by','=','ura.id')
                ->leftJoin('users as ured', 'accounts_sales_invoice_vouchers.updated_by','=','ured.id')
                ->leftJoin('users as urea', 'accounts_sales_invoice_vouchers.approve_by','=','urea.id')
                ->whereNull('accounts_sales_invoice_vouchers.deleted_at')
                ->select(['accounts_sales_invoice_vouchers.id AS id',
                    'accounts_sales_invoice_vouchers.vouchers_no',
                    'accounts_sales_invoice_vouchers.sales_date',
                    'accounts_sales_invoice_vouchers.total_qty',
                    'accounts_sales_invoice_vouchers.total_price',
                    'accounts_sales_invoice_vouchers.price_paid',
                    'accounts_sales_invoice_vouchers.price_due',
                    'accounts_sales_invoice_vouchers.approve_status',
                    'accounts_sales_invoice_vouchers.created_by',
                    'accounts_sales_invoice_vouchers.updated_by',
                    'accounts_sales_invoice_vouchers.approve_by',
                    'sales_delivery_challans.sales_delivery_no',
                    'sales_customers.sales_customer_name',
                    'sales_invoices.sales_invoices_no',
                    'ura.user_name as ad_name',
                    'ured.user_name as ed_name',
                    'urea.user_name as ap_name'
                ]);

            return datatables()->of($query)->toJson();
        }
        $productLists   = ProductionProducts::Select('production_products.*')
            ->where('production_products.status', '1')
            ->where('production_products.is_raw_material_status', '0')
            ->get();

        $challanLists  = SalesDeliveryChallan::where('status', '1')->get();
        $customerLists  = SalesCustomer::where('status', '1')->get();
        // $costCenterLists  = AccountsCostCenter::where('status', '1')->get();
        $crchartAcLists = AccountsChartofAccounts::where('status', '1')->get();
        $drchartAcLists = AccountsChartofAccounts::where('status', '1')->get();

        /*$drchartAcLists = AccountsChartofAccounts::where('status', '1')->where('chart_of_account_code','like', '3207%')
            ->orWhere('chart_of_account_code','like', '3208%')->get();*/

        $bankLists = AccountsBankName::where('status', '1')->get();

        $chartofAccountBankWiseList = AccountsChartofAccounts::where('status', '1')
                                        ->where('chart_of_account_code','like','3207%')
                                        ->get();

        return view('accounts.accounts_section.sales_invoice_voucher',compact( 'productLists',
            'customerLists','challanLists','chartofAccountBankWiseList','drchartAcLists','crchartAcLists','bankLists'));
    }



    public function show(Request $request,$id)
    {
        if ($request->filled('isSuperAdmin')) {
            if ($request->isSuperAdmin == 'unapprove') {

                //if (Auth::user()->email == 'software@iventurebd.com') {
                    $this->unApproveSalesInvVoucher($id);
                    return response()->json(['status' => 'success', 'message' => 'Sales Invoice Voucher was successfully UnApproved!']);
                //}
            }
        }

        try {
            $showData = AccountsSalesInvoiceVoucher::with('get_coa','get_check_leaf')->where('id',$id)->first();

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
                      ->where('transaction_reference_no','like','JSV-%')
                      ->first();

            $showDetailsData = AccountsJournalEntryDetails::with('coa')
                              ->where('journal_entry_id',$jrData->id)
                              ->get();

            $debitCreditDetails = AccountsJournalEntryDetails::with('coa')
                              ->where('journal_entry_id',$jrData->id)
                              ->first();

            if($showData->bank_id !=null){
                 $bankName =AccountsBankName::where('id',$showData->bank_id)->first()->accounts_bank_names;
            }else{
                $bankName ='';
            }

            $data = [
                'id'                => $showData->id,
                'vouchers_no'       => $showData->vouchers_no,
                'sales_date'        => date('d/m/Y', strtotime($showData->sales_date)),
                'total_price'       => $showData->total_price,
                'narration'         => $showData->narration,

                'bank_name'         => $bankName,
                'payment_type'      => $showData->payment_type,
                'account_name'      => $showData->account_name,
                'account_no'        => $bankAccountNo,
                'cheque_date'       => dateConvertDBtoForm($showData->cheque_date),
                'cheque_book'       => $showData->cheque_book,
                'cheque_leaf'       => $checkBookLeaf,
                'cheque_remarks'    => $showData->cheque_remarks,
                'journal_main'      => $jrData,

                'approve'           => $showData->approve_status,
                'total_amount_word' => getCurrency($showData->total_price),
                'get_details'       => $showDetailsData,
                'sub2_details'      => $debitCreditDetails,

            ];
            return response()->json(['status'=>'success','data'=>$data]);
        } catch(\Exception $e){
            //print_r($e->getMessage()); die();
            return response()->json(['status'=>'error','data'=>[]]);
        }
    }

    public function edit($id)
    {
        try {
            $editModeData        = AccountsSalesInvoiceVoucher::FindOrFail($id);
            $editModeDetailsData = AccountsSalesInvoiceVoucherDetails::where('ac_sales_voucher_id',$id)->get();
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
                'sales_challan_id'       => $editModeData->sales_challan_id,
                'sales_date'             => date('d/m/Y', strtotime($editModeData->sales_date)),
                'customer_id'            => $editModeData->customer_id,
                'tender_no'              => $editModeData->tender_no,

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
                'sub_total_price'        => $editModeData->sub_total_price,
                'commission'             => $editModeData->commission,
                'discount'               => $editModeData->discount,
                'vat'                    => $editModeData->vat,
                'vat_type'               => $editModeData->vat_type,

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

            print($e->getMessage());
            return response()->json(['status'=>'error',$result_data]);
        }
    }

    public function update(Request $request, $id)
    {
        date_default_timezone_set("Asia/Dhaka");
        $this->validate($request, [
            'dr_chart_of_account_id'    => 'required',
            'cr_chart_of_account_id'    => 'required',
            // 'cost_center_id'            => 'required',
            'payment_type'              => 'required',
            'price_paid'                => 'required',
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
        $journalRef='JSV-'.date('Y').date('m').$currentId;

        if($due<0){$amount =$paid;}else{$amount =$total;}
        $data_jr = [
            'transaction_reference_id'      => $id,
            'transaction_reference_no'      => $journalRef,
            'transaction_date'              => dateConvertFormtoDB($request->sales_date),
            'transaction_type'              => AccountsTransactionType::$salesInvVoucer,
            'transaction_type_name'         => AccountsTransactionType::$salesInvVoucerTitle,
            'cost_center_id'                => $request->cost_center_id,

            'bank_id'                       => $request->bank_id,
            'bank_reference'                => $request->account_no,

            'narration'                     => $request->narration,
            'total_debit'                   => $amount,
            'total_credit'                  => $amount,
            'approve_status' => 1,
            'created_by'                    => Auth::user()->id,
        ];


        try {
            DB::beginTransaction();

            $editModeData = AccountsSalesInvoiceVoucher::FindOrFail($id);

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

            /*if($editModeData->price_due > 0)
            {
                $editModeData->due_status = 0;
            }
            else
            {
                $editModeData->due_status = NULL;
            }*/


            if ($editModeData->cheque_leaf || $request->cheque_leaf) {
                if ($editModeData->cheque_leaf != $request->cheque_leaf) {
                    /* Check if it was changed. Then reverse status of the old one */
                    $this->updateCheckLeafStatus('id',$editModeData->cheque_leaf,0); //0=Available to use
                }
                $this->updateCheckLeafStatus('id',$request->cheque_leaf,2); //2=In Draft
            }

            $editModeData->save();


            if($approve == 1 ){

                DB::table('sales_delivery_challans')->where('id', $editModeData->sales_challan_id)->update(['account_status' => 1]);

                //approve_status
                $data_jr['approve_status'] = '1';
                $result_jr = AccountsJournalEntry::create($data_jr);

                $jr_details = $this->generateData($total, $due, $paid,$request, $result_jr->id);
                $details = $this->dataFormat($jr_details, $result_jr->id);
                AccountsJournalEntryDetails::insert($details);

                $this->currentBalanceIncrementDecrement($details);


                DB::table('accounts_sales_invoice_vouchers')->where('id', $id)->update([
                    'approve_status' => 1,
                    'approve_by'     => Auth::user()->id,
                    'approve_at'     => Carbon::now(),
                ]);

                CheckBookLeafGenDetails::where('id',$editModeData->cheque_leaf)->update(['use_status'=> 1]);// Check Book Leaf user_status update
            }
            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Sales Invoice successfully updated!']);
        }
        catch (\Exception $e) {
            DB::rollback();
             $mm = $e->getMessage();
             return response()->json(['status' => 'error', 'message' => $mm]);
            //return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }


    public function update_approval($id)
    {
        $req_id = $id;

        try {
            $approval = DB::table('accounts_sales_invoice_vouchers')->where('id',$req_id)->first()->approve_status;
            if($approval == 0)
            {
                DB::table('accounts_sales_invoice_vouchers')->where('id', $req_id)  // find your requisition
                    ->update(array('approve_status' => 1));
                return response()->json(['status' => 'success', 'message' => 'Sales Invoice Approved!']);
            }
            else{
                return response()->json(['status' => 'success', 'message' => 'Already Approved!']);
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            return response()->json(['status' => 'error', 'message' => $message]);
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
                'char_of_account_id' => $request->dr_chart_of_account_id,
                'remarks' => '',
                'debit_amount' => $request->price_paid,
                'credit_amount' => '',
            ];
            $dataFormat_jr[1] = [
                'journal_entry_id' => $id,
                'char_of_account_id' => $request->cr_chart_of_account_id,
                'remarks' => '',
                'debit_amount' => '',
                'credit_amount' => $request->price_paid,
            ];
        }
        else if($due != $total && $due>0){ //partial due
            $caid= SalesCustomer::findOrFail($request->customer_id);
            $dataFormat_jr[0] = [
                'journal_entry_id' => $id,
                'char_of_account_id' => $request->dr_chart_of_account_id,
                'remarks' => '',
                'debit_amount' => $paid,
                'credit_amount' => '',
            ];
            $dataFormat_jr[1] = [
                'journal_entry_id' => $id,
                'char_of_account_id' => $caid->chart_ac_id,
                'remarks' => '',
                'debit_amount' => $due,
                'credit_amount' => '',
            ];
            $dataFormat_jr[2] = [
                'journal_entry_id' => $id,
                'char_of_account_id' => $request->cr_chart_of_account_id,
                'remarks' => '',
                'debit_amount' => '',
                'credit_amount' => $total,
            ];

        }
        else if($due == $total){//full due
            $caid2= SalesCustomer::findOrFail($request->customer_id);
            $dataFormat_jr[0] = [//customer er
                'journal_entry_id' => $id,
                'char_of_account_id' => $caid2->chart_ac_id,
                'remarks' => '',
                'debit_amount' => $total,
                'credit_amount' => '',
            ];
            $dataFormat_jr[1] = [
                'journal_entry_id' => $id,
                'char_of_account_id' => $request->cr_chart_of_account_id,
                'remarks' => '',
                'debit_amount' => '',
                'credit_amount' => $total,
            ];
        }
        else if($due != $total && $due<0){//advance Pay
            $caid= SalesCustomer::findOrFail($request->customer_id);
            $dataFormat_jr[0] = [
                'journal_entry_id' => $id,
                'char_of_account_id' => $request->dr_chart_of_account_id, //cash
                'remarks' => '',
                'debit_amount' => $paid,
                'credit_amount' => '',
            ];
            $dataFormat_jr[1] = [
                'journal_entry_id' => $id,
                'char_of_account_id' => $caid->chart_ac_id, //customer
                'remarks' => '',
                'debit_amount' => '',
                'credit_amount' => abs($due),
            ];
            $dataFormat_jr[2] = [
                'journal_entry_id' => $id,
                'char_of_account_id' => $request->cr_chart_of_account_id, //sale
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


    public function update_approval_test($id)
    {
        //dd($request->id);
        $req_id = $id;
        try {
            $approval = DB::table('production_oil_requisition_for_rms')->where('id',$req_id)->first()->approval;
            if($approval == 0)
            {
                $req_products = DB::table('production_oil_requisition_for_rm_details')->where('requisition_for_rm_id',$req_id)->get();
                foreach($req_products as $req_product)
                {
                    $product_id = $req_product->product_id;
                    $product_qty = $req_product->qty;
                    $product_unit = $req_product->unit;

                    $purchase_product_qty = DB::table('purchase_stock_entry_details')->where('purchase_stock_entry_details_product_id',$product_id)->where('purchase_stock_entry_details_unit',$product_unit)->first()->purchase_stock_entry_details_qty;
                    $result = $purchase_product_qty - $product_qty;

                    if($result >= 0)
                    {
                        DB::table('purchase_stock_entry_details')
                            ->where('purchase_stock_entry_details_product_id', $product_id)
                            ->where('purchase_stock_entry_details_unit', $product_unit)
                            ->update(array(
                                    'purchase_stock_entry_details_qty' => $result,
                                    'updated_at' => date('Y/m/d H:i:s')//'status' => 1,
                                )
                            );
                    }
                }
                // update the record in the DB.
                DB::table('production_oil_requisition_for_rms')
                    ->where('id', $req_id)  // find your requisition
                    ->update(array('approval' => 1));
                return response()->json(['status' => 'success', 'message' => 'Requisition for RM Approved!']);
            }
            else{
                return response()->json(['status' => 'success', 'message' => 'Alredy Approved!']);
            }
        }catch (\Exception $e) {
            $message = $e->getMessage();
            return response()->json(['status' => 'error', 'message' => $message]);
        }
    }


    public function getSalesInvAcctNumbList(Request $request){
        return  AccountsBankAccount::where('accounts_bank_id',$request->bank_id)->get();

    }
    public function getSalesInvCheckLeafList(Request $request){
        return  CheckBookLeafGenDetails::where('account_number',$request->account_no)->where('use_status',0)->get();

    }

    private function updateCheckLeafStatus($column_name,$column_value,$status){
        CheckBookLeafGenDetails::where($column_name,$column_value)->update(['use_status'=> $status]);
    }

    private function unApproveSalesInvVoucher($id) {

        try{
            DB::beginTransaction();
            //Get Primary Table Data
            $data = AccountsSalesInvoiceVoucher::where('approve_status',1)->FindOrFail($id);

            //Get JournalEntry Table Data
            $journalData = AccountsJournalEntry::where('transaction_reference_id',$data->id)
                ->where('transaction_type',AccountsTransactionType::$salesInvVoucer)
                ->first();
            //REVERSE AMOUNT AND DELETE - JournalEntryDetails Table Data
            $allJournals = AccountsJournalEntryDetails::where('journal_entry_id',$journalData->id)->get();
            foreach ($allJournals as $row) {
                $amount = 0;
                $dr_cr = Null;
                if ($row->debit_amount && ($row->debit_amount > 0) ){
                    $amount = $row->debit_amount;
                    $dr_cr = 'dr';
                } elseif($row->credit_amount && ($row->credit_amount > 0) ){
                    $amount= $row->credit_amount;
                    $dr_cr = 'cr';
                }

                reverseDrCrAccountBalanceIncDec($row->char_of_account_id, $amount,$dr_cr);

                $row->delete();
            }

            $journalData->delete();

            CheckBookLeafGenDetails::where('id',$data->cheque_leaf)->update(['use_status'=> 0]);

            //Update Status Primary Table Data
            $data->approve_status = 0;
            $data->save();

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Sales Invoice Voucher was successfully UnApproved!']);
        }
        catch(\Exception $e){
            DB::rollback();
            $msg = $e->getMessage();
            return response()->json(['status' => 'error', 'message' => $msg]);
        }
    }
}
