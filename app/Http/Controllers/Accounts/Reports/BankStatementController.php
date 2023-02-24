<?php

namespace App\Http\Controllers\Accounts\Reports;

use App\Exports\Accounts\BankStatementExport;
use App\Model\Accounts\AccountsJournalEntry;
use App\Model\CompanyInfo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Model\Accounts\AccountsChartofAccounts;
use App\Model\Accounts\AccountsJournalEntryDetails;
use Maatwebsite\Excel\Facades\Excel;

class BankStatementController extends Controller
{

    public function index(Request $request)
    {
        if ($request->filled('export')) {

            $file_type = $request->export;

            $data = [];
            $data['ledgers'] = $this->getJournalsData($request);

            $data['coa_id']    = $request->coa_id;
            $data['from_date'] = $request->from_date;
            $data['end_date']  = $request->end_date;

            if ($file_type=='pdf') {

                $this->exportAsPdf($data);

            } elseif ($file_type=='excel') {

                return Excel::download(new BankStatementExport( $data['ledgers'] ), 'Bank Statements.xlsx');

            } else {
                dd('Something went wrong.');
            }
        }

        if ($request->ajax()) {

            return $this->getJournalsData($request)->toJson();
        }


        $bank_coa_list = AccountsChartofAccounts::where('status', '1')
                                        ->where('chart_of_account_code','like','3207%')
                                        ->get();

        return view('accounts.reports.bank_statments',compact('bank_coa_list'));
    }


    public function getJournalsData(Request $request){
        $from_date = '';
        $end_date = '';

        if ($request->filled('from_date')) {
            $from_date = $request->from_date;
        }
        if ($request->filled('end_date')) {
            $end_date = $request->end_date;
        }

        try {

            if ($request->filled('coa_id')) {

                $coa_id = $request->coa_id;


                $journal_entry_ids = AccountsJournalEntryDetails::where('char_of_account_id',$coa_id)
                    ->whereHas('get_jrnl_entry', function ($query) use ($from_date,$end_date) {
                        $query->where('approve_status',1);
                        $query->whereBetween('transaction_date', [$from_date, $end_date]);
                        $query->where('transaction_type_name','like','%Bank%');
                    })
                    ->groupBy('journal_entry_id')
                    ->pluck('journal_entry_id');


                $results = AccountsJournalEntry::with('details')
                    ->select('id','transaction_reference_no','transaction_type_name','transaction_date')
                    ->orderBy('transaction_date','asc')
                    ->Find($journal_entry_ids);
                    //->toJson();
                //dd($results);

                return $results;

            }

            $results = AccountsJournalEntry::with('details')
                ->where('approve_status',1)
                ->whereBetween('transaction_date', [$from_date, $end_date])
                ->where('transaction_type_name','like','%Bank%')
                ->select('id','transaction_reference_no','transaction_type_name','transaction_date')
                ->orderBy('transaction_date','asc')
                ->get();
                //->toJson();

            return $results;
        }
        catch (\Exception $e) {
            $mm = $e->getMessage();
            //print_r($mm);
            return $mm;
        }
    }

    public function exportAsPdf($data)
    {
        $company = CompanyInfo::Find(1);

        $data = array(
            'company'   => $company,
            'ledgers'   => $data['ledgers'],
            'coa_id'    => $data['coa_id'],
            'from_date' => $data['from_date'],
            'end_date'  => $data['end_date'],
        );
        $html = \View::make('accounts.reports.report_pdf.bank_statementPDF')->with($data);

        $mpdf = new \Mpdf\Mpdf([
            'margin_left'   => 20,
            'margin_right'  => 15,
            'margin_top'    => 48,
            'margin_bottom' => 25,
            'margin_header' => 10,
            'margin_footer' => 10
        ]);

        $title = 'Bank Statements';
        $author = 'iVenture';
        if ($company) {
            $title .= ' - '.$company->name;
            $author = $company->name;
        }

        $mpdf->SetProtection(array('print'));
        $mpdf->SetTitle($title);
        $mpdf->SetAuthor($author);
        $mpdf->SetWatermarkText("Bank Statements");
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
