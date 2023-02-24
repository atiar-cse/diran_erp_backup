<?php

namespace App\Http\Controllers\Accounts\Reports;

use App\Exports\Accounts\BankReconciliationExport;
use App\Model\CompanyInfo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Accounts\AccountsChartofAccounts;
use DB;
use Maatwebsite\Excel\Facades\Excel;

class BankReconciliationReportController extends Controller
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

                return Excel::download(new BankReconciliationExport( $data['ledgers'] ), "Bank_Reconcilation_$coa.xlsx");

            } else {
                dd('Something went wrong.');
            }
        }

        if($request->ajax()){

            return $this->getJournalsData($request);
        }

        $chartofAccountList  = AccountsChartofAccounts::where('status', '1')->where('chart_of_account_code','like', '3207%')->get();
        return view('accounts.reports.bank_reconcilation_report',compact('chartofAccountList'));
        //return view('accounts.reports.balance_sheet');
    }

    public function getJournalsData(Request $request){

            $where='';
            $zero= '0.00';
            $account_id= $request->account_id;
            $from_date2 = $request->from_date;
            $end_date2  = $request->end_date;
            if($from_date2!=''){
                $from_date=date('Y-m-d', strtotime(str_replace('/', '-',$from_date2)));
            }else{$from_date='';}

            if($end_date2!=''){
                $end_date=date('Y-m-d', strtotime(str_replace('/', '-',$end_date2)));
            }else{$end_date='';}

            if($account_id!=''){$where.=" and dtails.char_of_account_id = $account_id ";}

            if($from_date!=''){$where.=" and journal.transaction_date>='$from_date' ";}
            if($end_date!=''){$where.=" and journal.transaction_date<='$end_date' ";}

            $ledger[] ='';

            $open_balance=AccountsChartofAccounts::where('id',$account_id)->first()->opening_balance;
            $ledger[0]= [
                'date'    => '',
                'type'    => '',
                'debit'   => '',
                'credit'  => '',
                'balance' => $open_balance,
            ];

            if($where !=''){
                $return = DB::table('accounts_journal_entry_details as dtails')
                    ->leftJoin('accounts_journal_entries as journal', 'dtails.journal_entry_id','=','journal.id')
                    ->leftJoin('accounts_chartof_accounts as chartof', 'dtails.char_of_account_id','=','chartof.id')
                    ->select('dtails.*',
                        'journal.transaction_date','journal.transaction_type_name','journal.total_credit',
                        'chartof.chart_of_accounts_title','chartof.chart_of_account_code')
                    ->orderBy('journal.transaction_date','ASC')
                    ->whereRaw('journal.approve_status=1  '.$where)
                    ->get();
                foreach ($return as $key => $value) {
                   /* if($value->debit_amount!= '' || (int)$value->debit_amount!= $zero){
                        $open_balance = $open_balance + $value->debit_amount;
                    }*/
                    if($value->debit_amount == $zero){
                        $open_balance = $open_balance - $value->credit_amount;
                    }else{
                        $open_balance = $open_balance + $value->debit_amount;
                    }

                    /*if($value->credit_amount!= '' || $value->credit_amount!= '0.00'){
                        $open_balance = $open_balance - $value->credit_amount;
                    }*/

                    $ledger[] = [
                        'date'    => $value->transaction_date,
                        'type'    => $value->transaction_type_name,
                        'debit'   => $value->debit_amount,
                        'credit'  => $value->credit_amount,
                        'balance' => number_format($open_balance,2),
                    ];
                }

                return $ledger;
           // }

        }
    }


    public function exportAsPdf($data)
    {
        $company =CompanyInfo::Find(1);

        $data = array(
            'company' => $company,
            'ledgers' => $data['ledgers'],
            'coa_title' => $data['coa_title'],
            'from_date' => $data['from_date'],
            'end_date' => $data['end_date'],
        );
        $html = \View::make('accounts.reports.report_pdf.bank_reconciliationPDF')->with($data);

        $mpdf = new \Mpdf\Mpdf([
            'margin_left' => 20,
            'margin_right' => 15,
            'margin_top' => 48,
            'margin_bottom' => 25,
            'margin_header' => 10,
            'margin_footer' => 10
        ]);

        $title = 'Bank Reconcilation';
        $author = 'iVenture';
        if ($company) {
            $title .= ' - '.$company->name;
            $author = $company->name;
        }

        $mpdf->SetProtection(array('print'));
        $mpdf->SetTitle($title);
        $mpdf->SetAuthor($author);
        $mpdf->SetWatermarkText("Bank Reconcilation");
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
