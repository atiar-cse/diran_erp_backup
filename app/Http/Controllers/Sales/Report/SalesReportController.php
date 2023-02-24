<?php

namespace App\Http\Controllers\Sales\Report;

use App\Exports\Sales\SalesReportExport;
use App\Exports\Sales\SalesReturnReportExport;
use App\Exports\Sales\SalesSummaryReportExport;
use App\Model\CompanyInfo;
use Illuminate\Http\Request;
use Response;
use App\Http\Controllers\Controller;
use App\Model\Production\ProductionProducts;
use App\Model\Purchase\PurchaseWareHouse;
use App\Model\Sales\SalesCustomer;
use App\Model\Sales\SalesDeliveryChallan;
use App\Model\Sales\SalesDeliveryReturn;
use DB;
use Maatwebsite\Excel\Facades\Excel;

use App\Model\Accounts\AccountsJournalEntry;

class SalesReportController extends Controller
{
    public function sales(Request $request)
    {
        if ($request->filled('export')) {

            $file_type = $request->export;

            $data = [];
            $data['sales'] = $this->salesFilterReport($request);

            $data['from_date'] = $request->from_date;
            $data['end_date']  = $request->end_date;

            if ($file_type == 'pdf') {
                $this->exportAsSalesReportPdf($data);

            } elseif ($file_type == 'excel') {

                return Excel::download(new SalesReportExport($data['sales']), 'Sales Reports.xlsx');

            } else {
                dd('Something went wrong.');

            }
        }


        if ($request->ajax()) {
            return $this->salesFilterReport($request);
        }


        $productLists = ProductionProducts::Select('production_products.*')
            ->where('production_products.status', '1')
            ->where('production_products.is_raw_material_status', '0')
            ->get();
        $challanLists  = SalesDeliveryChallan::where('status', '1')->where('approve_status', '1')->get();
        $warehouseLists  = PurchaseWareHouse::where('status', '1')->get();
        $customerLists  = SalesCustomer::where('status', '1')->get();

        return view('sales.report.sales_report',compact('productLists','challanLists','customerLists','warehouseLists'));
    }

    public function salesFilterReport(Request $request){
            $sales='';
            $where='';
            $challan_id   = $request->challan_id;
            $customer_id  = $request->customer_id;
            $warehouse_id = $request->warehouse_id;
            $product_id   = $request->product_id;

            $from_date2 = $request->from_date;
            $end_date2  = $request->end_date;
            if($from_date2!=''){
                $from_date=date('Y-m-d', strtotime(str_replace('/', '-',$from_date2)));
            }else{$from_date='';}

            if($end_date2!=''){
                $end_date=date('Y-m-d', strtotime(str_replace('/', '-',$end_date2)));
            }else{$end_date='';}


            if($challan_id!=''){$where.=" and challan.id= $challan_id";}
            if($customer_id!=''){$where.=" and challan.sales_customer_id = $customer_id ";}
            if($warehouse_id!=''){$where.="and challan.sales_warehouse_id = $warehouse_id";}
            if($product_id!=''){$where.=" and dtails.sales_delivery_details_product_id=$product_id";}

            if($from_date!=''){$where.=" and challan.sales_delivery_date>='$from_date' ";}
            if($end_date!=''){$where.=" and challan.sales_delivery_date<='$end_date' ";}

            if($where!=''){
                $sales = DB::table('sales_delivery_challan_details as dtails')
                    ->leftJoin('sales_delivery_challans as challan', 'dtails.sales_delivery_id','=','challan.id')
                    ->leftJoin('production_products as pr', 'dtails.sales_delivery_details_product_id','=','pr.id')
                    ->leftJoin('purchase_ware_houses as wh', 'challan.sales_warehouse_id','=','wh.id')
                    ->leftJoin('sales_customers as cus', 'challan.sales_customer_id','=','cus.id')
                    ->select('dtails.*',
                        'cus.sales_customer_name','wh.purchase_ware_houses_name',
                        'challan.sales_delivery_no','pr.product_name','challan.sales_delivery_date')
                    ->orderBy('challan.sales_delivery_date','desc')
                    ->whereRaw('challan.status=1  '.$where)
                    ->get();
                    //->toJson();
                //dd($where);
            }/*else{
                $sales = DB::table('sales_delivery_challan_details as dtails')
                    ->leftJoin('sales_delivery_challans as challan', 'dtails.sales_delivery_id','=','challan.id')
                    ->leftJoin('production_products as pr', 'dtails.sales_delivery_details_product_id','=','pr.id')

                    ->leftJoin('purchase_ware_houses as wh', 'challan.sales_warehouse_id','=','wh.id')
                    ->leftJoin('sales_customers as cus', 'challan.sales_customer_id','=','cus.id')
                    ->select('dtails.*',
                        'cus.sales_customer_name','wh.purchase_ware_houses_name',
                        'challan.sales_delivery_no','pr.product_name','challan.sales_delivery_date')

                    ->orderBy('dtails.id','desc')
                    ->where('challan.account_status','2')
                    ->get();
            }*/

            return $sales;
       // }
    }

    public function exportAsSalesReportPdf($data)
    {
        $company =CompanyInfo::Find(1);

        $data = array(
            'company'   => $company,
            'sales'     => $data['sales'],
            'from_date' => $data['from_date'],
            'end_date'  => $data['end_date'],
        );
        $html = \View::make('sales.report.sales_pdf.sales_reportPDF')->with($data);

        $mpdf = new \Mpdf\Mpdf([
            'margin_left'   => 20,
            'margin_right'  => 15,
            'margin_top'    => 48,
            'margin_bottom' => 25,
            'margin_header' => 10,
            'margin_footer' => 10
        ]);

        $title = 'Sales Report';
        $author = 'iVenture';
        if ($company) {
            $title .= ' - ' . $company->name;
            $author = $company->name;
        }

        $mpdf->SetProtection(array('print'));
        $mpdf->SetTitle($title);
        $mpdf->SetAuthor($author);
        $mpdf->SetWatermarkText("Sales Report");
        $mpdf->showWatermarkText = true;
        $mpdf->watermark_font = 'DejaVuSansCondensed';
        $mpdf->watermarkTextAlpha = 0.07;
        $mpdf->SetDisplayMode('fullpage');
        // $stylesheet = file_get_contents(public_path().'/css/mpdf.css');
        // $mpdf->WriteHTML($stylesheet,1);
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }

    public function returnd(Request $request)
    {
        if ($request->filled('export')) {

            $file_type = $request->export;

            $data = [];
            $data['sales_return'] = $this->salesReturnFilterReport($request);

            $data['from_date'] = $request->from_date;
            $data['end_date']  = $request->end_date;

            if ($file_type == 'pdf') {
                $this->exportAsSalesReturnReportPdf($data);

            } elseif ($file_type == 'excel') {

                return Excel::download(new SalesReturnReportExport($data['sales_return']), 'Sales Return Reports.xlsx');

            } else {
                dd('Something went wrong.');
            }
        }

        if ($request->ajax()) {
            return $this->salesReturnFilterReport($request);
        }


        $productLists = ProductionProducts::Select('production_products.*')
            ->where('production_products.status', '1')
            ->where('production_products.is_raw_material_status', '0')
            ->get();
        $returnLists  = SalesDeliveryReturn::where('status', '1')->where('approve_status', '1')->get();
        $warehouseLists  = PurchaseWareHouse::where('status', '1')->get();
        $customerLists  = SalesCustomer::where('status', '1')->get();

        return view('sales.report.sales_return_report',compact('productLists','returnLists','customerLists','warehouseLists'));
    }



    public function salesReturnFilterReport(Request $request){
            $return_val = '';
            $where='';

            $return_id   = $request->return_id;
            $customer_id = $request->customer_id;
            $warehouse_id= $request->warehouse_id;
            $product_id  = $request->product_id;

            $from_date2 =$request->from_date;
            $end_date2 =$request->end_date;
            if($from_date2!=''){
                $from_date=date('Y-m-d', strtotime(str_replace('/', '-',$from_date2)));
            }else{$from_date='';}

            if($end_date2!=''){
                $end_date=date('Y-m-d', strtotime(str_replace('/', '-',$end_date2)));
            }else{$end_date='';}



            if($return_id!=''){$where.=" and return.id= $return_id";}
            if($customer_id!=''){$where.=" and return.sales_delivery_return_customer_id = $customer_id ";}
            if($warehouse_id!=''){$where.="and return.sales_delivery_return_warehouse_id = $warehouse_id";}
            if($product_id!=''){$where.=" and details.sales_delivery_return_details_product_id=$product_id";}

            if($from_date!=''){$where.=" and return.sales_delivery_return_date>='$from_date' ";}
            if($end_date!=''){$where.=" and return.sales_delivery_return_date<='$end_date' ";}

            if($where !=''){
                $return_val = DB::table('sales_delivery_return_details as details')
                    ->leftJoin('sales_delivery_returns as return', 'details.sales_delivery_return_id','=','return.id')
                    ->leftJoin('production_products as pr', 'details.sales_delivery_return_details_product_id','=','pr.id')

                    ->leftJoin('purchase_ware_houses as wh', 'return.sales_delivery_return_warehouse_id','=','wh.id')
                    ->leftJoin('sales_customers as cus', 'return.sales_delivery_return_customer_id','=','cus.id')

                    ->select('details.*',
                        'cus.sales_customer_name','wh.purchase_ware_houses_name',
                        'return.sales_delivery_return_no','pr.product_name','return.sales_delivery_return_date')

                    ->orderBy('return.sales_delivery_return_date','desc')
                    ->whereRaw('return.status=1  '.$where)
                    ->get();
            }

            return $return_val;

    }

    public function exportAsSalesReturnReportPdf($data)
    {
        $company =CompanyInfo::Find(1);

        $data = array(
            'company'     => $company,
            'sales_return'=> $data['sales_return'],
            'from_date'   => $data['from_date'],
            'end_date'    => $data['end_date'],
        );
        $html = \View::make('sales.report.sales_pdf.sales__return_reportPDF')->with($data);

        $mpdf = new \Mpdf\Mpdf([
            'margin_left'   => 20,
            'margin_right'  => 15,
            'margin_top'    => 48,
            'margin_bottom' => 25,
            'margin_header' => 10,
            'margin_footer' => 10
        ]);

        $title = 'Sales Return  Report';
        $author = 'iVenture';
        if ($company) {
            $title .= ' - ' . $company->name;
            $author = $company->name;
        }

        $mpdf->SetProtection(array('print'));
        $mpdf->SetTitle($title);
        $mpdf->SetAuthor($author);
        $mpdf->SetWatermarkText("Sales Return Report");
        $mpdf->showWatermarkText = true;
        $mpdf->watermark_font = 'DejaVuSansCondensed';
        $mpdf->watermarkTextAlpha = 0.07;
        $mpdf->SetDisplayMode('fullpage');
        // $stylesheet = file_get_contents(public_path().'/css/mpdf.css');
        // $mpdf->WriteHTML($stylesheet,1);
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }


    public function customer_index(Request $request)
    {
        if ($request->ajax()) {

            $customer_id = '';
            $chart_ac_id = '';
            $from_date = '';
            $end_date = '';

            if ($request->filled('customer_id')) {
                $customer_id = $request->customer_id;

                $customer = SalesCustomer::where('status', '1')->Find($customer_id);
                if ($customer && $customer->chart_ac_id > 0) {
                    $chart_ac_id = $customer->chart_ac_id;
                } else {
                    return Response::json(array(
                            'status'      =>  'failed',
                            'code'      =>  501,
                            'message'   =>  'Chart of Accounts not found for this customer!.'
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

        $customerLists  = SalesCustomer::where('status', '1')->get();

        return view('sales.report.customer_ledeger_report',compact('customerLists'));
    }

    public function sales_summary(Request $request)
    {
        if ($request->filled('export')) {

            $file_type = $request->export;

            $data = [];
            $data['sales'] = $this->salesSummaryFilterReport($request);

            $data['from_date'] = $request->from_date;
            $data['end_date']  = $request->end_date;

            if ($file_type == 'pdf') {
                $this->exportAsSalesSummaryReportPdf($data);

            } elseif ($file_type == 'excel') {

                return Excel::download(new SalesSummaryReportExport($data['sales']), 'Customer Summary Reports.xlsx');

            } else {
                dd('Something went wrong.');

            }
        }


        if ($request->ajax()) {
            return $this->salesSummaryFilterReport($request);
        }


        $productLists = ProductionProducts::Select('production_products.*')
            ->where('production_products.status', '1')
            ->where('production_products.is_raw_material_status', '0')
            ->get();
        $customerLists  = SalesCustomer::where('status', '1')->get();

        return view('sales.report.sales_summary_report',compact('productLists','customerLists'));

    }

    public function salesSummaryFilterReport(Request $request){
        $sales='';
        $where='';
        $customer_id  = $request->customer_id;
        $product_id   = $request->product_id;
        $from_date2 = $request->from_date;
        $end_date2  = $request->end_date;

        if($from_date2!=''){
            $from_date=date('Y-m-d', strtotime(str_replace('/', '-',$from_date2)));
        }else{$from_date='';}

        if($end_date2!=''){
            $end_date=date('Y-m-d', strtotime(str_replace('/', '-',$end_date2)));
        }else{$end_date='';}


        if($customer_id!=''){$where.=" and challan.sales_customer_id = $customer_id ";}

        if($product_id!=''){$where.=" and dtails.sales_delivery_details_product_id = $product_id ";}

        if($from_date!=''){$where.=" and challan.sales_delivery_date >= $from_date ";}
        if($end_date!=''){$where.=" and challan.sales_delivery_date <= $end_date ";}
        //dd($where);

        if($where!==''){
            $sales = DB::table('sales_delivery_challan_details as dtails')
                ->leftJoin('sales_delivery_challans as challan', 'dtails.sales_delivery_id','=','challan.id')
                ->leftJoin('production_products as pr', 'dtails.sales_delivery_details_product_id','=','pr.id')
                ->leftJoin('sales_customers as cus', 'challan.sales_customer_id','=','cus.id')
                ->select('dtails.*',
                    'cus.sales_customer_name',
                    'challan.sales_delivery_no','pr.product_name','challan.sales_delivery_date')
                ->orderBy('challan.sales_delivery_date','desc')
                ->whereRaw('challan.status=1  '.$where)
                ->get();

        }else{
            $sales = DB::table('sales_delivery_challan_details as dtails')
                ->leftJoin('sales_delivery_challans as challan', 'dtails.sales_delivery_id','=','challan.id')
                ->leftJoin('production_products as pr', 'dtails.sales_delivery_details_product_id','=','pr.id')
                ->leftJoin('sales_customers as cus', 'challan.sales_customer_id','=','cus.id')
                ->select('dtails.*',
                    'cus.sales_customer_name',
                    'challan.sales_delivery_no','pr.product_name','challan.sales_delivery_date')
                ->orderBy('challan.sales_delivery_date','desc')
                ->get();
        }
        return $sales;
    }

    public function exportAsSalesSummaryReportPdf($data)
    {
        $company =CompanyInfo::Find(1);

        $data = array(
            'company'     => $company,
            'sales'=> $data['sales'],
            'from_date'   => $data['from_date'],
            'end_date'    => $data['end_date'],
        );
        $html = \View::make('sales.report.sales_pdf.sales_summary_reportPDF')->with($data);

        $mpdf = new \Mpdf\Mpdf([
            'margin_left'   => 20,
            'margin_right'  => 15,
            'margin_top'    => 48,
            'margin_bottom' => 25,
            'margin_header' => 10,
            'margin_footer' => 10
        ]);

        $title = 'Customer Summary Report';
        $author = 'iVenture';
        if ($company) {
            $title .= ' - ' . $company->name;
            $author = $company->name;
        }

        $mpdf->SetProtection(array('print'));
        $mpdf->SetTitle($title);
        $mpdf->SetAuthor($author);
        $mpdf->SetWatermarkText("Customer Summary Report");
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
