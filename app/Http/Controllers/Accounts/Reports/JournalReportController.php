<?php

namespace App\Http\Controllers\Accounts\Reports;

use App\Exports\Accounts\JournalReportExport;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use DB;

class JournalReportController extends Controller
{

    public function index(Request $request)
    {
        if ($request->filled('export')) {

            $file_type = $request->export;

            $data = [];
            $data['journals'] = $this->journalFilterReport($request);

            $data['from_date'] = $request->from_date;
            $data['end_date']  = $request->end_date;

            if ($file_type=='pdf') {

                $this->exportAsPdf($data);

            }elseif ($file_type=='excel') {

                return Excel::download(new JournalReportExport( $data['journals'] ), 'Journal Reports.xlsx');

            } else {
                dd('Something went wrong.');
            }
        }

        if($request->ajax()){
            return $this->journalFilterReport($request);
        }

        return view('accounts.reports.journal_report');
    }


    public function journalFilterReport(Request $request){

            $return = '';
            $where='';
            $from_date2 = $request->from_date;
            $end_date2  = $request->end_date;

            if($from_date2!=''){
                $from_date=date('Y-m-d', strtotime(str_replace('/', '-',$from_date2)));
            }else{$from_date='';}

            if($end_date2!=''){
                $end_date=date('Y-m-d', strtotime(str_replace('/', '-',$end_date2)));
            }else{$end_date='';}

            if($from_date!=''){$where.=" and journal.transaction_date>='$from_date' ";}
            if($end_date!=''){$where.=" and journal.transaction_date<='$end_date' ";}

            if($where!=''){

                $return = DB::table('accounts_journal_entry_details as dtails')
                    ->leftJoin('accounts_journal_entries as journal', 'dtails.journal_entry_id','=','journal.id')
                    ->leftJoin('accounts_chartof_accounts as chartof', 'dtails.char_of_account_id','=','chartof.id')
                    ->select('dtails.*',
                        'journal.transaction_date','journal.total_debit','journal.total_credit',
                        'chartof.chart_of_accounts_title','chartof.chart_of_account_code',
                        'chartof.*')
                    ->orderBy('journal.transaction_date','ASC')
                    ->whereRaw('journal.approve_status=1  '.$where)
                    ->get();
            }

         return $return;

    }


    public function exportAsPdf($data)
    {
        $company = \App\Model\CompanyInfo::Find(1);

        $data = array(
            'company'   => $company,
            'journals'  => $data['journals'],
            'from_date' => $data['from_date'],
            'end_date'  => $data['end_date'],
        );
        ini_set("pcre.backtrack_limit", "5000000");
        $html = \View::make('accounts.reports.report_pdf.journal_reportPDF')->with($data);

        $mpdf = new \Mpdf\Mpdf([
            'margin_left'   => 20,
            'margin_right'  => 15,
            'margin_top'    => 48,
            'margin_bottom' => 25,
            'margin_header' => 10,
            'margin_footer' => 10
        ]);

        $title = 'Journal Report';
        $author = 'iVenture';
        if ($company) {
            $title .= ' - '.$company->name;
            $author = $company->name;
        }

        $mpdf->SetProtection(array('print'));
        $mpdf->SetTitle($title);
        $mpdf->SetAuthor($author);
        $mpdf->SetWatermarkText("Journal Report");
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
