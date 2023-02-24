<?php

namespace App\Http\Controllers\LcImport;

use App\Http\Controllers\Controller;
use App\Model\LC\LcCustomDutyChargeDetails;
use App\Model\LC\LcCustomDutyChargeEntry;
use App\Model\LC\LcCustomDutyCostNameEntry;
use App\Model\LC\LcOpeningSection;
use App\Model\Production\ProductionProducts;
use App\Model\Purchase\PurchaseSupplierEntry;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

use App\Model\LC\LcCnfAgent;

class CustomDutyChargeEntryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = DB::table('lc_custom_duty_charge_entries')
                ->leftJoin('lc_opening_sections', 'lc_custom_duty_charge_entries.lc_opening_info_id', '=', 'lc_opening_sections.id')
                ->leftJoin('lc_commercial_invoice_entries', 'lc_custom_duty_charge_entries.lc_commercial_invoice_id', '=', 'lc_commercial_invoice_entries.id')
                ->leftJoin('lc_custom_duty_cost_name_entries', 'lc_custom_duty_charge_entries.custom_duty_cost_id', '=', 'lc_custom_duty_cost_name_entries.id')
                ->leftJoin('users as ura', 'lc_custom_duty_charge_entries.created_by', '=', 'ura.id')
                ->leftJoin('users as ured', 'lc_custom_duty_charge_entries.updated_by', '=', 'ured.id')
                ->leftJoin('users as urea', 'lc_custom_duty_charge_entries.approve_by', '=', 'urea.id')
                ->whereNull('lc_custom_duty_charge_entries.deleted_at')
                ->select(['lc_custom_duty_charge_entries.id AS id',
                    'lc_custom_duty_charge_entries.lc_custom_duty_no',
                    'lc_custom_duty_charge_entries.custom_duty_date',
                    'lc_custom_duty_charge_entries.total_cost',
                    'lc_custom_duty_charge_entries.created_by',
                    'lc_custom_duty_charge_entries.updated_by',
                    'lc_custom_duty_charge_entries.approve_by',
                    'lc_custom_duty_charge_entries.approve_status',
                    'lc_opening_sections.lc_no',
                    'lc_commercial_invoice_entries.ci_no',
                    'lc_custom_duty_cost_name_entries.duty_cost_name',
                    'ura.user_name as ad_name',
                    'ured.user_name as ed_name',
                    'urea.user_name as ap_name'
                ]);

            return datatables()->of($query)->toJson();
        }

        $lcLists = LcOpeningSection::select('id', 'lc_no')->where('status', 1)->where('approve_status', 1)
            ->where('lc_closing_status', 0)->get();
        $lcCustomDutyNameLists = LcCustomDutyCostNameEntry::where('status', 1)->selectRaw('id,duty_cost_name')->get();
        $productLists = ProductionProducts::where('status', 1)->selectRaw('id,product_name,product_code')->get();
        $cnf_agent_list = LcCnfAgent::where('status',1)->select('id','cnf_agent_name')->get();
        return view('lc.lc_section.custom_duty_charge_entry', compact(
            'lcLists', 'lcCustomDutyNameLists', 'productLists', 'cnf_agent_list'));
    }

    public function lcNoGenerate()
    {
        $id = LcOpeningSection::withTrashed()->count();
        $currentId = $id + 1;
        return 'LC-CD-' . date('Ym') . $currentId;
    }

    public function create()
    {

    }

    public function store(Request $request)
    {
        $ci_id = $request->lc_commercial_invoice_id;
        $cost_id = $request->custom_duty_cost_id;

        $this->validate($request, [
            'lc_opening_info_id' => 'required',
            'lc_commercial_invoice_id' => 'required',
            'custom_duty_date' => 'required',
            'custom_duty_cost_id' => [
                'required',
                Rule::unique('lc_custom_duty_charge_entries')->whereNull('deleted_at')
                    ->where(function ($query) use ($ci_id, $cost_id) {
                        return $query->where('lc_commercial_invoice_id', $ci_id)
                            ->where('custom_duty_cost_id', $cost_id);
                    }),
            ],
            'details.*.product_id' => 'required',
            'details.*.cost_value' => 'required|numeric',
            'lc_custom_duty_no' => 'required',
        ], [
            'lc_opening_info_id.required' => 'The LC No field is required.',
            'lc_commercial_invoice_id.required' => 'The CI No field is required.',
            'custom_duty_cost_id.required' => 'The Custom Duty Cost Name field is required.',
            'custom_duty_cost_id.unique' => 'The Custom Duty Cost Name has already been taken for the CI No.',
            'details.*.product_id.required' => 'Required field.',
            'details.*.cost_value.required' => 'Required field.',
            'lc_custom_duty_no.required' => 'This field is required.',
        ]);

        $input = $request->except('details');
        $input['custom_duty_date'] = dateConvertFormtoDB($request->custom_duty_date);
        $input['created_by'] = Auth::user()->id;

        $approval = $request->approve;

        try {
            $bug = 0;
            DB::beginTransaction();

            $result = LcCustomDutyChargeEntry::create($input);
            $details = $this->dataFormat($request, $result->id);
            LcCustomDutyChargeDetails::insert($details);
            if ($approval == 1) {
                $this->approve($result->id);

                if ($request->updated_by == Null) {
                    DB::table('lc_custom_duty_charge_entries')->where('id', $result->id)->update(array('updated_by' => Auth::user()->id));
                }
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Custom Duty Charge Successfully saved!']);
        } catch (Exception $e) {
            DB::rollback();
            print_r($e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    public function dataFormat($request, $id)
    {
        $dataFormat = [];
        $count = count($request->details);
        for ($i = 0; $i < $count; $i++) {
            $dataFormat[$i] = [
                'lc_custom_duty_entry_id' => $id,
                'product_id' => $request->details[$i]['product_id'],
                'cost_value' => $request->details[$i]['cost_value'],
            ];
        }

        return $dataFormat;
    }

    public function approve($id)
    {
        $lcLatr = LcCustomDutyChargeEntry::Find($id);
        if ($lcLatr->approve_status == 0) {

            $lcLatr->approve_status = 1;
            $lcLatr->approve_by = Auth::user()->id;
            $lcLatr->approve_at = Carbon::now();
            $lcLatr->save();
        }
    }

    public function show($id)
    {
        try {
            $editModeData = LcCustomDutyChargeEntry::with('get_lc_no', 'get_ci_no', 'get_custom_duty', 'get_cnf_agent')->FindOrFail($id);
            $editModeDetailsData = LcCustomDutyChargeDetails::with('get_product')->where('lc_custom_duty_entry_id', $id)->get();

            $data = [
                'id' => $editModeData->id,
                'lc_custom_duty_no' => $editModeData->lc_custom_duty_no,
                'custom_duty_date' => dateConvertDBtoForm($editModeData->custom_duty_date),
                'total_cost' => $editModeData->total_cost,
                'narration' => $editModeData->narration,
                'status' => $editModeData->status,
                'approve_status' => $editModeData->approve_status,
                'details' => $editModeDetailsData,
                'get_lc_no' => $editModeData->get_lc_no,
                'get_ci_no' => $editModeData->get_ci_no,
                'get_custom_duty' => $editModeData->get_custom_duty,
                'get_cnf_agent' => $editModeData->get_cnf_agent ? $editModeData->get_cnf_agent->purchase_supplier_name : '',
            ];
            return response()->json(['status' => 'success', 'data' => $data]);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'data' => []]);
        }
    }

    public function edit($id)
    {
        try {
            $editModeData = LcCustomDutyChargeEntry::FindOrFail($id);
            $editModeDetailsData = LcCustomDutyChargeDetails::where('lc_custom_duty_entry_id', $id)->get();

            $data = [
                'id' => $editModeData->id,
                'lc_opening_info_id' => $editModeData->lc_opening_info_id,
                'lc_commercial_invoice_id' => $editModeData->lc_commercial_invoice_id,
                'custom_duty_date' => dateConvertDBtoForm($editModeData->custom_duty_date),
                'custom_duty_cost_id' => $editModeData->custom_duty_cost_id,
                'total_cost' => $editModeData->total_cost,
                'cnf_agent_id' => $editModeData->cnf_agent_id,
                'lc_custom_duty_no' => $editModeData->lc_custom_duty_no,
                'narration' => $editModeData->narration,
                'status' => $editModeData->status,
                'details' => $editModeDetailsData,
            ];
            return response()->json(['status' => 'success', 'data' => $data]);
        } catch (Exception $e) {
            print_r($e->getMessage());
            return response()->json(['status' => 'error', 'data' => []]);
        }
    }

    public function update(Request $request, $id)
    {
        $ci_id = $request->lc_commercial_invoice_id;
        $cost_id = $request->custom_duty_cost_id;

        $this->validate($request, [
            'lc_opening_info_id' => 'required',
            'lc_commercial_invoice_id' => 'required',
            'custom_duty_date' => 'required',
            'custom_duty_cost_id' => [
                'required',
                Rule::unique('lc_custom_duty_charge_entries')->ignore($id)->whereNull('deleted_at')
                    ->where(function ($query) use ($ci_id, $cost_id) {
                        return $query->where('lc_commercial_invoice_id', $ci_id)
                            ->where('custom_duty_cost_id', $cost_id);
                    }),
            ],
            'details.*.product_id' => 'required',
            'details.*.cost_value' => 'required|numeric',
            'lc_custom_duty_no' => 'required',
        ], [
            'lc_opening_info_id.required' => 'The LC No field is required.',
            'lc_commercial_invoice_id.required' => 'The CI No field is required.',
            'custom_duty_cost_id.required' => 'The Custom Duty Cost Name field is required.',
            'custom_duty_cost_id.unique' => 'The Custom Duty Cost Name has already been taken for the CI No.',
            'details.*.product_id.required' => 'Required field.',
            'details.*.cost_value.required' => 'Required field.',
            'lc_custom_duty_no.required' => 'This field is required.',
        ]);

        $approval = $request->approve;

        try {
            DB::beginTransaction();

            $input = $request->except('details');
            $input['custom_duty_date'] = dateConvertFormtoDB($request->custom_duty_date);

            if ($approval != 1) {
                $input['updated_by'] = Auth::user()->id;
            }

            $editModeData = LcCustomDutyChargeEntry::FindOrFail($id);
            $editModeData->update($input);

            if ($approval == 1) {
                $this->approve($id);
            }

            /* Insert update and delete proforma-invoice details  */
            LcCustomDutyChargeDetails::where('lc_custom_duty_entry_id', $id)->delete();

            $dataFormat = [];
            $count = count($request->details);
            for ($i = 0; $i < $count; $i++) {
                $dataFormat[$i] = [
                    'lc_custom_duty_entry_id' => $id,
                    'product_id' => $request->details[$i]['product_id'],
                    'cost_value' => $request->details[$i]['cost_value'],
                ];
            }
            if (count($dataFormat) > 0) {
                LcCustomDutyChargeDetails::insert($dataFormat);
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Custom Duty Charge successfully updated!']);
        } catch (Exception $e) {
            DB::rollback();
            print_r($e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    public function destroy($id)
    {
        try {
            LcCustomDutyChargeDetails::where('lc_custom_duty_entry_id', $id)->delete();
            LcCustomDutyChargeEntry::FindOrFail($id)->delete();
            $bug = 0;
        } catch (Exception $e) {
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Custom Duty Charge successfully Deleted !']);
        } elseif ($bug == 1451) {
            return response()->json(['status' => 'error', 'message' => 'This data is used another table,can not delete data']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }
}
