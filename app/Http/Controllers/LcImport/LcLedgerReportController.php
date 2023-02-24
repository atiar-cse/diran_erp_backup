<?php

namespace App\Http\Controllers\LcImport;

use App\Model\Accounts\AccountsJournalEntry;
use App\Model\Accounts\AccountsJournalEntryDetails;
use App\Model\Accounts\AccountsSubCode2;
use App\Model\LC\LcOpeningSection;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Accounts\AccountsChartofAccounts;
use Illuminate\Support\Facades\DB;

class LcLedgerReportController extends Controller
{
    public function index()
    {
        $chartofAccountList  = AccountsChartofAccounts::where('status', '1')->get();
        $lc_no  = LcOpeningSection::where('status', '1')->get();
        //$subCode2List        = AccountsSubCode2::where('status', '1')->get();
        return view('lc.lc_section.lc_ledger_report',compact('chartofAccountList','lc_no'));
    }


    public function ledgerFilterReport(Request $request){

        $ledger_dr_balance = 0;
        $ledger_cr_balance = 0;
        $return = '';



        if($request->ajax()){
            DB::enableQueryLog();
            $where='';

            $lc_no= $request->lc_no;
            $account_id= $request->account_id;
            $from_date2 = $request->from_date;
            $end_date2  = $request->end_date;

            if($from_date2!=''){
                $from_date=date('Y-m-d', strtotime(str_replace('/', '-',$from_date2)));
            }else{$from_date='';}

            if($end_date2!=''){
                $end_date=date('Y-m-d', strtotime(str_replace('/', '-',$end_date2)));
            }else{$end_date='';}

            if($account_id!=''){$where.=" and dtails.char_of_account_id = $account_id";}

            if($from_date!=''){$where.=" and journal.transaction_date>='$from_date'";}
            if($end_date!=''){$where.=" and journal.transaction_date<='$end_date'";}

            //$lc_transactions = AccountsJournalEntry::with('get_customer')->where('type','lc')->get();

            $lc_transactions = DB::table('accounts_journal_entry_details as dtails')
                ->leftJoin('accounts_journal_entries as journal', 'dtails.journal_entry_id','=','journal.id')
                ->leftJoin('accounts_chartof_accounts as chartof', 'dtails.char_of_account_id','=','chartof.id')
                ->select('dtails.*',
                    'journal.*',
                    'chartof.chart_of_accounts_title','chartof.chart_of_account_code')
                ->orderBy('dtails.id','desc')
                ->whereRaw('journal.approve_status=1  '.$where)
                ->where('dtails.type_id', $lc_no)
                ->where('dtails.credit_amount', 0)
                //->where('dtails.credit_amount', 0)
                ->where('journal.type', 'lc')
                ->get();

            //dd(DB::getQueryLog());

            $i = 1;

            foreach ($lc_transactions as $lc_transaction){

                $ledger[] = [
                    'sl'            => $i++,
                    'coa'           => $lc_transaction->chart_of_accounts_title,
                    'voucher_no'    => $lc_transaction->transaction_reference_no,
                    'date'          => $lc_transaction->transaction_date,
                    'voucher_type'  => $lc_transaction->transaction_type_name,
                    'narration'     => $lc_transaction->remarks,
                    'party'         => '',//$lc_transaction->get_customer ? $lc_transaction->get_customer->sales_customer_name : '',
                    'ref_type'      => $lc_transaction->type,
                    'ref_code'      => $lc_transaction->type_desc,
                    'credit'        => $lc_transaction->credit_amount,
                    'debit'         => $lc_transaction->debit_amount,
                    'balance'       => 0,
                ];

            }

            return $ledger;

            /*if($account_id!=''){$where.=" and dtails.char_of_account_id = $account_id ";}

            if($from_date!=''){$where.=" and journal.transaction_date>='$from_date' ";}
            if($end_date!=''){$where.=" and journal.transaction_date<='$end_date' ";}

            $ledger[] ='';

            $open_balance=AccountsChartofAccounts::where('id',$account_id)->first()->opening_balance;
            $chart_of_account_code = AccountsChartofAccounts::where('id',$account_id)->first()->chart_of_account_code;
            $acc_head_code = substr($chart_of_account_code,0,1);
            $ledger[0]= [
                'date'    => '',
                'type'    => 'Opening Banalce',
                'detail'  => '',
                'debit'   => '',
                'credit'  => '',
                'balance' => $open_balance,
            ];
            if($where !=''){

                $return = DB::table('accounts_journal_entry_details as dtails')
                    ->leftJoin('accounts_journal_entries as journal', 'dtails.journal_entry_id','=','journal.id')
                    ->leftJoin('accounts_chartof_accounts as chartof', 'dtails.char_of_account_id','=','chartof.id')
                    ->select('dtails.*',
                        'journal.transaction_date','journal.transaction_reference_id','journal.transaction_reference_no','journal.transaction_type_name','journal.total_credit',
                        'chartof.chart_of_accounts_title','chartof.chart_of_account_code')
                    ->orderBy('dtails.id','desc')
                    ->whereRaw('journal.approve_status=1  '.$where)
                    ->whereIn('journal.transaction_type', [68,69,70,71,72,73,74,75,76])
                    ->get();
                $transactionBalance = $open_balance;
                foreach ($return as $key => $value) {

                    $detailsDT = '';
                    $new_array = [];

                    if($value->transaction_type_name == 'Sales Voucher')
                    {
                        $AccountsSalesInvoiceVoucher_id = AccountsSalesInvoiceVoucher::where('id',$value->transaction_reference_id)->first()->sales_challan_id;

                        $detailsDT = SalesDeliveryChallanDetails::where('challan_id',$AccountsSalesInvoiceVoucher_id)->get();

                        foreach ($detailsDT as $detailData)
                        {
                            $new_array[] = ProductionProducts::where('id',$detailData->chd_product_id)->first()->product_name;
                            $new_array[] = $detailData->chd_qty;
                            $new_array[] = ProductionMeasureUnit::where('id',$detailData->chd_unit)->first()->measure_unit;
                            $new_array[] = $detailData->chd_unit_price.'Tk';
                        }
                    }


                    if($acc_head_code == 2 || $acc_head_code == 3) {
                        $transactionBalance = $transactionBalance + ($value->debit_amount - $value->credit_amount);
                    }elseif($acc_head_code == 1 || $acc_head_code == 4)
                    {
                        $transactionBalance = $transactionBalance + ($value->credit_amount - $value->debit_amount);
                    }


                    if(count($new_array) > 0)
                    {
                        $ledger[] = [
                            'date'    => $value->transaction_date,
                            'type'    => $value->transaction_type_name,
                            'detail'  => $new_array,
                            'debit'   => $value->debit_amount,
                            'credit'  => $value->credit_amount,
                            'balance' => $transactionBalance,
                        ];
                    }else
                    {
                        $ledger[] = [
                            'date'    => $value->transaction_date,
                            'type'    => $value->transaction_type_name,
                            'detail'  => '',
                            'debit'   => $value->debit_amount,
                            'credit'  => $value->credit_amount,
                            'balance' => $transactionBalance,
                        ];
                    }



                    $ledger_dr_balance = $ledger_dr_balance + $value->debit_amount;

                    $ledger_cr_balance = $ledger_cr_balance + $value->credit_amount;

                }


                // echo $acc_head_code;
                //exit();
                if($acc_head_code == 2 || $acc_head_code == 3)
                {
                    $ledger[count($ledger)] = [
                        'date'    => '',
                        'type'    => 'Current Banalce',
                        'detail'  => '',
                        'debit'   => '',
                        'credit'  => '',
                        'balance' => ($open_balance+$ledger_dr_balance)-$ledger_cr_balance,
                    ];
                }
                elseif($acc_head_code == 1 || $acc_head_code == 4)
                {
                    $ledger[count($ledger)] = [
                        'date'    => '',
                        'type'    => 'Current Banalce',
                        'detail'  => '',
                        'debit'   => '',
                        'credit'  => '',
                        'balance' => ($open_balance+$ledger_cr_balance)-$ledger_dr_balance,
                    ];
                }
            }
            */

        }

        $chartofAccountList  = AccountsChartofAccounts::where('status', '1')->get();

        return view('accounts.reports.ledger_report',compact('chartofAccountList'));


    }

}
