<?php

namespace App\Http\Controllers\LcImport;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use DB;

use App\Model\Purchase\PurchaseSupplierEntry;
use App\Model\Production\ProductionProducts;
use App\Model\LC\LcWorkOrderImport;

use App\Model\LC\LcProformaInvoiceEntry;
use App\Model\LC\LcProformaInvoiceDetails;

use App\Model\LC\LcOpeningSection;

use App\Model\LC\LcCommercialInvoiceEntry;
use App\Model\LC\LcCommercialInvoiceDetails;
use App\Model\LC\LcCfValueMarginEntry;

use App\Model\LC\LcLatrEntry;
use App\Model\LC\LcCustomDutyChargeEntry;
use App\Model\LC\LcCustomDutyChargeDetails;

use App\Model\LC\LcOthersChargeEntry;

use App\Model\LC\LcCustomDutyCostNameEntry;

class LcReportController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $work_order_id = $request->work_order_id;
            $supplier_id = $request->supplier_id;
            $product_id = $request->product_id;
            $from_date = $request->from_date;
            $to_date = $request->to_date;

            $q = LcCommercialInvoiceEntry::query();
            $q->where('approve_status',1);
            $q->with('get_lc_no');

            if ($request->filled('work_order_id')) {

                $pi_IDs = LcProformaInvoiceEntry::where('work_order_id',$request->work_order_id)->pluck('id');
                $lc_opening_IDs = LcOpeningSection::whereIn('proforma_invoice_id',$pi_IDs)->pluck('id');

                $q->whereIn('lc_opening_info_id',$lc_opening_IDs);
            }
            if ($request->filled('supplier_id')) {
                $lc_opening_IDs = LcOpeningSection::where('supplier_id',$request->supplier_id)->pluck('id');

                $q->whereIn('lc_opening_info_id',$lc_opening_IDs);
            }

            return $q->orderBy('id', 'desc')->paginate($request->perPage);
        }

        $supplierLists = PurchaseSupplierEntry::where('status',1)->where('is_importer',1)->select('id','purchase_supplier_name')->get();
        $productLists = ProductionProducts::where('status',1)->select('id','product_name','product_code')->get();
        $workOrderLists = LcWorkOrderImport::select('id','work_order_no')->where('approve_status',1)->get();

        return view('lc.lc_section.lc_report',compact('supplierLists','productLists','workOrderLists'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {

    }

    public function show($ci_id) //$work_order_id
    {
        try {
            $lcCommercial = LcCommercialInvoiceEntry::where('approve_status',1)->FindOrFail($ci_id);
            $lcCommercialDetails = LcCommercialInvoiceDetails::with('get_product','get_measure_unit')->where('lc_commercial_invoice_id',$lcCommercial->id)->Get();

            $lcOpening = LcOpeningSection::where('approve_status',1)
                                  ->with('get_opening_bank')
                                  ->FindOrFail($lcCommercial->lc_opening_info_id);

            $lcProformaInv = LcProformaInvoiceEntry::where('status',1)
                                    ->FindOrFail($lcOpening->proforma_invoice_id);

            $lcWorkOrder = LcWorkOrderImport::with('get_supplier')
                                    ->where('approve_status',1)
                                    ->FindOrFail($lcProformaInv->work_order_id);

            $bdt_convert_rate = LcProformaInvoiceDetails::select('bdt_convert_rate')
                                    ->where('lc_proforma_invoice_id',$lcProformaInv->id)
                                    ->First();

            $lcMargin = LcCfValueMarginEntry::where('approve_status',1)
                                    ->where('lc_opening_info_id',$lcOpening->id)
                                    ->First();

            $lcLATR = LcLatrEntry::where('approve_status',1)
                            ->where('lc_commercial_invoice_id',$lcCommercial->id)
                            ->First();
            $LcCustomDutyCharges = LcCustomDutyChargeEntry::with('get_custom_duty','get_details')
                                          ->where('approve_status',1)
                                          ->where('lc_commercial_invoice_id',$lcCommercial->id)
                                          ->Get();
            $LcOthersChargeEntry = LcOthersChargeEntry::with('get_cost_cat','get_details')
                                          ->where('approve_status',1)
                                          ->where('lc_commercial_invoice_id',$lcCommercial->id)
                                          ->Get();

            $vat_name_row = LcCustomDutyCostNameEntry::where(DB::raw('LOWER(duty_cost_name)'), 'LIKE', 'vat')->First();
            $vat_products = '';
            if ($vat_name_row) {
                $vat_products = LcCustomDutyChargeDetails::where('lc_custom_duty_entry_id',$vat_name_row->id)
                                        ->get();
            }

            $temp_products_customs = '';
            if (!empty($LcCustomDutyCharges)) {
                $ids = $LcCustomDutyCharges->pluck('id');
                $temp_products_customs = LcCustomDutyChargeDetails::whereIn('lc_custom_duty_entry_id',$ids)
                                        ->selectRaw('sum(cost_value) as cost_value')
                                        ->groupBy('product_id')
                                        ->get();
            }

            $data = [
                'lc_work_order' => $lcWorkOrder,
                'lc_proforma_inv' => $lcProformaInv,
                'bdt_convert_rate' => $bdt_convert_rate,

                'lc_opening' => $lcOpening,
                'lc_commercial' => $lcCommercial,
                'lc_commercial_details' => $lcCommercialDetails,

                'lc_margin' => $lcMargin,    //C& F VALUE:
                'lc_latr' => $lcLATR,
                'lc_custom_duty_charges' => $LcCustomDutyCharges,
                'lc_others_charges' => $LcOthersChargeEntry,

                'vat_products' => $vat_products,
                'temp_products_customs' => $temp_products_customs,
                'products_landed_cost' => [],
            ];

            return response()->json(['status'=>'success','data'=>$data]);
        } catch(\Exception $e){
            return response()->json(['status'=>'error','data'=>[]]);
        }
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }

}
