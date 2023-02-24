<?php

namespace App\Http\Controllers\Accounts\Reports;

use App\Exports\Accounts\LedgerReportExport;
use App\Model\Accounts\AccountsProjectInfo;
use App\Model\Accounts\AccountsSubCode2;
use App\Model\HR\HrManageEmployee;
use App\Model\LC\LcOpeningSection;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Accounts\AccountsChartofAccounts;
use Maatwebsite\Excel\Facades\Excel;
use DB;

use App\Model\Accounts\AccountsJournalEntryDetails;

class LedgerReportController extends Controller
{
    public function index(Request $request)
    {
        if ($request->filled('export')) {

            $file_type = $request->export;

            $data = [];
            $data['ledgers'] = $this->getLedgerFilterData($request);

            if($request->type == 'employee')
            {
                $res = HrManageEmployee::where('id',$request->type_id)->first();
                $data['type_name'] = $res ? $res->first_name : '';
            }
            elseif ($request->type == 'lc')
            {
                $data['type_name'] = LcOpeningSection::find($request->type_id)->first()->lc_no;
            }
            elseif ($request->type == "project")
            {
                $data['type_name'] = AccountsProjectInfo::find($request->type_id)->first()->project_name;
            }

            $coa_title = AccountsChartofAccounts::where('id', $request->account_id)->first();

            if($coa_title)
                $data['coa_title'] = $coa_title->chart_of_accounts_title;
            else
                $data['coa_title'] = '';

            $data['from_date'] = $request->from_date;
            $data['end_date'] = $request->end_date;


            if ($file_type=='pdf') {

                $this->exportAsPdf($data);

            } elseif ($file_type=='excel') {

                return Excel::download(new LedgerReportExport( $data['ledgers'] ), 'Ledger Reports.xlsx');

            } else {
                dd('Something went wrong.');
            }
        }

        $subCode2List  = AccountsSubCode2::where('status', '1')->get();
        $chartofAccountList  = AccountsChartofAccounts::where('status', '1')->get();
        return view('accounts.reports.ledger_report',compact('chartofAccountList','subCode2List'));
    }

    public function ledgerFilterReport(Request $request){

        if($request->ajax()){

            return $this->getLedgerFilterData($request);
        }

        $chartofAccountList  = AccountsChartofAccounts::where('status', '1')->get();
        return view('accounts.reports.ledger_report',compact('chartofAccountList'));
    }

    public function getLedgerFilterData(Request $request){

        $ledger_dr_balance = 0;
        $ledger_cr_balance = 0;
        $journalDetails = '';

            // DB::enableQueryLog();

            $where = '';
            $account_id = $request->account_id;
            $from_date2 = $request->from_date;
            $end_date2  = $request->end_date;

            $type  = $request->type;
            $type_id  = $request->type_id;

            if($from_date2!=''){
                $from_date=date('Y-m-d', strtotime(str_replace('/', '-',$from_date2)));
            }else{$from_date='';}

            if($end_date2!=''){
                $end_date=date('Y-m-d', strtotime(str_replace('/', '-',$end_date2)));
            }else{$end_date='';}

            if($account_id !='' && (int) $account_id !=0){$where.=" and dtails.char_of_account_id = $account_id ";}

            if($from_date!=''){$where.=" and journal.transaction_date>='$from_date' ";}
            if($end_date!=''){$where.=" and journal.transaction_date<='$end_date' ";}

            if($type!=''){$where .= " and journal.type = '$type' ";}
            if($type_id!=''){$where .= " and dtails.type_id = $type_id ";}

            $ledger = [];

            $transactionBalance = 0;
            $acc_head_code = Null;

            $coa_row = AccountsChartofAccounts::Find($account_id);
            if ($coa_row) {
                $transactionBalance = $coa_row->opening_balance;
                $acc_head_code = substr($coa_row->chart_of_account_code,0,1);
            }

            if ($from_date) {
                $before_that_date = date('Y-m-d',strtotime("$from_date -1days"));
                $dr_cr_sum =AccountsJournalEntryDetails::where('char_of_account_id',$account_id)
                                ->whereHas('get_jrnl_entry', function ($query) use ($before_that_date) {
                                    $query->where('approve_status',1);
                                    $query->whereDate('transaction_date', '<=', $before_that_date);
                                })
                                ->selectRaw('sum(debit_amount) as debit_amount,sum(credit_amount) as credit_amount')
                                ->first();

                        if($dr_cr_sum) {
                            if($acc_head_code == 1 || $acc_head_code == 5){
                                $transactionBalance = $transactionBalance - ($dr_cr_sum->credit_amount - $dr_cr_sum->debit_amount);
                            }
                            if($acc_head_code == 2 || $acc_head_code == 3){
                                $transactionBalance = $transactionBalance + ($dr_cr_sum->debit_amount - $dr_cr_sum->credit_amount );
                            }
                            if($acc_head_code == 4){ //Liabilities
                                $transactionBalance = $transactionBalance + ($dr_cr_sum->credit_amount - $dr_cr_sum->debit_amount );
                            }
                        }
            }

            if($type == ''){
                $ledger[]= [    //Very First Row Data
                    'date'    => '',
                    'ref_no'  => '',
                    'type'    => 'Opening Banalce',
                    'cheque_info'  => '',
                    'detail'  => '',
                    'debit'   => '',
                    'credit'  => '',
                    'balance' => round($transactionBalance,2) //$open_balance,
                ];
            }

            if($where !=''){

                $journalDetails = DB::table('accounts_journal_entry_details as dtails')
                    ->leftJoin('accounts_journal_entries as journal', 'dtails.journal_entry_id','=','journal.id')
                    ->leftJoin('accounts_chartof_accounts as chartof', 'dtails.char_of_account_id','=','chartof.id')
                    ->select('dtails.*',
                        'journal.transaction_date','journal.id','journal.transaction_reference_id','journal.transaction_type','journal.narration','journal.transaction_reference_no','journal.transaction_type_name','journal.total_credit',
                        'chartof.chart_of_accounts_title','chartof.chart_of_account_code',
                        'journal.type','dtails.type_id')
                    ->orderBy('journal.transaction_date','ASC')
                    ->whereRaw('journal.approve_status=1  '.$where)
                    ->get();

                $transactionBalance = $transactionBalance; //$open_balance;

                foreach ($journalDetails as $key => $value) {

                    if($acc_head_code == 2 || $acc_head_code == 3) {
                        $transactionBalance = $transactionBalance + ($value->debit_amount - $value->credit_amount);
                    }elseif($acc_head_code == 1 || $acc_head_code == 4)
                    {
                        $transactionBalance = $transactionBalance + ($value->credit_amount - $value->debit_amount);
                    }

                        //GetChequeInfo
                        $cheque_info = '';
                        if ($value->transaction_type==4) {

                            $results = DB::table('accounts_bank_payment_details_vouchers as acc')
                                                ->leftJoin('accounts_chartof_accounts as coa','acc.credit_account_id','=','coa.id')
                                                ->leftJoin('check_book_leafs as chq','acc.credit_account_id','=','chq.chart_ac_id')
                                                ->select('acc.check_leaf','acc.check_date','coa.chart_of_accounts_title','chq.account_no')

                                                ->where('acc.bank_payment_id',$value->transaction_reference_id)
                                                ->whereNotNull('acc.credit_account_id')
                                                ->limit(1)
                                                ->Get();


                            $cheque_info = '';
                            foreach ($results as $row) {
                                $cheque_info .= 'Bank Name : '.$row->chart_of_accounts_title.' <br> ';
                                $cheque_info .= 'Cheque No : '.$row->check_leaf.' <br> ';

                                $theDate = '';
                                if ($row->check_date) {
                                    $theDate = date('d M Y', strtotime($row->check_date));
                                }
                            }
                        } elseif($value->transaction_type==6) {

                            $results = DB::table('accounts_bank_receipt_details_vouchers as acc')
                                             ->leftJoin('accounts_chartof_accounts as coa','acc.debit_account_id','=','coa.id')
                                             ->leftJoin('check_book_leafs as chq','acc.debit_account_id','=','chq.chart_ac_id')
                                             ->select('acc.cheque_no','acc.cheque_date','coa.chart_of_accounts_title','chq.account_no')
                                             ->where('acc.bank_receipt_id',$value->transaction_reference_id)
                                            ->whereNotNull('acc.debit_account_id')
                                            ->limit(1)
                                            ->Get();

                            $cheque_info = '';
                            foreach ($results as $row) {
                                $cheque_info .= 'Bank Name : '.$row->chart_of_accounts_title.' <br> ';
                                $cheque_info .= 'Cheque No : '.$row->cheque_no.' <br> ';
                                $theDate = '';
                                if ($row->cheque_date) {
                                    $theDate = date('d M Y', strtotime($row->cheque_date));
                                }
                            }

                        } else {

                            /* @ return row object ::Account Main Voucher */
                            $mainVoucherRow = getAccMainVoucherRow($value->transaction_type,$value->transaction_reference_id);

                            if ($mainVoucherRow) {

                                $cheque_info = 'Leaf: '.$mainVoucherRow->leaf_number;
                                $theDate = '';
                                if ($mainVoucherRow->cheque_date) {
                                    $theDate = date('d M Y', strtotime($mainVoucherRow->cheque_date));
                                }
                            }
                        }

                        //$this->getRemarksOrNarration($value->transaction_type);

                        $ledger[] = [
                            'date'    => dateConvertDBtoForm($value->transaction_date) ,
                            'ref_no'  => $value->transaction_reference_no,
                            'type'    => $value->transaction_type_name,
                            'cheque_info'  => $cheque_info,
                            'detail'  => $value->transaction_type==3 ? $value->remarks : $value->narration,
                            'debit'   => $value->debit_amount,
                            'credit'  => $value->credit_amount,
                            'balance' => round($transactionBalance,2),
                        ];

                    $ledger_dr_balance = $ledger_dr_balance + $value->debit_amount;
                    $ledger_cr_balance = $ledger_cr_balance + $value->credit_amount;

                } //end foreach $journalDetails

                if($type == ''){
                    $ledger[] = [ //Very Last Row Data
                        'date'    => '',
                        'ref_no'  => '',
                        'type'    => 'Current Balance',
                        'cheque_info'  => '',
                        'detail'  => '',
                        'debit'   => '',
                        'credit'  => '',
                        'balance' => round($transactionBalance,2),
                    ];
                }

            }
        return $ledger;
    }

    function getRemarksOrNarration($transaction_type)
    {
        switch ($transaction_type)
        {
            case 2:
                //***"ContraEntry"***//

                break;
            case 3:
                //***"CashPayment"***//

                break;
            case 4:
                //***"BankPayment"***//

                break;
            case 5:
            //***"CashReceipt"***//

                break;
            case 6:
            //***"BankReceipt"***//

                break;
            default:
                return false;
        }
    }

    public function exportAsPdf($data)
    {
        $company = \App\Model\CompanyInfo::Find(1);


        $data = array(
            'company'   => $company,
            'ledgers'   => $data['ledgers'],
            'from_date' => $data['from_date'],
            'end_date'  => $data['end_date'],
            'coa_title' => $data['coa_title'],
            'type_name' => isset($data['type_name']) ? $data['type_name'] : '',
        );

        ini_set("pcre.backtrack_limit", "5000000");

        $html = \View::make('accounts.reports.report_pdf.ledger_reportPDF')->with($data);

        $mpdf = new \Mpdf\Mpdf([
            'margin_left' => 20,
            'margin_right' => 15,
            'margin_top' => 10,
            'margin_bottom' => 25,
            'margin_header' => 10,
            'margin_footer' => 10
        ]);

        $title = 'Ledger Report';
        $author = 'iVenture';
        if ($company) {
            $title .= ' - '.$company->name;
            $author = $company->name;
        }

        $mpdf->SetProtection(array('print'));
        $mpdf->SetTitle($title);
        $mpdf->SetAuthor($author);
        $mpdf->SetWatermarkText("Ledger Report");
        $mpdf->showWatermarkText = true;
        $mpdf->watermark_font = 'DejaVuSansCondensed';
        $mpdf->watermarkTextAlpha = 0.07;
        $mpdf->SetDisplayMode('fullpage');
        // $stylesheet = file_get_contents(public_path().'/css/mpdf.css');
        // $mpdf->WriteHTML($stylesheet,1);
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }

    function getCoaListBySub2($sub_code2_id)
    {
        $account_list = getChartOfAcc($sub_code2_id);
        return $account_list;
    }
}
