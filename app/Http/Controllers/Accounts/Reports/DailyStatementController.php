<?php

namespace App\Http\Controllers\Accounts\Reports;

use App\Exports\Accounts\DailyStatementExport;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Model\Accounts\AccountsJournalEntry;
use Maatwebsite\Excel\Facades\Excel;
use App\Model\CompanyInfo;

class DailyStatementController extends Controller
{

    public function index(Request $request)
    {
        if ($request->filled('export')) {

            $file_type = $request->export;

            $data = [];
            $data['ledgers'] = $this->getJournalsData($request);

            $data['from_date'] = $request->from_date;
            $data['end_date']  = $request->end_date;

            if ($file_type=='pdf') {

                $this->exportAsPdf($data);

            } elseif ($file_type=='excel') {

                return Excel::download(new DailyStatementExport( $data['ledgers'] ), 'Daily Statements.xlsx');

            } else {
                dd('Something went wrong.');
            }
        }

        if ($request->ajax()) {

            return $this->getJournalsData($request)->toJson();
        }

        return view('accounts.reports.daily_statments');
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

            $results = AccountsJournalEntry::with('details')
                ->where('approve_status',1)
                ->whereBetween('transaction_date', [$from_date, $end_date])
                ->select('id','transaction_reference_no','transaction_type_name','transaction_date')
                ->orderBy('transaction_date','asc')
                ->get();
                //->toJson();
            return $results;
        }
        catch (\Exception $e) {
            $mm = $e->getMessage();
            return $mm;
        }

    }

    public function exportAsPdf($data)
    {
        $company = CompanyInfo::Find(1);

        $data = array(
            'company'   => $company,
            'ledgers'   => $data['ledgers'],
            'from_date' => $data['from_date'],
            'end_date'  => $data['end_date'],
        );
        $html = \View::make('accounts.reports.report_pdf.daily_statementPDF')->with($data);

        $mpdf = new \Mpdf\Mpdf([
            'margin_left'   => 20,
            'margin_right'  => 15,
            'margin_top'    => 48,
            'margin_bottom' => 25,
            'margin_header' => 10,
            'margin_footer' => 10
        ]);

        $title = 'Daily Statements';
        $author = 'iVenture';
        if ($company) {
            $title .= ' - '.$company->name;
            $author = $company->name;
        }

        $mpdf->SetProtection(array('print'));
        $mpdf->SetTitle($title);
        $mpdf->SetAuthor($author);
        $mpdf->SetWatermarkText("Daily Statements");
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
