<?php

namespace App\Http\Controllers\LcImport;

use App\Model\LC\LcOthersChargeDetails;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use DB;

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

class LcCiLandedCostController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = DB::table('lc_commercial_invoice_entries')
                ->leftJoin('lc_opening_sections', 'lc_commercial_invoice_entries.lc_opening_info_id', '=', 'lc_opening_sections.id')
                ->leftJoin('purchase_supplier_entries', 'lc_opening_sections.supplier_id', '=', 'purchase_supplier_entries.id')
                ->leftJoin('users as ura', 'lc_commercial_invoice_entries.created_by','=','ura.id')
                ->leftJoin('users as ured', 'lc_commercial_invoice_entries.updated_by','=','ured.id')
                ->leftJoin('users as urea', 'lc_commercial_invoice_entries.approve_by','=','urea.id')
                ->whereNull('lc_commercial_invoice_entries.deleted_at')
                ->groupBy('lc_commercial_invoice_entries.lc_opening_info_id')
                ->select(['lc_commercial_invoice_entries.id AS id',
                    'lc_commercial_invoice_entries.ci_no',
                    'lc_commercial_invoice_entries.ci_bill_entry_date',
                    'lc_commercial_invoice_entries.narration',
                    'lc_commercial_invoice_entries.total_qty',
                    'lc_commercial_invoice_entries.total_amount',
                    'lc_commercial_invoice_entries.created_by',
                    'lc_commercial_invoice_entries.updated_by',
                    'lc_commercial_invoice_entries.approve_by',
                    'lc_commercial_invoice_entries.ci_landed_status',
                    'lc_opening_sections.supplier_id',
                    'lc_opening_sections.lc_no',
                    'purchase_supplier_entries.purchase_supplier_name',
                    'ura.user_name as ad_name',
                    'ured.user_name as ed_name',
                    'urea.user_name as ap_name'
                ]);
            return datatables()->of($query)->toJson();
        }

        return view('lc.lc_section.lc_ci_landed_cost');
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
            $lcNo = LcCommercialInvoiceEntry::where('id',$ci_id)->first()->lc_opening_info_id;
            $lcCommercial = LcCommercialInvoiceEntry::where('lc_opening_info_id',$lcNo)->where('approve_status',1)->get();
            $count = $lcCommercial->count();
            $CI_ID = [];

            foreach ($lcCommercial as $value)
            {
                array_push($CI_ID,$value->id);
            }

            $total_insurance = LcCommercialInvoiceEntry::where('lc_opening_info_id',$lcNo)->where('approve_status',1)->sum('ci_insurance');
            $total_bank_expenses = LcCommercialInvoiceEntry::where('lc_opening_info_id',$lcNo)->where('approve_status',1)->sum('ci_bank_expenses');
            $total_opening = LcCommercialInvoiceEntry::where('lc_opening_info_id',$lcNo)->where('approve_status',1)->sum('ci_opening_charge');
            $ci_margin = LcCommercialInvoiceEntry::where('lc_opening_info_id',$lcNo)->where('approve_status',1)->sum('ci_margin');
            $total_amount = LcCommercialInvoiceEntry::where('lc_opening_info_id',$lcNo)->where('approve_status',1)->sum('total_amount');
            $total_qty = LcCommercialInvoiceEntry::where('lc_opening_info_id',$lcNo)->where('approve_status',1)->sum('total_qty');
            $total_net_weight = LcCommercialInvoiceEntry::where('lc_opening_info_id',$lcNo)->where('approve_status',1)->sum('total_net_weight');
            $total_latr_amount = LcLatrEntry::where('approve_status',1)->whereIn('lc_commercial_invoice_id',$CI_ID)->sum('total_amount');

            $totalCost = $total_insurance+$total_bank_expenses+$total_opening+$ci_margin+$total_latr_amount;

            //$lcCommercial = LcCommercialInvoiceEntry::where('approve_status',1)->FindOrFail($ci_id);
            $lcCommercialDetails = LcCommercialInvoiceDetails::with('get_product','get_measure_unit')->whereIn('lc_commercial_invoice_id',$CI_ID)->Get();

            $lcOpening = LcOpeningSection::where('approve_status',1)
                                  ->with('get_opening_bank')
                                  //->FindOrFail($lcCommercial->lc_opening_info_id);
                                  ->FindOrFail($lcNo);

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
                            ->whereIn('lc_commercial_invoice_id',$CI_ID)
                            ->get();

            $LcCustomDutyCharges = LcCustomDutyChargeEntry::with('get_custom_duty','get_details')
                                          ->where('approve_status',1)
                                          ->whereIn('lc_commercial_invoice_id',$CI_ID)
                                          ->Get();
            $LcCustomDutyChargesName = LcCustomDutyChargeEntry::with('get_custom_duty','get_details')
                ->where('approve_status',1)
                ->whereIn('lc_commercial_invoice_id',$CI_ID)
                ->groupBy('custom_duty_cost_id')
                ->Get();

            $LcTotalCustomDutyCharges = LcCustomDutyChargeEntry::
                where('approve_status',1)
                ->whereIn('lc_commercial_invoice_id',$CI_ID)
                ->selectRaw('sum(total_cost) as total_cost')
                ->first()->total_cost;

            $LcOthersChargeEntry = LcOthersChargeEntry::with('get_cost_cat','get_details')
                                          ->where('approve_status',1)
                                          ->whereIn('lc_commercial_invoice_id',$CI_ID)
                                          ->groupBy('cost_category_id')
                                          ->Get();

            $LcOthersChargeIds = LcOthersChargeEntry::with('get_cost_cat','get_details')
                ->where('approve_status',1)
                ->whereIn('lc_commercial_invoice_id',$CI_ID)
                ->pluck('id')->toArray();

            $SumOfLcOthersCharges = LcOthersChargeDetails::whereIn('lc_other_charge_entry_id',$LcOthersChargeIds)
                ->groupBy('lc_cost_name_id')
                ->selectRaw('sum(cost_value) as sum_cost_value,lc_cost_name_id')
                ->get();

            for ($ci_i = 0; $ci_i < count($CI_ID);$ci_i++)
            {
                $LcOthersChargeDetails[$ci_i] = LcOthersChargeEntry::with('get_details')
                    ->where('approve_status',1)
                    ->where('lc_commercial_invoice_id',$CI_ID[$ci_i])
                    ->get();
            }


            $vat_name_row = LcCustomDutyCostNameEntry::where(DB::raw('LOWER(duty_cost_name)'), 'LIKE', 'vat')->First();
            $vat_products = '';
            if ($vat_name_row) {
                $vat_entry = LcCustomDutyChargeEntry::where('approve_status',1)
                                            ->whereIn('lc_commercial_invoice_id',$CI_ID)
                                            ->where('custom_duty_cost_id',$vat_name_row->id)
                                            ->First();
                if ($vat_entry)
                    $vat_products = LcCustomDutyChargeDetails::where('lc_custom_duty_entry_id',$vat_entry->id)->get();
                else
                    $vat_products = '';
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
                'total_amount' => $total_amount,
                'ci_margin' => $ci_margin,
                'total_qty' => $total_qty,
                'total_net_weight' => $total_net_weight,
                'lc_commercial_details' => $lcCommercialDetails,
                'total_insurance' => $total_insurance,
                'total_bank_expenses' => $total_bank_expenses,
                'total_opening' => $total_opening,
                'total_latr_amount' => $total_latr_amount,    //C& F VALUE:
                'lc_margin' => $lcMargin,    //C& F VALUE:
                'lc_latr' => $lcLATR,
                'lc_custom_duty_charges' => $LcCustomDutyCharges,
                'lc_custom_duty_charges_name' => $LcCustomDutyChargesName,
                'lc_total_custom_duty_charges' => $LcTotalCustomDutyCharges,
                'lc_others_charges' => $LcOthersChargeEntry,
                'lc_others_charges_details' => $LcOthersChargeDetails,
                'sum_of_lc_others_charges' => $SumOfLcOthersCharges,
                'total_cost'    => $totalCost,
                'count'    => $count,
                'vat_products' => $vat_products,
                'temp_products_customs' => $temp_products_customs,
                'products_landed_cost' => [],
            ];

            return response()->json(['status'=>'success','data'=>$data]);
        } catch(\Exception $e){
            dd($e->getMessage());
            return response()->json(['status'=>'error','data'=>[]]);
        }
    }

    public function edit($id)
    {
        return $this->show($id);
    }

    public function update(Request $request, $id)
    {
        $ci_details =$request->products_landed_cost;
        $approval =$request->approval;
        try {
            DB::beginTransaction();

            $lcCommercial = LcCommercialInvoiceEntry::where('approve_status',1)->FindOrFail($id);

            if($approval == 1){
                $lcCommercial->ci_landed_status = 1;
                $lcCommercial->save();

                foreach ($ci_details as $item) {
                    $result = LcCommercialInvoiceDetails::FindOrFail($item['ci_detail_id']);
                    $result->landed_unit_price = $item['landed_unit_price'];
                    $result->save();
                }
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Successfully Approved!']);
        } catch (\Exception $e) {
            DB::rollback();
            print_r($e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Something Error Found!, Please try again']);
        }
    }

    public function destroy($id)
    {
        //
    }

}
