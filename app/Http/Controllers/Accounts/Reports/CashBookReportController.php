<?php

namespace App\Http\Controllers\Accounts\Reports;

use App\Exports\Accounts\CashBookExport;
use App\Model\Accounts\AccountsCashPaymentVoucher;
use App\Model\Accounts\AccountsCashReceiptDetailsVoucher;
use App\Model\Accounts\AccountsCashReceiptVoucher;
use App\Model\Accounts\AccountsJournalEntry;
use App\Model\Accounts\AccountsJournalEntryDetails;
use App\Model\CompanyInfo;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Accounts\AccountsChartofAccounts;
use DB;
use App\Model\Accounts\AccountsCashPaymentDetailsVoucher;
use Maatwebsite\Excel\Facades\Excel;

use App\Lib\Enumerations\AccountsTransactionType;

class CashBookReportController extends Controller
{

    public function index(Request $request)
    {
        if ($request->filled('export')) {

            $file_type = $request->export;

            $data = [];
            $data['ledgers'] = $this->getJournalsData($request);

            $data['coa_title'] = $request->coa_title;
            $data['from_date'] = $request->from_date;
            $data['end_date'] = $request->end_date;

            if ($file_type=='pdf') {

                $this->exportAsPdf($data);

            } elseif ($file_type=='excel') {

                $coa = str_replace(' ', '_', $request->coa_title); // Replaces all spaces with hyphens.
                $coa = preg_replace('/[^A-Za-z0-9\-]/', '_', $coa); // Removes special chars.

                return Excel::download(new CashBookExport( $data['ledgers'] ), "Cash_Book_$coa.xlsx");

            } else {
                dd('Something went wrong.');
            }
        }

        if($request->ajax()){

            return $this->getJournalsData($request);
        }

        $chartofAccountList = AccountsChartofAccounts::where('status', '1')->where('chart_of_account_code', 'like', '3208%')->get();

        return view('accounts.reports.cash_book_report',compact('chartofAccountList'));
    }

    public function getJournalsData(Request $request){

            $account_id ='';

            $from_date = dateConvertFormtoDB('01-01-1970');
            $end_date = dateConvertFormtoDB(date('Y-m-d')) ;

            $data = [];

            if ($request->filled('account_id'))
            {
                $account_id = $request->account_id;
            }

            if ($request->filled('from_date'))
            {
                $from_date = $request->from_date;
            }

            if ($request->filled('end_date'))
            {
                $end_date = $request->end_date;
            }

            if($account_id != '')
            {

                $coaData = AccountsChartofAccounts::findOrFail($account_id);
                $Open_balance = $coaData->opening_balance;
                $balance = $Open_balance;
                $acc_code = $coaData->chart_of_account_code;
                $acc_title = $coaData->chart_of_accounts_title;

                $jrnlIDs = AccountsJournalEntryDetails::where('char_of_account_id',$account_id)
                                    ->whereHas('get_jrnl_entry', function ($query) use ($from_date,$end_date) {
                                        $query->where('approve_status',1);
                                        $query->where('is_flash',0);
                                        $query->whereBetween('transaction_date', [$from_date, $end_date]);
                                    })
                                    ->groupBy('journal_entry_id')
                                    ->pluck('journal_entry_id');

                //Get Transactional Opening Balance
                $chart_of_account_code = $acc_code;

                $acc_head_code = substr($chart_of_account_code,0,1);
                $before_that_date = date('Y-m-d',strtotime("$from_date -1days"));

                $dr_cr_sum =AccountsJournalEntryDetails::where('char_of_account_id',$account_id)
                                ->whereHas('get_jrnl_entry', function ($query) use ($before_that_date) {
                                    $query->where('approve_status',1);
                                    $query->where('is_flash',0);
                                    $query->whereDate('transaction_date', '<=', $before_that_date);
                                })
                                ->selectRaw('sum(debit_amount) as debit_amount,sum(credit_amount) as credit_amount')
                                ->first();

                            if($acc_head_code == 1 || $acc_head_code == 4 || $acc_head_code == 5){
                                $transactionBalance = $Open_balance - ($dr_cr_sum->credit_amount - $dr_cr_sum->debit_amount);
                            }
                            if($acc_head_code == 2 || $acc_head_code == 3){
                                $transactionBalance = $Open_balance + ($dr_cr_sum->debit_amount - $dr_cr_sum->credit_amount );
                            }

                //Opening Row Data #01
                $data[] = [
                    'chart_of_account_code'   => $acc_code,
                    'chart_of_accounts_title' => $acc_title,
                    'particular'              => 'Opening',
                    'party'            => '',
                    'debit'                   => $dr_cr_sum->debit_amount,
                    'credit'                  => $dr_cr_sum->credit_amount,
                    'balance'                 => $transactionBalance,
                ];

                $jrnlEntryRows = AccountsJournalEntry::
                                        with(array('details' => function($query) use ($account_id) {
                                            $query->where('char_of_account_id','!=',$account_id);
                                        }))
                                        ->selectRaw('id,transaction_reference_id,transaction_reference_no,transaction_date,transaction_type,transaction_type_name,total_debit,total_credit,narration')
                                        ->orderBy('transaction_date','asc')
                                        //->where('transaction_reference_no','CP-202101625')
                                        ->Find($jrnlIDs);

                foreach ($jrnlEntryRows as $jrnlRow)
                {

                    $eachRowDebitSum = (float) 0;
                    $eachRowCreditSum = (float) 0;

                    $total_dr = 0;
                    $total_cr = 0;

                        $getVoucherNo = $jrnlRow->transaction_reference_no;
                        //AccountsTransactionType::$purchaseInvVoucer == 10
                        if ($jrnlRow->transaction_type == AccountsTransactionType::$purchaseInvVoucer) {
                            if ($jrnlRow->getPurchaseInvVoucer) {
                                $getVoucherNo = $jrnlRow->getPurchaseInvVoucer->vouchers_no;
                            }
                        }

                    //Voucher Row # table head - TransactionRefNo > JournalRow
                    $data[] = [
                        'chart_of_account_code'   => dateFormat($jrnlRow->transaction_date,'d-M-Y'),
                        'chart_of_accounts_title' => '<strong title="accounts_journal_entries transaction_reference_no='.$jrnlRow->transaction_reference_no.'">Voucher NO :</strong> '.$getVoucherNo,
                        'particular'              => '<strong>'.$jrnlRow->transaction_type_name.'</strong>',
                        'party'            => '',
                        'debit'                   => '',
                        'credit'                  => '',
                        'balance'                 => '',
                    ];

                    //Journal Details # table body

                    foreach ($jrnlRow->details as $item)
                    {
                        $detailsRemarks = AccountsJournalEntryDetails::where('journal_entry_id',$jrnlRow->id)->get();

                        $multiRemarks = '';
                        foreach ($detailsRemarks as $detailsRemark) {
                            $multiRemarks .= $detailsRemark->remarks.'<b>,</b> ';
                        }

                        // @ REMARKS check and copy remark if not pushed to journal_details tbl
                        if (is_null($item->remarks) || $item->remarks == '') {
                            if ($jrnlRow->transaction_type == AccountsTransactionType::$cashPayment) {
                                $getRemark = DB::table('accounts_cash_payment_details_vouchers')
                                                ->where('cash_payment_id',$jrnlRow->transaction_reference_id)
                                                //->where('debit_account_id',$item->char_of_account_id)
                                                ->First();
                                if ($getRemark) {
                                    $item->remarks = $getRemark->remarks;
                                    $item->save();
                                }
                            }
                            if ($jrnlRow->transaction_type == AccountsTransactionType::$cashReceipt) {
                                $getRemark = DB::table('accounts_cash_receipt_details_vouchers')
                                                ->where('cash_receipt_id',$jrnlRow->transaction_reference_id)
                                                //->where('credit_account_id',$item->char_of_account_id)
                                                ->First();
                                if ($getRemark) {
                                    $item->remarks = $getRemark->remarks;
                                    $item->save();
                                }
                            }
                            if ($jrnlRow->transaction_type == AccountsTransactionType::$bankPayment) {
                                $getRemark = DB::table('accounts_bank_payment_vouchers')
                                    ->where('id',$jrnlRow->transaction_reference_id)
                                    ->First();
                                if ($getRemark) {
                                    $item->remarks = $getRemark->narration;
                                    $item->save();
                                }
                            }
                            if ($jrnlRow->transaction_type == AccountsTransactionType::$purchaseInvVoucer) {
                                $getRemark = DB::table('accounts_purchase_order_vouchers')
                                    ->where('id',$jrnlRow->transaction_reference_id)
                                    ->First();
                                if ($getRemark) {
                                    $item->remarks = $getRemark->narration;
                                    $item->save();
                                }
                            }
                        } // # END REMARKS

                        //Code has been modified because of multiple journal under same chart of acc at 10-4-21

                            if ($jrnlRow->transaction_type == AccountsTransactionType::$cashPayment) {

                                    $transactionBalance = $transactionBalance - $item->debit_amount;
                                    $transactionBalance = $transactionBalance + $item->credit_amount;

                                    $data[] = [
                                        'chart_of_account_code'   => $item->coa ? $item->coa->chart_of_account_code : '',
                                        'chart_of_accounts_title' => $item->coa ? $item->coa->chart_of_accounts_title : '',
                                        'particular'              => $item->remarks,
                                        'party'                   => '',
                                        'debit'                   => $item->credit_amount,
                                        'credit'                  => $item->debit_amount,
                                        'balance'                 => $transactionBalance,
                                    ];

                            }

                            if ($jrnlRow->transaction_type == AccountsTransactionType::$cashReceipt) {

                                    $transactionBalance = $transactionBalance + $item->credit_amount;
                                    $transactionBalance = $transactionBalance - $item->debit_amount;

                                    $data[] = [
                                        'chart_of_account_code'   => $item->coa->sub_code2 ? $item->coa->sub_code2->sub_code2 : '',
                                        'chart_of_accounts_title' => $item->coa->sub_code2 ? $item->coa->sub_code2->sub_code_title2 : '',
                                        'particular'              => $item->remarks,
                                        'party'                   => '',
                                        'debit'                   => $item->credit_amount,
                                        'credit'                  => $item->debit_amount,
                                        'balance'                 => $transactionBalance,
                                    ];


                            }

                            if ($jrnlRow->transaction_type == AccountsTransactionType::$bankPayment) {

                                $transactionBalance = $transactionBalance + $item->credit_amount;
                                $transactionBalance = $transactionBalance - $item->debit_amount;

                                $data[] = [
                                    'chart_of_account_code'   => $item->coa->sub_code2 ? $item->coa->sub_code2->sub_code2 : '',
                                    'chart_of_accounts_title' => $item->coa->sub_code2 ? $item->coa->sub_code2->sub_code_title2 : '',
                                    'particular'              => $item->remarks,
                                    'party'            => '',
                                    'debit'                   => $item->credit_amount,
                                    'credit'                  => $item->debit_amount,
                                    'balance'                 => $transactionBalance,
                                ];
                            }

                            if ($jrnlRow->transaction_type == AccountsTransactionType::$purchaseInvVoucer) {

                                $transactionBalance = $transactionBalance - $item->debit_amount;
                                $transactionBalance = $transactionBalance + $item->credit_amount;

                                $data[] = [
                                    'chart_of_account_code'   => $item->coa ? $item->coa->chart_of_account_code : '',
                                    'chart_of_accounts_title' => $item->coa ? $item->coa->chart_of_accounts_title : '',
                                    'particular'              => $item->remarks,
                                    'party'                   => '',
                                    'debit'                   => $item->credit_amount,
                                    'credit'                  => $item->debit_amount,
                                    'balance'                 => $transactionBalance,
                                ];
                            }

                            //JV
                            if ($jrnlRow->transaction_type == AccountsTransactionType::$journal) {
                                $coa_cr_amount = '';
                                $coa_dr_amount = '';

                                    $coa_dr_amount = $item->debit_amount;
                                    $coa_cr_amount = $item->credit_amount; ////Reverse
                                    $transactionBalance = $transactionBalance + $item->credit_amount;
                                    $transactionBalance = $transactionBalance - $item->debit_amount;


                                $data[] = [
                                    'chart_of_account_code'   => $item->coa ? $item->coa->chart_of_account_code : '',
                                    'chart_of_accounts_title' => $item->coa ? $item->coa->chart_of_accounts_title : '',
                                    'particular'              => $item->remarks,
                                    'party'                   => '',
                                    'debit'                   => $coa_cr_amount,
                                    'credit'                  => $coa_dr_amount,
                                    'balance'                 => $transactionBalance,
                                ];
                            }

                            //new sum

                            $eachRowDebitSum = $eachRowDebitSum + $item->debit_amount;

                            $eachRowCreditSum = $eachRowCreditSum + $item->credit_amount;

                    } // # END JournalDetails foreach

                    //Get Sub Total > JournalRow SubFooter
                    if ($jrnlRow->transaction_type == AccountsTransactionType::$cashPayment) {
                        $theDebit = '';
                        $theCredit = '';

                            $theCredit = '<strong>'.$jrnlRow->total_credit.'</strong>';

                            $theParty = '<strong title="accounts_journal_entries id='.$jrnlRow->id.'">Total of' . ' (' . $jrnlRow->transaction_reference_no . ')</strong>';
                    } else {
                        $theDebit = '';
                        $theCredit = '';

                            $theDebit = '<strong>'.$jrnlRow->total_debit.'</strong>';

                            $theParty = '<strong title="accounts_journal_entries id='.$jrnlRow->id.'"> Total of' . ' (' . $getVoucherNo . ') </strong>';
                    }

                    $data[] = [
                        'chart_of_account_code'   => '',
                        'chart_of_accounts_title' => '',
                        'particular'              => '',
                        'party'                   => $theParty,
                        'debit'                   => $theDebit,
                        'credit'                  => $theCredit,
                        'balance'                 => '',
                    ];
                }

                //Closing Balance
                $data[] = [
                    'chart_of_account_code'   => '',
                    'chart_of_accounts_title' => $acc_title,
                    'particular'              => 'Closing',
                    'party'                   => '',
                    'debit'                   => '',
                    'credit'                  => '',
                    'balance'                 => $transactionBalance,
                ];

                return $data;
        }
    }

    public function exportAsPdf($data)
    {
        $company = CompanyInfo::Find(1);

        $data = array(
            'company' => $company,
            'ledgers' => $data['ledgers'],
            'coa_title' => $data['coa_title'],
            'from_date' => $data['from_date'],
            'end_date' => $data['end_date'],
        );

        ini_set("pcre.backtrack_limit", "5000000");

        $html = \View::make('accounts.reports.report_pdf.cash_bookPDF')->with($data);

        $mpdf = new \Mpdf\Mpdf([
            'margin_left' => 20,
            'margin_right' => 15,
            'margin_top' => 48,
            'margin_bottom' => 25,
            'margin_header' => 10,
            'margin_footer' => 10,
            'orientation' => 'L'
        ]);

        $title = 'Cash Book';
        $author = 'iVenture';
        if ($company) {
            $title .= ' - '.$company->name;
            $author = $company->name;
        }

        $mpdf->SetProtection(array('print'));
        $mpdf->SetTitle($title);
        $mpdf->SetAuthor($author);
        $mpdf->SetWatermarkText("Cash Book");
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
