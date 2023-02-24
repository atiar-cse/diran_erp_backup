<?php

namespace App\Http\Controllers\Inventory\Report;

use App\Http\Controllers\Production\Setup\IndirectCostsTypeController;
use App\Model\Inventory\StockTransectionLog;
use App\Model\Production\ProductionCategory;
use App\Model\Production\ProductionIndirectCosts;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Model\Purchase\PurchaseWareHouse;
use App\Model\Production\ProductionProducts;
use App\Model\Production\ProductionBrand;
use App\Model\Production\ProductionType;

use DB;

class SalesPriceReportController extends Controller
{
    public function index(Request $request){

        if ($request->ajax()) {

            return $this->getInvCostPricingFilterData($request);
        }

        $productLists   = ProductionProducts::where('status', '1')
                                    ->select('id','product_name','product_code')
                                    ->get();
        $warehouseLists   = PurchaseWareHouse::where('status', '1')
                                    ->select('id','purchase_ware_houses_name')
                                    ->get();
        $product_brands   = ProductionBrand::where('status', '1')
                                    ->select('id','product_brand_name')
                                    ->get();
        $product_types = ProductionCategory::where('status', '1')
                                    ->select('id','product_category_name')
                                    ->get();

        return view('inventory.report.sales_price_report',compact( 'productLists','warehouseLists','product_brands','product_types'));
    }


    public function getInvCostPricingFilterData(Request $request)
    {
        $where='';
        $warehouse_id   = $request->warehouse_id;
        //$type_id       = $request->type_id;
        $product_id     = $request->product_id;
        $from           = substr($request->from_date,6,4).'-'.substr($request->from_date,3,2).'-'.substr($request->from_date,0,2);
        $to             = substr($request->to_date,6,4).'-'.substr($request->to_date,3,2).'-'.substr($request->to_date,0,2);

        if($product_id!=''){$where.=" and stock_transection_log_details.product_id=$product_id";}
        //if($type_id!=''){$where.=" and production_categories.id=$type_id";}

        DB::connection()->enableQueryLog();

        if($where!='')
        {
            $data= DB::table('stock_transection_logs')
                ->Select('inventory_current_stocks.inventory_stocks_open_qty','production_categories.product_category_name','production_products.product_name','production_products.product_code','stock_transection_log_details.log_open_qty','stock_transection_log_details.log_current_qty','stock_transection_log_details.log_entry_qty','stock_transection_log_details.log_close_qty','stock_transection_log_details.log_table_name','stock_transection_logs.id','stock_transection_log_details.entry_date')
                ->leftJoin('stock_transection_log_details', 'stock_transection_logs.id', '=', 'stock_transection_log_details.log_id')
                ->leftJoin('production_products', 'stock_transection_log_details.product_id', '=', 'production_products.id')
                ->leftJoin('production_categories', 'production_products.category_id', '=', 'production_categories.id')
                ->leftJoin('inventory_current_stocks', 'inventory_current_stocks.inventory_current_stocks_product_id', '=', 'production_products.id')
                //->where('stock_transection_logs.stock_transection_logs_date', '>=' ,"$from")
                //->where('stock_transection_logs.stock_transection_logs_date', '<=' ,"$to")
                ->where('stock_transection_logs.stock_trans_warehouse_id',$warehouse_id)
                ->whereRaw('stock_transection_logs.id > 0 '.$where)
                ->orderBy('stock_transection_logs.stock_transection_logs_date','ASC')
                ->get();
        }

        //dd(DB::getQueryLog());
        //dd(count($data));

        return $data;
    }

    function getCatWiseProductList($type_id)
    {
        $product_list = get_cat_wise_product($type_id);
        return $product_list;
    }
}
