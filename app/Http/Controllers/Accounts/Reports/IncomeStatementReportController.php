<?php

namespace App\Http\Controllers\Accounts\Reports;

use App\Exports\Accounts\IncomeStatementsExport;
use App\Http\Controllers\Controller;
use App\Model\Accounts\AccountsChartofAccounts;
use App\Model\Accounts\AccountsJournalEntryDetails;
use App\Model\Accounts\AccountsMainCode;
use App\Model\Accounts\AccountsSubCode;
use App\Model\Accounts\AccountsSubCode2;
use App\Model\CompanyInfo;
use Exception;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Mpdf\Mpdf;
use View;

class IncomeStatementReportController extends Controller
{

    public function index(Request $request)
    {
        if ($request->filled('export')) {

            $file_type = $request->export;

            $data = [];
            $data['ledgers'] = $this->getJournalsData($request);

            $data['from_date'] = $request->from_date;
            $data['end_date'] = $request->end_date;

            if ($file_type == 'pdf') {
                $this->exportAsPdf($data);
            } elseif ($file_type == 'excel') {
                return Excel::download(new IncomeStatementsExport($data['ledgers']), 'Income Statements.xlsx');
            } else {
                dd('Something went wrong.');
            }
        }

        if ($request->ajax()) {
            return $this->getJournalsData($request);
        }

        $chartofAccountList = AccountsChartofAccounts::where('chart_of_account_code', 'like', '11%')->where('status', 1)->get();

        return view('accounts.reports.income_statements', compact('chartofAccountList'));
    }


    public function getJournalsData(Request $request)
    {
        $account_id = '';
        $from_date = '';
        $end_date = '';

        if ($request->filled('account_id')) {
            $account_id = $request->account_id;
        }

        if ($request->filled('from_date')) {
            $from_date = dateConvertFormtoDB($request->from_date);
        }
        if ($request->filled('end_date')) {
            $end_date = dateConvertFormtoDB($request->end_date);
        }

        try {
            if ($account_id != '') {

                $coa = AccountsChartofAccounts::where('id', $account_id)->get();

                $data = [];
                foreach ($coa as $accountsDtls) {
                    $jrnlSum = AccountsJournalEntryDetails::where('char_of_account_id', $account_id)
                        ->whereHas('get_jrnl_entry', function ($query) use ($from_date, $end_date) {
                            $query->where('approve_status', 1);
                            if ($from_date !== '') {
                                $query->where('transaction_date', '>=', $from_date);
                            }
                            if ($end_date !== '') {
                                $query->where('transaction_date', '<=', $end_date);
                            }
                            //$query->whereBetween('/**/transaction_date', [$from_date, $end_date]);
                        })
                        ->selectRaw('journal_entry_id,sum(credit_amount) as credit_amount')
                        ->First();
                    $credit_amount = $jrnlSum->credit_amount ? $jrnlSum->credit_amount : 0;
                    $data[] = [
                        'sub_code_title2' => $accountsDtls->chart_of_accounts_title,
                        'sub_code2' => $accountsDtls->chart_of_account_code,
                        'credit_amount' => $credit_amount,
                    ];
                }
                return $data;
            } else {
                $main_code = AccountsMainCode::where('main_code', 1)->First();
                $subCodeIDs = AccountsSubCode::where('main_code_id', $main_code->id)->pluck('id');
                $subCodes2 = AccountsSubCode2::whereIn('sub_code_id', $subCodeIDs)->get();

                $data = [];
                foreach ($subCodes2 as $sub_code) {
                    $chartOfAccIDs = AccountsChartofAccounts::where('sub_code2_id', $sub_code->id)->pluck('id');

                    $jrnlSum = AccountsJournalEntryDetails::whereIn('char_of_account_id', $chartOfAccIDs)
                        ->whereHas('get_jrnl_entry', function ($query) use ($from_date, $end_date) {
                            $query->where('approve_status', 1);
                            if ($from_date !== '') {
                                $query->where('transaction_date', '>=', $from_date);
                            }
                            if ($end_date !== '') {
                                $query->where('transaction_date', '<=', $end_date);
                            }
//                            $query->whereBetween('transaction_date', [$from_date, $end_date]);
                        })
                        ->selectRaw('journal_entry_id,sum(credit_amount) as credit_amount')
                        ->First();
                    $credit_amount = $jrnlSum->credit_amount ? $jrnlSum->credit_amount : 0;
                    $data[] = [
                        'sub_code_title2' => $sub_code->sub_code_title2,
                        'sub_code2' => $sub_code->sub_code2,
                        'credit_amount' => $credit_amount,
                    ];
                }
                return $data;
            }
        } catch (Exception $e) {
            $mm = $e->getMessage();
            return $mm;
        }
    }

    public function exportAsPdf($data)
    {
        $company = CompanyInfo::Find(1);

        $data = array(
            'company' => $company,
            'ledgers' => $data['ledgers'],
            'from_date' => $data['from_date'],
            'end_date' => $data['end_date'],
        );
        $html = View::make('accounts.reports.report_pdf.income_statementPDF')->with($data);

        $mpdf = new Mpdf([
            'margin_left' => 20,
            'margin_right' => 15,
            'margin_top' => 48,
            'margin_bottom' => 25,
            'margin_header' => 10,
            'margin_footer' => 10
        ]);

        $title = 'Income Statements';
        $author = 'iVenture';
        if ($company) {
            $title .= ' - ' . $company->name;
            $author = $company->name;
        }

        $mpdf->SetProtection(array('print'));
        $mpdf->SetTitle($title);
        $mpdf->SetAuthor($author);
        $mpdf->SetWatermarkText("Income Statements");
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
