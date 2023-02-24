<?php

namespace App\Http\Controllers\Accounts\Reports;

use App\Exports\Accounts\BalanceSheetExport;
use App\Model\CompanyInfo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Model\Accounts\AccountsMainCode;
use App\Model\Accounts\AccountsSubCode;
use App\Model\Accounts\AccountsSubCode2;
use App\Model\Accounts\AccountsChartofAccounts;

use App\Model\Accounts\AccountsJournalEntry;
use App\Model\Accounts\AccountsJournalEntryDetails;
use Maatwebsite\Excel\Facades\Excel;

class BalanceSheetController extends Controller
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

                return Excel::download(new BalanceSheetExport( $data['ledgers'] ), 'Balance Sheet.xlsx');

            } else {
                dd('Something went wrong.');
            }
        }

        if ($request->ajax()) {

            return $this->getJournalsData($request);
        }

        return view('accounts.reports.balance_sheet');


    }

    public function getJournalsData(Request $request){
        //if ($request->ajax()) {

            $from_date = '';
            $end_date = '';

            if ($request->filled('from_date')) {
                $from_date = $request->from_date;
            }
            if ($request->filled('end_date')) {
                $end_date = $request->end_date;
            }

            try {

                //PROPERTY & ASSETS
                $main_code = AccountsMainCode::where('main_code',3)->First();
                $subCodes = AccountsSubCode::with('get_sub_code2_rows')->where('main_code_id',$main_code->id)->get();

                $dataPropertyAssets = [];
                foreach ($subCodes as $sub_code) {

                    $subCode2IDs = AccountsSubCode2::where('sub_code_id',$sub_code->id)->pluck('id');

                    $chartOfAccIDs = AccountsChartofAccounts::whereIn('sub_code2_id',$subCode2IDs)->where('status',1)->pluck('id');

                    $coaOpeningBalance = AccountsChartofAccounts::selectRaw('sum(opening_balance) as opening_balance ')->whereIn('sub_code2_id',$subCode2IDs)->first();

                    $jrnlSum = AccountsJournalEntryDetails::whereIn('char_of_account_id',$chartOfAccIDs)
                        ->whereHas('get_jrnl_entry', function ($query) use ($from_date,$end_date) {
                            $query->where('approve_status',1);
                            $query->whereBetween('transaction_date', [$from_date, $end_date]);
                        })
                        ->selectRaw('journal_entry_id,sum(debit_amount) as debit_amount,sum(credit_amount) as credit_amount,(debit_amount-credit_amount) as balance')
                        ->First();

                        $balance = $coaOpeningBalance->opening_balance ?  $coaOpeningBalance->opening_balance : 0;
                        $balance = $jrnlSum->balance ? $balance + $jrnlSum->balance: $balance;



                    $dataPropertyAssets[] = [
                        'sub_code_title' => $sub_code->sub_code_title,
                        'sub_code'       => $sub_code->sub_code,
                        'balance'        => $balance,
                    ];
                }

                // LIABILITIES
                $main_code = AccountsMainCode::where('main_code',4)->First();
                $subCodes = AccountsSubCode::with('get_sub_code2_rows')->where('main_code_id',$main_code->id)->get();

                $dataLiabilities = [];
                foreach ($subCodes as $sub_code) {

                    $subCode2IDs = AccountsSubCode2::where('sub_code_id',$sub_code->id)->pluck('id');
                    $chartOfAccIDs = AccountsChartofAccounts::whereIn('sub_code2_id',$subCode2IDs)->where('status',1)->pluck('id');

                    $coaOpeningBalance = AccountsChartofAccounts::selectRaw('sum(opening_balance) as opening_balance ')->whereIn('sub_code2_id',$subCode2IDs)->first();

                    $jrnlSum = AccountsJournalEntryDetails::whereIn('char_of_account_id',$chartOfAccIDs)
                        ->whereHas('get_jrnl_entry', function ($query) use ($from_date,$end_date) {
                            $query->where('approve_status',1);
                            $query->whereBetween('transaction_date', [$from_date, $end_date]);
                        })
                        ->selectRaw('journal_entry_id,sum(debit_amount) as debit_amount,sum(credit_amount) as credit_amount,(credit_amount-debit_amount) as balance')
                        ->First();

                    $balance = $coaOpeningBalance->opening_balance ?  $coaOpeningBalance->opening_balance : 0;
                    $balance = $jrnlSum->balance ? $balance + $jrnlSum->balance: $balance;

                    $dataLiabilities[] = [
                        'sub_code_title' => $sub_code->sub_code_title,
                        'sub_code' => $sub_code->sub_code,
                        'balance' => $balance,
                    ];
                }

                $data = [
                    'assets' => $dataPropertyAssets,
                    'liabilities' => $dataLiabilities,
                ];
                return $data;
            }
            catch (\Exception $e) {
                $msg = $e->getMessage();
                return $msg;
            }
        //}
    }


    public function exportAsPdf($data)
    {
        $company =CompanyInfo::Find(1);

        $data = array(
            'company' => $company,
            'ledgers' => $data['ledgers'],
            'from_date' => $data['from_date'],
            'end_date' => $data['end_date'],
        );
        $html = \View::make('accounts.reports.report_pdf.balance_sheetPDF')->with($data);

        $mpdf = new \Mpdf\Mpdf([
            'margin_left' => 20,
            'margin_right' => 15,
            'margin_top' => 48,
            'margin_bottom' => 25,
            'margin_header' => 10,
            'margin_footer' => 10
        ]);

        $title = 'Balance Sheet';
        $author = 'iVenture';
        if ($company) {
            $title .= ' - '.$company->name;
            $author = $company->name;
        }

        $mpdf->SetProtection(array('print'));
        $mpdf->SetTitle($title);
        $mpdf->SetAuthor($author);
        $mpdf->SetWatermarkText("Balance Sheet");
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
