<?php

namespace App\Http\Controllers\LcImport;

use App\Http\Controllers\Controller;
use App\Model\LC\LcInsurance;
use App\Model\LC\LcOpeningSection;
use Auth;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Http\Request;

class LcInsuranceController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = DB::table('lc_insurances')
                ->leftJoin('lc_opening_sections', 'lc_insurances.lc_opening_info_id', '=', 'lc_opening_sections.id')
                ->leftJoin('users as ura', 'lc_insurances.created_by', '=', 'ura.id')
                ->leftJoin('users as ured', 'lc_insurances.updated_by', '=', 'ured.id')
                ->leftJoin('users as urea', 'lc_insurances.approve_by', '=', 'urea.id')
                ->select(['lc_insurances.id AS id',
                    'lc_insurances.insurance_company',
                    'lc_insurances.insurance_date',
                    'lc_insurances.insurance_no',
                    'lc_insurances.insurance_amount',
                    'lc_insurances.lc_insurance_no',
                    'lc_insurances.created_by',
                    'lc_insurances.updated_by',
                    'lc_insurances.approve_by',
                    'lc_insurances.approve_status',
                    'lc_opening_sections.lc_no',
                    'lc_opening_sections.lc_total_value',
                    'ura.user_name as ad_name',
                    'ured.user_name as ed_name',
                    'urea.user_name as ap_name'
                ]);

            return datatables()->of($query)->toJson();
        }

        $lcLists = LcOpeningSection::with('get_supplier')
            ->where('status', 1)
            ->where('approve_status', 1)
            ->where('lc_closing_status', 0)
            ->get();

        return view('lc.lc_section.lc_insurance', compact('lcLists'));
    }

    public function lcNoGenerate()
    {
        $id = LcOpeningSection::withTrashed()->count();
        $currentId = $id + 1;
        return 'LC-Ins-' . date('Ym') . $currentId;
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'lc_opening_info_id' => 'required',
            'insurance_date' => 'required',
            'insurance_company' => 'required',
            'insurance_amount' => 'required|numeric',
            'lc_insurance_no' => 'required',
        ], [
            'lc_opening_info_id.required' => 'The LC No field is required.',
            'insurance_date.required' => 'The Date field is required.',
            'insurance_company.required' => 'The Insurance Company field is required.',
            'insurance_amount.required' => 'The Insurance Amount field is required.',
            'lc_insurance_no.required' => 'This field is required.',
        ]);

        $input = $request->all();
        $input['insurance_date'] = dateConvertFormtoDB($request->insurance_date);
        $input['created_by'] = Auth::user()->id;

        $approval = $request->approve;

        try {
            DB::beginTransaction();
            $response = LcInsurance::create($input);

            if ($approval == 1) {
                $this->approve($response->id);
                if ($request->updated_by == Null) {
                    DB::table('lc_insurances')
                        ->where('id', $response->id)
                        ->update(array('updated_by' => Auth::user()->id));
                }
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'LC Insurance successfully saved!']);
        } catch (Exception $e) {
            DB::rollback();
            print_r($e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    public function approve($id)
    {
        $row = LcInsurance::FindOrFail($id);
        if ($row->approve_status == 0) {

            $row->approve_status = 1;
            $row->approve_by = Auth::user()->id;
            $row->approve_at = Carbon::now();
            $row->save();

            LcOpeningSection::where('id', $row->lc_opening_info_id)
                ->increment('insurance_amount', $row->insurance_amount);
        }
    }

    public function show($id)
    {
        try {
            $editModeData = LcInsurance::with('get_lc_no')->FindOrFail($id);
            return response()->json(['status' => 'success', 'data' => $editModeData]);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'data' => []]);
        }
    }

    public function edit($id)
    {
        try {
            $editModeData = LcInsurance::with('get_lc_no')->FindOrFail($id);
            $data = [
                'id' => $editModeData->id,
                'lc_opening_info_id' => $editModeData->lc_opening_info_id,
                'lc_no' => $editModeData->get_lc_no->lc_no,
                'insurance_date' => dateConvertDBtoForm($editModeData->insurance_date),
                'insurance_company' => $editModeData->insurance_company,
                'insurance_no' => $editModeData->insurance_no,
                'insurance_amount' => $editModeData->insurance_amount,
                'narration' => $editModeData->narration,
                'lc_insurance_no' => $editModeData->lc_insurance_no,

                'supplier_info_display' => '', //Populated from frontend
                'lc_value' => $editModeData->get_lc_no->lc_total_value, //Populated from frontend
            ];
            return response()->json(['status' => 'success', 'data' => $data]);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'data' => []]);
        }
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'lc_opening_info_id' => 'required',
            'insurance_date' => 'required',
            'insurance_company' => 'required',
            'insurance_amount' => 'required|numeric',
            'lc_insurance_no' => 'required',
        ], [
            'lc_opening_info_id.required' => 'The LC No field is required.',
            'insurance_date.required' => 'The Date field is required.',
            'insurance_company.required' => 'The Insurance Company field is required.',
            'insurance_amount.required' => 'The Insurance Amount field is required.',
            'lc_insurance_no.required' => 'This field is required.',
        ]);

        $input = $request->all();
        $input['insurance_date'] = dateConvertFormtoDB($request->insurance_date);

        $approval = $request->approve;

        if ($approval != 1) {
            $input['updated_by'] = Auth::user()->id;
        }

        try {
            DB::beginTransaction();

            $editModeData = LcInsurance::with('get_lc_no')->FindOrFail($id);
            $editModeData->update($input);

            if ($approval == 1) {
                $this->approve($id);
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'LC Insurance successfully updated!']);
        } catch (Exception $e) {
            DB::rollback();
            print_r($e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    public function destroy($id)
    {
        try {
            LcInsurance::FindOrFail($id)->delete();
            return response()->json(['status' => 'success', 'message' => 'The LC Insurance entry successfully Deleted !']);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }
}
