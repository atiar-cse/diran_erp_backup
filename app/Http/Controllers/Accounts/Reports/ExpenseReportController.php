<?php

namespace App\Http\Controllers\Accounts\Reports;

use App\Exports\Accounts\ExpenseStatementsExport;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Model\Accounts\AccountsMainCode;
use App\Model\Accounts\AccountsSubCode;
use App\Model\Accounts\AccountsSubCode2;
use App\Model\Accounts\AccountsChartofAccounts;

use App\Model\Accounts\AccountsJournalEntry;
use App\Model\Accounts\AccountsJournalEntryDetails;
use Maatwebsite\Excel\Facades\Excel;

class ExpenseReportController extends Controller
{
    public function index(Request $request)
    {

        if ($request->filled('export')) {

            $file_type = $request->export;

            $data = [];
            $data['ledgers'] = $this->getJournalsData($request);

            $data['from_date'] = $request->from_date;
            $data['end_date'] = $request->end_date;

            if ($file_type=='pdf') {

                $this->exportAsPdf($data);

            } elseif ($file_type=='excel') {

                return Excel::download(new ExpenseStatementsExport( $data['ledgers'] ), 'Expense Statements.xlsx');

            } else {
                dd('Something went wrong.');
            }
        }


        if ($request->ajax()) {

            return $this->getJournalsData($request);
        }


        $chartofAccountList  = AccountsChartofAccounts::where('chart_of_account_code','like','2%')->where('status', '1')->get();
        return view('accounts.reports.expense_statments',compact('chartofAccountList'));
    }


    public function getJournalsData(Request $request){

            $account_id ='';
            $from_date = '';
            $end_date = '';

            if ($request->filled('account_id')) {
                $account_id = $request->account_id;
            }

            if ($request->filled('from_date')) {
                $from_date = $request->from_date;
            }
            if ($request->filled('end_date')) {
                $end_date = $request->end_date;
            }

            try {

                if($account_id!=''){

                    $coa = AccountsChartofAccounts::where('id',$account_id)->get();

                    $data = [];
                    foreach ($coa as $accountsDtls) {

                        $jrnlSum = AccountsJournalEntryDetails::where('char_of_account_id',$account_id)
                            ->whereHas('get_jrnl_entry', function ($query) use ($from_date,$end_date) {
                                $query->where('approve_status',1);
                                $query->whereBetween('transaction_date', [$from_date, $end_date]);
                            })
                            ->selectRaw('journal_entry_id,sum(debit_amount) as debit_amount')
                            ->First();

                        $debit_amount = $jrnlSum->debit_amount ? $jrnlSum->debit_amount : 0;

                        $data[] = [
                            'sub_code_title2' => $accountsDtls->chart_of_accounts_title,
                            'sub_code2'       => $accountsDtls->chart_of_account_code,
                            'debit_amount'    => $debit_amount,
                        ];
                    }
                    return $data;


                }else{

                    $main_code = AccountsMainCode::where('main_code',2)->First();
                    $subCodeIDs = AccountsSubCode::where('main_code_id',$main_code->id)->pluck('id');
                    $subCodes2 = AccountsSubCode2::whereIn('sub_code_id',$subCodeIDs)->get();

                    $data = [];
                    foreach ($subCodes2 as $sub_code) {

                        $chartOfAccIDs = AccountsChartofAccounts::where('sub_code2_id',$sub_code->id)->pluck('id');

                        $jrnlSum = AccountsJournalEntryDetails::whereIn('char_of_account_id',$chartOfAccIDs)
                            ->whereHas('get_jrnl_entry', function ($query) use ($from_date,$end_date) {
                                $query->where('approve_status',1);
                                $query->whereBetween('transaction_date', [$from_date, $end_date]);
                            })
                            ->selectRaw('journal_entry_id,sum(debit_amount) as debit_amount')
                            ->First();
                        $debit_amount = $jrnlSum->debit_amount ? $jrnlSum->debit_amount : 0;
                        $data[] = [
                            'sub_code_title2' => $sub_code->sub_code_title2,
                            'sub_code2' => $sub_code->sub_code2,
                            'debit_amount' => $debit_amount,
                        ];
                    }

                    return $data;

                }
            }
            catch (\Exception $e) {
                $mm = $e->getMessage();
                return $mm;
            }
        }


    public function exportAsPdf($data){

        $company = \App\Model\CompanyInfo::Find(1);

        $data = array(
            'company'   => $company,
            'ledgers'   => $data['ledgers'],
            'from_date' => $data['from_date'],
            'end_date'  => $data['end_date'],
        );
        $html = \View::make('accounts.reports.report_pdf.expense_statementPDF')->with($data);

        $mpdf = new \Mpdf\Mpdf([
            'margin_left' => 20,
            'margin_right' => 15,
            'margin_top' => 48,
            'margin_bottom' => 25,
            'margin_header' => 10,
            'margin_footer' => 10
        ]);

        $title = 'Expense Statements';
        $author = 'iVenture';
        if ($company) {
            $title .= ' - '.$company->name;
            $author = $company->name;
        }

        $mpdf->SetProtection(array('print'));
        $mpdf->SetTitle($title);
        $mpdf->SetAuthor($author);
        $mpdf->SetWatermarkText("Expense Statements");
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
