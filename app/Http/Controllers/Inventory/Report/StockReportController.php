<?php

namespace App\Http\Controllers\Inventory\Report;

use App\Exports\Inventory\InventoryStockReportExport;
use App\Model\CompanyInfo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Model\Purchase\PurchaseWareHouse;
use App\Model\Production\ProductionProducts;
use App\Model\Inventory\InventoryCurrentStock;
use Maatwebsite\Excel\Facades\Excel;
use DB;

class StockReportController extends Controller
{
    public function stocks(Request $request)
    {

        if ($request->filled('export')) {

            $file_type = $request->export;

            $data = [];
            $data['stock_reports'] = $this->stocksFilterReport($request);

            if ($file_type == 'pdf') {
                $this->exportAsStockReportPdf($data);

            } elseif ($file_type == 'excel') {

                return Excel::download(new InventoryStockReportExport($data['stock_reports']), 'Inventory Stock Reports.xlsx');

            } else {
                dd('Something went wrong.');

            }
        }

        if ($request->ajax()) {
            return $this->stocksFilterReport($request)->toJson();
        }



        $productLists   = ProductionProducts::Select('production_products.*')->get();
        $warehouseLists   = PurchaseWareHouse::where('status', '1')->get();
        return view('inventory.inventory_stock_report',compact( 'productLists','warehouseLists'));

    }

    public function stocksFilterReport(Request $request){

            $where='';
            $warehouse_id =$request->warehouse_id;
            $product_id =$request->product_id;

            if($warehouse_id!=''){$where.="and inventory_current_stocks.inventory_current_stocks_warehouse_id= $warehouse_id";}
            if($product_id!=''){$where.=" and inventory_current_stocks.inventory_current_stocks_product_id=$product_id";}

            $stocks= InventoryCurrentStock::Select('inventory_current_stocks.*','purchase_ware_houses.purchase_ware_houses_name','production_products.product_name')
                ->leftJoin('purchase_ware_houses', 'inventory_current_stocks.inventory_current_stocks_warehouse_id', '=', 'purchase_ware_houses.id')
                ->leftJoin('production_products', 'inventory_current_stocks.inventory_current_stocks_product_id', '=', 'production_products.id')
                ->orderBy('inventory_current_stocks.id','desc')
                ->whereRaw('inventory_current_stocks.status=1  '.$where)
                ->get();
            return $stocks;

    }

    public function exportAsStockReportPdf($data)
    {
        $company =CompanyInfo::Find(1);

        $data = array(
            'company'      => $company,
            'stock_reports'=> $data['stock_reports'],

        );
        $html = \View::make('inventory.report_pdf.inventory_stock_reportPDF')->with($data);

        $mpdf = new \Mpdf\Mpdf([
            'margin_left'   => 20,
            'margin_right'  => 15,
            'margin_top'    => 48,
            'margin_bottom' => 25,
            'margin_header' => 10,
            'margin_footer' => 10
        ]);

        $title = 'Inventory Stock Report';
        $author = 'iVenture';
        if ($company) {
            $title .= ' - ' . $company->name;
            $author = $company->name;
        }

        $mpdf->SetProtection(array('print'));
        $mpdf->SetTitle($title);
        $mpdf->SetAuthor($author);
        $mpdf->SetWatermarkText("Inventory Stock Report");
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
