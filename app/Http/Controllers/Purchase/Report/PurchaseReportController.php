<?php

namespace App\Http\Controllers\Purchase\Report;

use App\Exports\Purchase\PurchaseReportExport;
use App\Exports\Purchase\PurchaseReturnReportExport;
use App\Exports\Purchase\PurchaseSummaryReportExport;
use Illuminate\Http\Request;
use Response;

use App\Http\Controllers\Controller;

use App\Model\Production\ProductionProducts;
use App\Model\Purchase\PurchaseWareHouse;
use App\Model\Purchase\PurchaseSupplierEntry;

use App\Model\Purchase\PurchaseOrderReceive;
use App\Model\Purchase\PurchaseStockEntryDetails;

use App\Model\Purchase\PurchaseReturn;
use Maatwebsite\Excel\Facades\Excel;
use DB;

use App\Model\Accounts\AccountsJournalEntry;

class PurchaseReportController extends Controller
{

    public function purchase(Request $request)
    {

        if ($request->filled('export')) {

            $file_type = $request->export;

            $data = [];
            $data['purchases'] = $this->purchaseFilterReport($request);

            $data['from_date'] = $request->from_date;
            $data['end_date']  = $request->end_date;

            if ($file_type == 'pdf') {
                $this->exportAsPurchaseReportPdf($data);

            }elseif ($file_type == 'excel') {

                return Excel::download(new PurchaseReportExport($data['purchases']), 'Purchases Reports.xlsx');

            }else {
                dd('Something went wrong.');

            }

        }

        if ($request->ajax()) {

            if ($request->avg_price == 1) {
                return $this->purchaseAvgPriceReport($request);
            } else {
                return $this->purchaseFilterReport($request);
            }            
        }


        $productLists = ProductionProducts::Select('id','product_name')
                                ->where('production_products.status', '1')
                                //->where('production_products.is_raw_material_status', '1')
                                ->get();
        $purStockLists  = PurchaseOrderReceive::where('status', '1')
                                ->where('approve_status', '1')
                                ->select('id','po_order_no')
                                ->get();
        $warehouseLists  = PurchaseWareHouse::where('status', '1')
                                ->select('id','purchase_ware_houses_name')
                                ->get();
        $supplierLists  = PurchaseSupplierEntry::where('status', '1')
                                ->select('id','purchase_supplier_name')
                                ->get();

        return view('purchase.report.purchase_report',compact('productLists','purStockLists','supplierLists','warehouseLists'));
    }


    public function purchaseFilterReport(Request $request){

        $purchase = '';

        $where='pur_ord_recv.approve_status=1 ';
        $pur_order_receive_id =$request->pur_stock_id;
        $supplier_id          =$request->supplier_id;
        $warehouse_id         =$request->warehouse_id;
        $product_id           =$request->product_id;

        $from_date2           =$request->from_date;
        $end_date2            =$request->end_date;
        if($from_date2!=''){
            $from_date=date('Y-m-d', strtotime(str_replace('/', '-',$from_date2)));
        }else{$from_date='';}

        if($end_date2!=''){
            $end_date=date('Y-m-d', strtotime(str_replace('/', '-',$end_date2)));
        }else{$end_date='';}


        if($pur_order_receive_id!=''){$where.=" and pur_ord_recv.id= $pur_order_receive_id";}
        if($supplier_id!=''){$where.=" and pur_ord_recv.po_supplier_id = $supplier_id ";}
        if($warehouse_id!=''){$where.="and pur_ord_recv.po_warehouse_id = $warehouse_id";}
        if($product_id!=''){$where.=" and dtails.pod_product_id=$product_id";}

        if($from_date!=''){$where.=" and pur_ord_recv.po_receive_date>='$from_date' ";}
        if($end_date!=''){$where.=" and pur_ord_recv.po_receive_date<='$end_date' ";}

        if($where!=''){
            $purchase = DB::table('purchase_order_receive_details as dtails')
                ->leftJoin('purchase_order_receives as pur_ord_recv', 'dtails.pod_order_id','=','pur_ord_recv.id')
                ->leftJoin('production_products as pr', 'dtails.pod_product_id','=','pr.id')

                ->leftJoin('purchase_ware_houses as wh', 'pur_ord_recv.po_warehouse_id','=','wh.id')
                ->leftJoin('purchase_supplier_entries as sup', 'pur_ord_recv.po_supplier_id','=','sup.id')

                ->select('dtails.*',
                    'sup.purchase_supplier_name','wh.purchase_ware_houses_name',
                    'pur_ord_recv.po_order_no','pr.product_name','pur_ord_recv.po_receive_date',
                    'pur_ord_recv.po_total_qty','pur_ord_recv.po_total_price','pur_ord_recv.po_narration')

                ->orderBy('pur_ord_recv.po_receive_date','ASC')
                ->whereRaw(' '.$where)
                ->get();
        }

        return $purchase;
    }

    public function purchaseAvgPriceReport(Request $request){

        $purchase = '';

        $where='pur_ord_recv.approve_status=1 ';
        $pur_order_receive_id =$request->pur_stock_id;
        $supplier_id          =$request->supplier_id;
        $warehouse_id         =$request->warehouse_id;
        $product_id           =$request->product_id;

        $from_date2           =$request->from_date;
        $end_date2            =$request->end_date;
        if($from_date2!=''){
            $from_date=date('Y-m-d', strtotime(str_replace('/', '-',$from_date2)));
        }else{$from_date='';}

        if($end_date2!=''){
            $end_date=date('Y-m-d', strtotime(str_replace('/', '-',$end_date2)));
        }else{$end_date='';}


        if($pur_order_receive_id!=''){$where.=" and pur_ord_recv.id= $pur_order_receive_id";}
        if($supplier_id!=''){$where.=" and pur_ord_recv.po_supplier_id = $supplier_id ";}
        if($warehouse_id!=''){$where.="and pur_ord_recv.po_warehouse_id = $warehouse_id";}
        if($product_id!=''){$where.=" and dtails.pod_product_id=$product_id";}

        if($from_date!=''){$where.=" and pur_ord_recv.po_receive_date>='$from_date' ";}
        if($end_date!=''){$where.=" and pur_ord_recv.po_receive_date<='$end_date' ";}

        if($where!=''){
            $purchase = DB::table('purchase_order_receive_details as dtails')                
                ->leftJoin('purchase_order_receives as pur_ord_recv', 'dtails.pod_order_id','=','pur_ord_recv.id')
                ->leftJoin('production_products as pr', 'dtails.pod_product_id','=','pr.id')

                ->leftJoin('purchase_ware_houses as wh', 'pur_ord_recv.po_warehouse_id','=','wh.id')
                ->leftJoin('purchase_supplier_entries as sup', 'pur_ord_recv.po_supplier_id','=','sup.id')

                ->selectRaw('pr.product_name,pr.id as prod_id,
                        sum(dtails.pod_qty) as total_qty,
                        sum(dtails.pod_total_price) as total_price'
                        )
                ->groupBy('dtails.pod_product_id')
                ->orderBy('dtails.pod_product_id','ASC')
                ->whereRaw(' '.$where)                
                ->get();
        }

        return $purchase;
    }


    public function exportAsPurchaseReportPdf($data)
    {
        $company = \App\Model\CompanyInfo::Find(1);

        $data = array(
            'company'   => $company,
            'purchases' => $data['purchases'],
            'from_date' => $data['from_date'],
            'end_date'  => $data['end_date'],
        );
        $html = \View::make('purchase.report.purchase_pdf.purchase_reportPDF')->with($data);

        $mpdf = new \Mpdf\Mpdf([
            'margin_left' => 20,
            'margin_right' => 15,
            'margin_top' => 48,
            'margin_bottom' => 25,
            'margin_header' => 10,
            'margin_footer' => 10
        ]);

        $title = 'Purchase Report';
        $author = 'iVenture';
        if ($company) {
            $title .= ' - ' . $company->name;
            $author = $company->name;
        }

        $mpdf->SetProtection(array('print'));
        $mpdf->SetTitle($title);
        $mpdf->SetAuthor($author);
        $mpdf->SetWatermarkText("Purchase Report");
        $mpdf->showWatermarkText = true;
        $mpdf->watermark_font = 'DejaVuSansCondensed';
        $mpdf->watermarkTextAlpha = 0.07;
        $mpdf->SetDisplayMode('fullpage');
        // $stylesheet = file_get_contents(public_path().'/css/mpdf.css');
        // $mpdf->WriteHTML($stylesheet,1);
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }


    public function return_purchase(Request $request){

        if ($request->filled('export')) {

            $file_type = $request->export;

            $data = [];
            $data['purchases_return'] = $this->purchaseReturnFilterReport($request);

            $data['from_date'] = $request->from_date;
            $data['end_date'] = $request->end_date;

            if ($file_type == 'pdf') {
                $this->exportAsPurchaseReturnReportPdf($data);

            } elseif ($file_type == 'excel') {

                return Excel::download(new PurchaseReturnReportExport($data['purchases_return']), 'Purchases Return Reports.xlsx');

            } else {
                dd('Something went wrong.');

            }
        }

        if ($request->ajax()) {
            return $this->purchaseReturnFilterReport($request);
        }


        $productLists = ProductionProducts::Select('production_products.*')
            ->where('production_products.status', '1')
            ->where('production_products.is_raw_material_status', '1')
            ->get();
        $returnLists  = PurchaseReturn::where('status', '1')->where('approve_status', '1')->get();
        $warehouseLists  = PurchaseWareHouse::where('status', '1')->get();
        $supplierLists  = PurchaseSupplierEntry::where('status', '1')->get();

        return view('purchase.report.purchase_return_report',compact('productLists','returnLists','supplierLists','warehouseLists'));
    }


    public function purchaseReturnFilterReport(Request $request){
        $return = '';

            $where='';
            $return_id=$request->return_id;
            $supplier_id=$request->supplier_id;
            $warehouse_id =$request->warehouse_id;
            $product_id =$request->product_id;

            $from_date2 =$request->from_date;
            $end_date2 =$request->end_date;
            if($from_date2!=''){
                $from_date=date('Y-m-d', strtotime(str_replace('/', '-',$from_date2)));
            }else{$from_date='';}

            if($end_date2!=''){
                $end_date=date('Y-m-d', strtotime(str_replace('/', '-',$end_date2)));
            }else{$end_date='';}


            if($return_id!=''){$where.=" and return.id= $return_id";}
            if($supplier_id!=''){$where.=" and return.po_rtn_supplier_id = $supplier_id ";}
            if($warehouse_id!=''){$where.="and return.po_rtn_warehouse_id = $warehouse_id";}
            if($product_id!=''){$where.=" and dtails.po_rtnd_product_id=$product_id";}

            if($from_date!=''){$where.=" and return.po_return_date>='$from_date' ";}
            if($end_date!=''){$where.=" and return.po_return_date<='$end_date' ";}

            if($where!=''){
                $return = DB::table('purchase_return_details as dtails')
                    ->leftJoin('purchase_returns as return', 'dtails.po_rtnd_return_id','=','return.id')
                    ->leftJoin('production_products as pr', 'dtails.po_rtnd_product_id','=','pr.id')

                    ->leftJoin('purchase_ware_houses as wh', 'return.po_rtn_warehouse_id','=','wh.id')
                    ->leftJoin('purchase_supplier_entries as sup', 'return.po_rtn_supplier_id','=','sup.id')

                    ->select('dtails.*',
                        'sup.purchase_supplier_name','wh.purchase_ware_houses_name',
                        'return.po_return_no','pr.product_name','return.po_return_date')

                    ->orderBy('dtails.id','desc')
                    ->whereRaw('return.approve_status=1  '.$where)
                    ->get();
            }

            return $return;
    }


    public function exportAsPurchaseReturnReportPdf($data)
    {
        $company = \App\Model\CompanyInfo::Find(1);

        $data = array(
            'company'          => $company,
            'purchases_return' => $data['purchases_return'],
            'from_date'        => $data['from_date'],
            'end_date'         => $data['end_date'],
        );
        $html = \View::make('purchase.report.purchase_pdf.purchase__return_reportPDF')->with($data);

        $mpdf = new \Mpdf\Mpdf([
            'margin_left'   => 20,
            'margin_right'  => 15,
            'margin_top'    => 48,
            'margin_bottom' => 25,
            'margin_header' => 10,
            'margin_footer' => 10
        ]);

        $title = 'Purchase Return Report';
        $author = 'iVenture';
        if ($company) {
            $title .= ' - ' . $company->name;
            $author = $company->name;
        }

        $mpdf->SetProtection(array('print'));
        $mpdf->SetTitle($title);
        $mpdf->SetAuthor($author);
        $mpdf->SetWatermarkText("Purchase Return Report");
        $mpdf->showWatermarkText = true;
        $mpdf->watermark_font = 'DejaVuSansCondensed';
        $mpdf->watermarkTextAlpha = 0.07;
        $mpdf->SetDisplayMode('fullpage');
        // $stylesheet = file_get_contents(public_path().'/css/mpdf.css');
        // $mpdf->WriteHTML($stylesheet,1);
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }


    public function supplier(Request $request)
    {
        if ($request->ajax()) {

            $supplier_id = '';
            $chart_ac_id = '';
            $from_date = '';
            $end_date = '';

            if ($request->filled('supplier_id')) {
                $supplier_id = $request->supplier_id;

                $supplier = PurchaseSupplierEntry::where('status', '1')->Find($supplier_id);
                if ($supplier && $supplier->chart_ac_id > 0) {
                    $chart_ac_id = $supplier->chart_ac_id;
                } else {
                    return Response::json(array(
                            'status'      =>  'failed',
                            'code'      =>  501,
                            'message'   =>  'Chart of Accounts not found for this supplier!.'
                        ), 501);
                }
            }
            if ($request->filled('from_date')) {
                $from_date = $request->from_date;
            }
            if ($request->filled('end_date')) {
                $end_date = $request->end_date;
            }

            $q = AccountsJournalEntry::query();
            $q->where('approve_status',1);
            $q->whereBetween('transaction_date', [$from_date, $end_date]);
            $q->whereHas('get_jrnl_detail', function ($query) use ($chart_ac_id) {
                $query->where('char_of_account_id',$chart_ac_id);
            });
            $q->select('id','transaction_date','transaction_reference_no','transaction_type_name','total_debit','total_credit','narration');
            $results = $q->get();

            return $results;
        }

        $supplierLists  = PurchaseSupplierEntry::where('status', '1')->select('id','purchase_supplier_name')->get();

        return view('purchase.report.supplier_ledeger_report',compact('supplierLists'));
    }

    public function purchase_summary(Request $request)
    {

        if ($request->filled('export')) {

            $file_type = $request->export;

            $data = [];
            $data['purchases'] = $this->purchaseSummaryFilterReport($request);

            $data['from_date'] = $request->from_date;
            $data['end_date'] = $request->end_date;

            if ($file_type == 'pdf') {
                $this->exportAsPurchaseSummaryReportPdf($data);

            } elseif ($file_type == 'excel') {

                return Excel::download(new PurchaseSummaryReportExport($data['purchases']), 'Purchases Summary Reports.xlsx');

            } else {
                dd('Something went wrong.');
            }
        }

        if ($request->ajax()) {

            return $this->purchaseSummaryFilterReport($request);
        }


        $productLists = ProductionProducts::Select('production_products.*')
            ->where('production_products.status', '1')
            ->where('production_products.is_raw_material_status', '1')
            ->get();
        $supplierLists = PurchaseSupplierEntry::where('status', '1')->get();

        return view('purchase.report.purchase_summery_report', compact('productLists', 'supplierLists'));
    }

    public function purchaseSummaryFilterReport(Request $request)
    {

        $purchase = '';
        $where = '';
        $supplier_id = $request->supplier_id;
        $product_id = $request->product_id;
        $from_date2 = $request->from_date;
        $end_date2 = $request->end_date;
        if ($from_date2 != '') {
            $from_date = date('Y-m-d', strtotime(str_replace('/', '-', $from_date2)));
        } else {
            $from_date = '';
        }

        if ($end_date2 != '') {
            $end_date = date('Y-m-d', strtotime(str_replace('/', '-', $end_date2)));
        } else {
            $end_date = '';
        }

        if ($supplier_id != '') {
            $where .= " and pur_ord_recv.po_supplier_id = $supplier_id ";
        }

        if ($product_id != '') {
            $where .= " and dtails.pod_product_id=$product_id";
        }

        if ($from_date != '') {
            $where .= " and pur_ord_recv.po_receive_date>='$from_date' ";
        }
        if ($end_date != '') {
            $where .= " and pur_ord_recv.po_receive_date<='$end_date' ";
        }

        if ($where != '') {
            $purchase = DB::table('purchase_order_receive_details as dtails')
                ->leftJoin('purchase_order_receives as pur_ord_recv', 'dtails.pod_order_id', '=', 'pur_ord_recv.id')
                ->leftJoin('production_products as pr', 'dtails.pod_product_id', '=', 'pr.id')
                ->leftJoin('purchase_ware_houses as wh', 'pur_ord_recv.po_warehouse_id', '=', 'wh.id')
                ->leftJoin('purchase_supplier_entries as sup', 'pur_ord_recv.po_supplier_id', '=', 'sup.id')
                ->select('dtails.*',
                    'sup.purchase_supplier_name', 'wh.purchase_ware_houses_name',
                    'pur_ord_recv.po_order_no', 'pr.product_name', 'pur_ord_recv.po_receive_date',
                    'pur_ord_recv.po_total_qty', 'pur_ord_recv.po_total_price', 'pur_ord_recv.po_narration')
                ->orderBy('dtails.id', 'desc')
                ->whereRaw('pur_ord_recv.approve_status=1  ' . $where)
                ->get();

        }else{
            $purchase = DB::table('purchase_order_receive_details as dtails')
                ->leftJoin('purchase_order_receives as pur_ord_recv', 'dtails.pod_order_id', '=', 'pur_ord_recv.id')
                ->leftJoin('production_products as pr', 'dtails.pod_product_id', '=', 'pr.id')
                ->leftJoin('purchase_ware_houses as wh', 'pur_ord_recv.po_warehouse_id', '=', 'wh.id')
                ->leftJoin('purchase_supplier_entries as sup', 'pur_ord_recv.po_supplier_id', '=', 'sup.id')
                ->select('dtails.*',
                    'sup.purchase_supplier_name', 'wh.purchase_ware_houses_name',
                    'pur_ord_recv.po_order_no', 'pr.product_name', 'pur_ord_recv.po_receive_date',
                    'pur_ord_recv.po_total_qty', 'pur_ord_recv.po_total_price', 'pur_ord_recv.po_narration')
                ->orderBy('dtails.id', 'desc')
                ->get();
        }

        return $purchase;
    }

    public function exportAsPurchaseSummaryReportPdf($data)
    {
        $company = \App\Model\CompanyInfo::Find(1);

        $data = array(
            'company'   => $company,
            'purchases' => $data['purchases'],
            'from_date' => $data['from_date'],
            'end_date'  => $data['end_date'],
        );
        $html = \View::make('purchase.report.purchase_pdf.purchase_summary_reportPDF')->with($data);

        $mpdf = new \Mpdf\Mpdf([
            'margin_left' => 20,
            'margin_right' => 15,
            'margin_top' => 48,
            'margin_bottom' => 25,
            'margin_header' => 10,
            'margin_footer' => 10
        ]);

        $title = 'Purchase Report';
        $author = 'iVenture';
        if ($company) {
            $title .= ' - ' . $company->name;
            $author = $company->name;
        }

        $mpdf->SetProtection(array('print'));
        $mpdf->SetTitle($title);
        $mpdf->SetAuthor($author);
        $mpdf->SetWatermarkText("Supplier Summary Report");
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
