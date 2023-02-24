<?php

namespace App\Http\Controllers\Production\Reports;

use App\Model\Production\ProductionCurrentStock;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

use App\Model\Production\ProductionProducts;

use App\Model\Production\ProductionFinishedStock;
use App\Model\Production\ProductionFinishedStockDetails;

use App\Model\Production\ProductionIndirectCosts;
use App\Model\Production\ProductionIndirectCostDetails;

class CostOfProductionReportController extends Controller
{
    public function index(Request $request)
    {
        if($request->ajax()){

            return $this->getCostOfProductionFilterData($request);
        }

        if ($request->filled('export')) { //Pending

            $file_type = $request->export;

            $data = [];
            $data['ledgers'] = $this->getCostOfProductionFilterData($request);

            $data['coa_title'] = AccountsChartofAccounts::where('id', $request->account_id)->first()->chart_of_accounts_title;

            $data['from_date'] = $request->from_date;
            $data['end_date'] = $request->end_date;

            if ($file_type=='pdf') {

                $this->exportAsPdf($data);
            } elseif ($file_type=='excel') {

                return Excel::download(new LedgerReportExport( $data['ledgers'] ), 'Ledger Reports.xlsx');
            } else {
                dd('Something went wrong.');
            }
        }
        $product_list  = ProductionProducts::where('status', '1')
                                            //->where('is_raw_material_status','!=',0)
                                            ->select('id','product_name')
                                            ->get();

        return view('production.production_report.cost_of_production_report',compact('product_list'));
    }

    public function getCostOfProductionFilterData(Request $request){
        
            // $q = DB::table('production_finished_stocks')->query();

        $product_id = null;
        $from_date = date('Y-m-01');
        $to_date   = date('Y-m-d');

        if ($request->filled('product_id')) {
            $product_id = $request->product_id;
        }
        if ($request->filled('from_date')) {
            $from_date = $request->from_date;
        }
        if ($request->filled('to_date')) {
            $to_date = $request->to_date;
        }

        $finishedIDs = ProductionFinishedStock::whereBetween('date', [$from_date, $to_date])
                                ->where('approve_status',1)
                                ->pluck('id'); 

        /* get sub total for all products with the date range. */
        $qAll = ProductionFinishedStockDetails::query();
                $qAll->leftJoin('production_products as prod', 'production_finished_stock_details.product_id','=','prod.id');
        $qAll->whereIn('production_finished_stock_details.finished_stock_section_id',$finishedIDs);
        $qAll->selectRaw(
                'prod.kiln_weight,
                sum(production_finished_stock_details.trans_to_store_qty) as total_qty,
                sum(production_finished_stock_details.total_price) as total_price'
            );
        $qAll->groupBy('product_id');
        $allProducts = $qAll->get();

        $total_kg = 0;
        $total_rm_price = 0;
        foreach ($allProducts as $row) {
            $total_kg = $total_kg + ($row->total_qty * $row->kiln_weight);
            $total_rm_price = $total_rm_price + $row->total_price;
        }

        /* get total indirect cost */
        $total_indirect_cost = ProductionIndirectCosts::whereBetween('date', [$from_date, $to_date])
                                ->where('approve_status',1)
                                ->selectRaw('sum(total_amount) as indirect_cost')
                                ->First();
        $indirect_cost_per_kg = 0;
        if ($total_indirect_cost && $total_indirect_cost->indirect_cost) {
            $indirect_cost_per_kg = $total_indirect_cost->indirect_cost / $total_kg;
        }

        /* Main Query Started */
        $q = ProductionFinishedStockDetails::query();
                $q->leftJoin('production_products as prod', 'production_finished_stock_details.product_id','=','prod.id');

        if ($request->filled('product_id')) {
            $q->where('production_finished_stock_details.product_id',$product_id);
        }
        $q->whereIn('production_finished_stock_details.finished_stock_section_id',$finishedIDs);
        $q->selectRaw(
                'prod.product_name,
                prod.kiln_weight,
                sum(production_finished_stock_details.trans_to_store_qty) as total_qty,
                sum(production_finished_stock_details.total_price) as total_price'
            );
        $q->groupBy('product_id');
        $products = $q->get();

        $data = [];
        foreach ($products as $product) {

            $add_indirect_cost = round($product->total_qty * $indirect_cost_per_kg * $product->kiln_weight,2);

            if ($product->total_qty != 0)
                $unit_price = round( ($product->total_price + $add_indirect_cost) / $product->total_qty,2);
            else
                $unit_price = 0;

            $data[] = [
                        'product_name' => $product->product_name . ' (Qty: ' . $product->total_qty . ', Per Kg: '.$product->kiln_weight . ')',
                        'rm_price' => $product->total_price,
                        'direct_expense' => 0,
                        'indirect_expense' => $add_indirect_cost,
                        'total_price' => $product->total_price + $add_indirect_cost,
                        'unit_price' => $unit_price, //x
                    ];
        }

        return $data;                              
    }

    public function exportAsPdf($data) //Pending
    {
        $company = \App\Model\CompanyInfo::Find(1);
        $data = array(
            'company'   => $company,
            'ledgers'   => $data['ledgers'],
            'from_date' => $data['from_date'],
            'end_date'  => $data['end_date'],
            'coa_title' => $data['coa_title'],
        );

        $html = \View::make('production.production_report.report_pdf.cost_of_production_reportPDF')->with($data);

        $mpdf = new \Mpdf\Mpdf([
            'margin_left' => 20,
            'margin_right' => 15,
            'margin_top' => 48,
            'margin_bottom' => 25,
            'margin_header' => 10,
            'margin_footer' => 10
        ]);

        $title = 'Ledger Report';
        $author = 'iVenture';
        if ($company) {
            $title .= ' - '.$company->name;
            $author = $company->name;
        }

        $mpdf->SetProtection(array('print'));
        $mpdf->SetTitle($title);
        $mpdf->SetAuthor($author);
        $mpdf->SetWatermarkText("Ledger Report");
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
