<?php

namespace App\Http\Controllers\LcImport;

use App\Http\Controllers\Controller;
use App\Model\LC\LcCfValueMarginEntry;
use App\Model\LC\LcOpeningSection;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LcCandFValueEntryController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {

            $query = DB::table('lc_cf_value_margin_entries')
                ->leftJoin('purchase_supplier_entries', 'lc_cf_value_margin_entries.supplier_id', '=', 'purchase_supplier_entries.id')
                ->leftJoin('lc_opening_sections', 'lc_cf_value_margin_entries.lc_opening_info_id', '=', 'lc_opening_sections.id')
                ->leftJoin('accounts_bank_names', 'lc_cf_value_margin_entries.bank_id', '=', 'accounts_bank_names.id')
                ->leftJoin('users as ura', 'lc_cf_value_margin_entries.created_by', '=', 'ura.id')
                ->leftJoin('users as ured', 'lc_cf_value_margin_entries.updated_by', '=', 'ured.id')
                ->leftJoin('users as urea', 'lc_cf_value_margin_entries.approve_by', '=', 'urea.id')
                ->whereNull('lc_cf_value_margin_entries.deleted_at')
                ->select(['lc_cf_value_margin_entries.id AS id',
                    'lc_cf_value_margin_entries.margin_entry_date',
                    'lc_cf_value_margin_entries.lc_value',
                    'lc_cf_value_margin_entries.margin_percentage',
                    'lc_cf_value_margin_entries.margin_value',
                    'lc_cf_value_margin_entries.narration',
                    'lc_cf_value_margin_entries.lc_margin_no',
                    'lc_cf_value_margin_entries.created_by',
                    'lc_cf_value_margin_entries.updated_by',
                    'lc_cf_value_margin_entries.approve_by',
                    'lc_cf_value_margin_entries.approve_status',
                    'purchase_supplier_entries.purchase_supplier_name',
                    'lc_opening_sections.lc_no',
                    'accounts_bank_names.accounts_bank_names',
                    'ura.user_name as ad_name',
                    'ured.user_name as ed_name',
                    'urea.user_name as ap_name'
                ]);

            return datatables()->of($query)->toJson();
        }

        $lcLists = LcOpeningSection::with('get_supplier', 'get_opening_bank')
            ->where('status', 1)
            ->where('approve_status', 1)
            ->where('lc_closing_status', 0)
            ->get();
        /* $chartofAccountList = AccountsChartofAccounts::where('status', '1')->get();*/

        return view('lc.lc_section.lc_c&f_value_margin_entry', compact('lcLists'));
    }

    public function lcNoGenerate()
    {
        $id = LcOpeningSection::withTrashed()->count();
        $currentId = $id + 1;
        return 'LC-Mar-' . date('Ym') . $currentId;
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'lc_opening_info_id' => 'required',//|unique:lc_cf_value_margin_entries,lc_opening_info_id
            'supplier_id' => 'required',
            'margin_entry_date' => 'required',
            'lc_value' => 'required|numeric',
            'margin_value' => 'required|numeric',
            'bank_id' => 'required',
            //'margin_percentage' => 'required|numeric|max:999',
            'lc_margin_no' => 'required',
        ], [

            'lc_opening_info_id.required' => 'The LC No field is required.',
            //'lc_opening_info_id.unique' => 'The LC No already exist.',
            'lc_margin_no.required' => 'This field is required.'
        ]);

        $input = $request->all();
        $input['margin_entry_date'] = dateConvertFormtoDB($request->margin_entry_date);
        $input['created_by'] = Auth::user()->id;

        $approval = $request->approve;

        try {
            DB::beginTransaction();

            $lcMargin = LcCfValueMarginEntry::create($input);

            if ($approval == 1) {
                $this->approve($lcMargin->id);

                if ($request->updated_by == Null) {
                    DB::table('lc_cf_value_margin_entries')->where('id', $lcMargin->id)->update(array('updated_by' => Auth::user()->id));
                }
            }

            DB::commit();
            $bug = 0;
        } catch (Exception $e) {
            DB::rollback();
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Proforma Invoice Margin Entry successfully saved!']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    public function approve($id)
    {
        $lcMargin = LcCfValueMarginEntry::Find($id);
        if ($lcMargin->approve_status == 0) {

            $lcMargin->approve_status = 1;
            $lcMargin->approve_by = Auth::user()->id;
            $lcMargin->approve_at = Carbon::now();
            $lcMargin->save();
        }
    }

    public function show($id)
    {
        try {
            $editModeData = LcCfValueMarginEntry::with('get_lc_no', 'get_supplier', 'get_bank')->FindOrFail($id);

            return response()->json(['status' => 'success', 'data' => $editModeData]);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'data' => []]);
        }
    }

    public function edit($id)
    {
        try {
            $editModeData = LcCfValueMarginEntry::FindOrFail($id);

            $data = [
                'id' => $editModeData->id,
                'lc_opening_info_id' => $editModeData->lc_opening_info_id,
                'supplier_id' => $editModeData->supplier_id,
                'margin_entry_date' => dateConvertDBtoForm($editModeData->margin_entry_date),
                'bank_id' => $editModeData->bank_id,
                'lc_value' => $editModeData->lc_value,
                'margin_percentage' => $editModeData->margin_percentage,
                'margin_value' => $editModeData->margin_value,
                'narration' => $editModeData->narration,
                'lc_margin_no' => $editModeData->lc_margin_no,
                'status' => $editModeData->status,
                'supplier_info_display' => '', //Populated from frontend
                'bank_info_display' => '', //Populated from frontend
            ];
            return response()->json(['status' => 'success', 'data' => $data]);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'data' => []]);
        }
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'lc_opening_info_id' => 'required',//|unique:lc_cf_value_margin_entries,lc_opening_info_id,' . $id,
            'supplier_id' => 'required',
            'margin_entry_date' => 'required',
            'lc_value' => 'required|numeric',
            'margin_value' => 'required|numeric',
            'bank_id' => 'required',
            //'margin_percentage' => 'required|numeric|max:999',
            'lc_margin_no' => 'required',
        ]);

        $input = $request->all();
        $input['margin_entry_date'] = dateConvertFormtoDB($request->margin_entry_date);

        $approval = $request->approve;

        if ($approval != 1) {
            $input['updated_by'] = Auth::user()->id;
        }
        try {
            DB::beginTransaction();

            $editModeData = LcCfValueMarginEntry::FindOrFail($id);
            $editModeData->update($input);

            if ($approval == 1) {
                $this->approve($id);
            }

            DB::commit();
            $bug = 0;
        } catch (Exception $e) {
            DB::rollback();
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Proforma Invoice Margin Entry successfully updated!']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    public function destroy($id)
    {
        try {
            LcCfValueMarginEntry::FindOrFail($id)->delete();

            $bug = 0;
        } catch (Exception $e) {
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Proforma Invoice Margin Entry successfully Deleted !']);
        } elseif ($bug == 1451) {
            return response()->json(['status' => 'error', 'message' => 'This data is used another table,can not delete data']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

}
