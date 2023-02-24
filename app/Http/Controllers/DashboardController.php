<?php

namespace App\Http\Controllers;

use App\Model\Accounts\AccountsBankPaymentVoucher;
use App\Model\Accounts\AccountsBankReceiptVoucher;
use App\Model\Accounts\AccountsCashPaymentVoucher;
use App\Model\Accounts\AccountsCashReceiptVoucher;
use App\Model\Accounts\AccountsPurchaseOrderVoucher;
use App\Model\Accounts\AccountsSalesInvoiceVoucher;
use App\Model\CompanyInfo;
use App\Model\Configuration;
use App\Model\Inventory\InventoryCurrentStock;
use App\Model\LC\LcWorkOrderImport;
use App\Model\Purchase\PurchaseOrderReceive;
use App\Model\Purchase\PurchaseRequisition;
use App\Model\Sales\SalesDeliveryChallan;
use App\Model\Sales\SalesInvoice;
use Illuminate\Http\Request;
use DB;

class DashboardController extends Controller
{
    public function index(){

        $data['company_info'] = CompanyInfo::get()->first();

        $data['req_inv'] = PurchaseRequisition::where('approve_status',0)->count();
        $data['prd_inv'] = InventoryCurrentStock::SelectRaw('sum(inventory_stocks_current_qty) as qty')->first()->qty;

        $data['prd_alert_qty'] =DB::table('production_products')
            ->join('inventory_current_stocks','production_products.id','=','inventory_current_stocks.inventory_current_stocks_product_id')
            ->where('production_products.credit_limit','>','inventory_current_stocks.current_qty')->count();

        $data['total_product']  = DB::table('inventory_current_stocks')
            ->select(DB::raw('count(*) as total'))
            ->first();

        $data['total_lc'] = LcWorkOrderImport::selectRaw('count(id)')->where('status',1)->count();

        $data['total_sales_req'] = SalesInvoice::selectRaw('count(id)')->where('status',1)->count();
        $data['total_sales_req_pending'] = SalesInvoice::selectRaw('count(id)')->where('approve_status',0)->count();
        $data['total_sales_req_apprv'] = SalesInvoice::selectRaw('count(id)')->where('approve_status',1)->count();
        $data['sales_order_lists'] =SalesInvoice::with('get_customer')->where('approve_status',0)->orderBy('id','DESC')->limit(5)->get();

        $data['total_sales_delivery'] = SalesDeliveryChallan::selectRaw('count(id)')->where('status',1)->count();
        $data['total_sales_delivery_pending'] = SalesDeliveryChallan::selectRaw('count(id)')->where('approve_status',0)->count();
        $data['total_sales_delivery_apprv'] = SalesDeliveryChallan::selectRaw('count(id)')->where('approve_status',1)->count();
        $data['sales_delivery_lists'] = SalesDeliveryChallan::with('get_customer')->where('approve_status',0)->orderBy('id','DESC')->limit(5)->get();

        $data['total_acc_sales'] = AccountsSalesInvoiceVoucher::selectRaw('count(id)')->where('status',1)->count();
        $data['total_acc_sales_pending'] = AccountsSalesInvoiceVoucher::selectRaw('count(id)')->where('approve_status',0)->count();
        $data['total_acc_sales_apprv'] = AccountsSalesInvoiceVoucher::selectRaw('count(id)')->where('approve_status',1)->count();
        $data['sales_acc_lists'] = AccountsSalesInvoiceVoucher::where('approve_status',0)->orderBy('id','DESC')->limit(5)->get();

        /*purchase*/
        $data['total_purchase_req'] = PurchaseRequisition::selectRaw('count(id)')->where('status',1)->count();
        $data['total_purchase_req_pending'] = PurchaseRequisition::selectRaw('count(id)')->where('approve_status',0)->count();
        $data['total_purchase_req_apprv'] = PurchaseRequisition::selectRaw('count(id)')->where('approve_status',1)->count();
        $data['purchase_order_lists'] =PurchaseRequisition::where('approve_status',0)->orderBy('id','DESC')->limit(5)->get();

        $data['total_po_order'] = PurchaseOrderReceive::selectRaw('count(id)')->where('status',1)->count();
        $data['total_po_order_pending'] = PurchaseOrderReceive::selectRaw('count(id)')->where('approve_status',0)->count();
        $data['total_po_order_apprv'] = PurchaseOrderReceive::selectRaw('count(id)')->where('approve_status',1)->count();
        $data['po_order_lists'] = PurchaseOrderReceive::with('get_supplier')->where('approve_status',0)->orderBy('id','DESC')->limit(5)->get();


        $data['total_acc_po'] = AccountsPurchaseOrderVoucher::selectRaw('count(id)')->where('status',1)->count();
        $data['total_acc_po_pending'] = AccountsPurchaseOrderVoucher::selectRaw('count(id)')->where('approve_status',0)->count();
        $data['total_acc_po_apprv'] = AccountsPurchaseOrderVoucher::selectRaw('count(id)')->where('approve_status',1)->count();
        $data['po_acc_lists'] = AccountsPurchaseOrderVoucher::where('approve_status',0)->orderBy('id','DESC')->limit(5)->get();

        /*Payment Accounting*/
        $data['total_bn_pay_acc_po'] = AccountsBankPaymentVoucher::selectRaw('count(id)')->count();
        $data['total_bn_pay_pending'] = AccountsBankPaymentVoucher::selectRaw('count(id)')->where('approve_status',0)->count();
        $data['total_bn_pay_apprv'] = AccountsBankPaymentVoucher::selectRaw('count(id)')->where('approve_status',1)->count();
        $data['bn_pay_lists'] = AccountsBankPaymentVoucher::where('approve_status',0)->orderBy('id','DESC')->limit(5)->get();

        $data['total_cash_pay_acc_po'] = AccountsCashPaymentVoucher::selectRaw('count(id)')->count();
        $data['total_cash_pay_pending'] = AccountsCashPaymentVoucher::selectRaw('count(id)')->where('approve_status',0)->count();
        $data['total_cash_pay_apprv'] = AccountsCashPaymentVoucher::selectRaw('count(id)')->where('approve_status',1)->count();
        $data['cash_pay_lists'] = AccountsCashPaymentVoucher::where('approve_status',0)->orderBy('id','DESC')->limit(5)->get();

        /*Reacive Accounting*/
        $data['total_bn_re_acc'] = AccountsBankReceiptVoucher::selectRaw('count(id)')->count();
        $data['total_bn_re_pending'] = AccountsBankReceiptVoucher::selectRaw('count(id)')->where('approve_status',0)->count();
        $data['total_bn_re_apprv'] = AccountsBankReceiptVoucher::selectRaw('count(id)')->where('approve_status',1)->count();
        $data['bn_re_lists'] = AccountsBankReceiptVoucher::where('approve_status',0)->orderBy('id','DESC')->limit(5)->get();

        $data['total_cash_re_acc'] = AccountsCashReceiptVoucher::selectRaw('count(id)')->count();
        $data['total_cash_re_pending'] = AccountsCashReceiptVoucher::selectRaw('count(id)')->where('approve_status',0)->count();
        $data['total_cash_re_apprv'] = AccountsCashReceiptVoucher::selectRaw('count(id)')->where('approve_status',1)->count();
        $data['cash_re_lists'] = AccountsCashReceiptVoucher::where('approve_status',0)->orderBy('id','DESC')->limit(5)->get();




        return view('dashboard',$data);
    }
}
