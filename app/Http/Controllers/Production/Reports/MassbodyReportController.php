<?php

namespace App\Http\Controllers\Production\Reports;

use App\Exports\Product\MassBodyReportExport;
use App\Model\CompanyInfo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Maatwebsite\Excel\Facades\Excel;

class MassbodyReportController extends Controller
{

    public function index(Request $request)
    {
        /*if ($request->filled('export')) {

            $file_type = $request->export;

            $data = [];
            $data['massbodies'] = $this->massbodyFilterReport($request);

            $data['from_date'] = $request->from_date;
            $data['end_date']  = $request->end_date;

            if ($file_type=='pdf') {

                $this->exportAsPdf($data);

            } elseif ($file_type=='excel') {

                return Excel::download(new MassBodyReportExport( $data['massbodies'] ), 'Massbody Report.xlsx');

            } else {
                dd('Something went wrong.');
            }
        }*/

        if ($request->ajax()) {

            return $this->massbodyFilterReport($request);
        }
        return view('production.production_report.massbody_report');
    }


    public function massbodyFilterReport(Request $request){
        $return = '';

        //if ($request->ajax()) {
            $where='';
            $from_date2 = $request->from_date;
            $end_date2  = $request->end_date;

            if($from_date2!=''){
                $from_date=date('Y-m-d', strtotime(str_replace('/', '-',$from_date2)));
            }else{$from_date='';}

            if($end_date2!=''){
                $end_date=date('Y-m-d', strtotime(str_replace('/', '-',$end_date2)));
            }else{$end_date='';}

            if($from_date!=''){$where.=" and p_rms.requisition_date>='$from_date' ";}
            if($end_date!=''){$where.=" and p_rms.requisition_date<='$end_date' ";}

            if($where!=''){

                $return = DB::table('production_requisition_for_rm_details as dtails')
                    ->leftJoin('production_requisition_for_rms as p_rms', 'dtails.requisition_for_rm_id','=','p_rms.id')
                    ->leftJoin('production_products as pp', 'dtails.product_id','=','pp.id')
                    ->select('dtails.*',
                        'p_rms.requisition_date',
                        'pp.product_name',
                        DB::raw('SUM(dtails.qty) as consumption')
                        )
                    ->groupBy('dtails.product_id')
                    ->orderBy('dtails.id','desc')
                    ->whereRaw('p_rms.approve_status=1  '.$where)
                    ->get();
            }

            return $return;
        //}

        //return view('production.production_report.massbody_report');

    }


    /*public function exportAsPdf($data)
    {
        $company = CompanyInfo::Find(1);

        $data = array(
            'company'   => $company,
            'massbodies'=> $data['massbodies'],
            'from_date' => $data['from_date'],
            'end_date'  => $data['end_date'],
        );
        $html = \View::make('production.production_report.production_pdf.massbodyPDF')->with($data);

        $mpdf = new \Mpdf\Mpdf([
            'margin_left'   => 20,
            'margin_right'  => 15,
            'margin_top'    => 48,
            'margin_bottom' => 25,
            'margin_header' => 10,
            'margin_footer' => 10
        ]);

        $title = 'Massbody Report';
        $author = 'iVenture';
        if ($company) {
            $title .= ' - '.$company->name;
            $author = $company->name;
        }

        $mpdf->SetProtection(array('print'));
        $mpdf->SetTitle($title);
        $mpdf->SetAuthor($author);
        $mpdf->SetWatermarkText("Massbody Report");
        $mpdf->showWatermarkText = true;
        $mpdf->watermark_font = 'DejaVuSansCondensed';
        $mpdf->watermarkTextAlpha = 0.07;
        $mpdf->SetDisplayMode('fullpage');
        // $stylesheet = file_get_contents(public_path().'/css/mpdf.css');
        // $mpdf->WriteHTML($stylesheet,1);
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }*/
}
