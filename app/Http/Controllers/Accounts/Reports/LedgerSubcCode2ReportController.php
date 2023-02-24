<?php

namespace App\Http\Controllers\Accounts\Reports;

use App\Exports\Accounts\LedgerSubCode2ReportExport;
use App\Http\Controllers\Controller;
use App\Model\Accounts\AccountsChartofAccounts;
use App\Model\Accounts\AccountsJournalEntryDetails;
use App\Model\Accounts\AccountsSubCode2;
use App\Model\CompanyInfo;
use DB;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Mpdf\Mpdf;
use View;

class LedgerSubcCode2ReportController extends Controller
{

    public function index(Request $request)
    {
        if ($request->filled('export')) {

            $file_type = $request->export;

            $data = [];
            $data['ledgers'] = $this->getLedgerSubCode2Report($request);
            $data['sub_code_title2'] = $request->sub_code_title2;
            $data['from_date'] = $request->from_date;
            $data['end_date'] = $request->end_date;

            if ($file_type == 'pdf') {
                $this->exportAsPdf($data);
            } elseif ($file_type == 'excel') {
                return Excel::download(new LedgerSubCode2ReportExport($data['ledgers']), 'Ledger sub code2 Reports.xlsx');
            } else {
                dd('Something went wrong.');
            }
        }

        if ($request->ajax()) {
            return $this->getLedgerSubCode2Report($request);
        }

        $subCode2List = AccountsSubCode2::where('status', '1')->get();
        return view('accounts.reports.ledger_sub_code2_report', compact('subCode2List'));
    }

    public function getLedgerSubCode2Report(Request $request)
    {
        $sub_code2_id = '';
        $from_date = '';
        $end_date = '';

        if ($request->filled('sub_code2_id')) {
            $sub_code2_id = $request->sub_code2_id;
        }
        if ($request->filled('from_date')) {
            $from_date = dateConvertFormtoDB($request->from_date);
        }
        if ($request->filled('end_date')) {
            $end_date = dateConvertFormtoDB($request->end_date);
        }

        $accountIDs = AccountsChartofAccounts::where('sub_code2_id', $sub_code2_id)->pluck('id');

        //$open_balance = AccountsChartofAccounts::where('id',$accountIDs)->first()->opening_balance;
        $balance = AccountsChartofAccounts::where('id', $accountIDs)->first()->opening_balance;

        $ledger = [];

        $jrnlSum = AccountsJournalEntryDetails::with('coa')->whereIn('char_of_account_id', $accountIDs)
            ->whereHas('get_jrnl_entry', function ($query) use ($from_date, $end_date) {
                $query->where('approve_status', 1);
                if ($from_date !== '') {
                    $query->where('transaction_date', '>=', $from_date);
                }
                if ($end_date !== '') {
                    $query->where('transaction_date', '<=', $end_date);
                }
//                $query->whereBetween('transaction_date', [$from_date, $end_date]);
            })
            ->selectRaw('journal_entry_id,sum(credit_amount) as credit_amount,sum(debit_amount) as debit_amount,char_of_account_id')
            ->groupBy('char_of_account_id')
            ->get();

        foreach ($jrnlSum as $accountsDtls) {
            $ledger[] = [
                'date' => dateConvertDBtoForm($accountsDtls->get_jrnl_entry->transaction_date),
                'chart_of_accounts_title' => $accountsDtls->coa->chart_of_accounts_title,
                'chart_of_account_code' => $accountsDtls->coa->chart_of_account_code,
                'opening_balance' => $accountsDtls->coa->opening_balance,
                'debit' => $accountsDtls->debit_amount,
                'credit' => $accountsDtls->credit_amount,
                'balance' => $accountsDtls->coa->opening_balance + ($accountsDtls->debit_amount - $accountsDtls->credit_amount),
            ];
        }

        return $ledger;
    }

    public function exportAsPdf($data)
    {
        $company = CompanyInfo::Find(1);

        $data = array(
            'company' => $company,
            'ledgers' => $data['ledgers'],
            'sub_code_title2' => $data['sub_code_title2'],
            'from_date' => $data['from_date'],
            'end_date' => $data['end_date'],
        );
        $html = View::make('accounts.reports.report_pdf.ledger_sub_code2_reportPDF')->with($data);

        $mpdf = new Mpdf([
            'margin_left' => 20,
            'margin_right' => 15,
            'margin_top' => 48,
            'margin_bottom' => 25,
            'margin_header' => 10,
            'margin_footer' => 10
        ]);

        $title = 'Ledger Sub Code2 Report';
        $author = 'iVenture';
        if ($company) {
            $title .= ' - ' . $company->name;
            $author = $company->name;
        }

        $mpdf->SetProtection(array('print'));
        $mpdf->SetTitle($title);
        $mpdf->SetAuthor($author);
        $mpdf->SetWatermarkText("Ledger Sub Code2 Report");
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
