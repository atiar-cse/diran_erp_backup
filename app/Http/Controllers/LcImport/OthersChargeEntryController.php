<?php

namespace App\Http\Controllers\LcImport;

use App\Http\Controllers\Controller;
use App\Model\LC\LcCostNameCategory;
use App\Model\LC\LcCostNameEntry;
use App\Model\LC\LcOpeningSection;
use App\Model\LC\LcOthersChargeDetails;
use App\Model\LC\LcOthersChargeEntry;
use App\Model\LC\LcOthersCharge;

use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class OthersChargeEntryController extends Controller
{
    public function index(Request $request)
    {  
        if ($request->ajax()) {
            $query = DB::table('lc_others_charges')
                ->leftJoin('lc_opening_sections', 'lc_others_charges.lc_opening_info_id', '=', 'lc_opening_sections.id')
                ->leftJoin('lc_commercial_invoice_entries', 'lc_others_charges.lc_commercial_invoice_id', '=', 'lc_commercial_invoice_entries.id')
                ->leftJoin('users as ura', 'lc_others_charges.created_by', '=', 'ura.id')
                ->leftJoin('users as ured', 'lc_others_charges.updated_by', '=', 'ured.id')
                ->leftJoin('users as urea', 'lc_others_charges.approve_by', '=', 'urea.id')
                ->whereNull('lc_others_charges.deleted_at')
                ->select(['lc_others_charges.id AS id',
                    'lc_others_charges.lc_others_charge_no',
                    'lc_others_charges.others_charge_date',
                    'lc_others_charges.total_amount',
                    'lc_others_charges.created_by',
                    'lc_others_charges.updated_by',
                    'lc_others_charges.approve_by',
                    'lc_others_charges.approve_status',
                    'lc_opening_sections.lc_no',
                    'lc_commercial_invoice_entries.ci_no',
                    'ura.user_name as ad_name',
                    'ured.user_name as ed_name',
                    'urea.user_name as ap_name'
                ]);
            return datatables()->of($query)->toJson();
        }

        $lcLists = LcOpeningSection::select('id', 'lc_no')->where('status', 1)->where('approve_status', 1)
                        ->where('lc_closing_status', 0)->get();
        $lcCostNameCategoryList = LcCostNameCategory::with('get_cost_names')
                                    ->where('status', 1)
                                    ->selectRaw('id,cost_category_name, "0" as total_cost')
                                    ->get();
        
        return view('lc.lc_section.others_charge_entry', compact('lcLists', 'lcCostNameCategoryList'));
    }

    public function lcNoGenerate()
    {
        $id = LcOpeningSection::withTrashed()->count();
        $currentId = $id + 1;
        return 'LC-OC-' . date('Ym') . $currentId;
    }

    public function store(Request $request)
    {   
        $ci_id = $request->lc_commercial_invoice_id;
        $cost_id = $request->cost_category_id;

        $this->validate($request, [
            'lc_others_charge_no' => 'required',
            'lc_opening_info_id' => 'required',
            'lc_commercial_invoice_id' => 'required',
            // 'cost_category_id' => [
            //     'required',
            //     Rule::unique('lc_others_charge_entries')->whereNull('deleted_at')->where(function ($query) use ($ci_id, $cost_id) {
            //         return $query->where('lc_commercial_invoice_id', $ci_id)
            //             ->where('cost_category_id', $cost_id);
            //     }),
            // ],
            'others_charge_date' => 'required',
            'total_amount' => 'required|numeric|min:1',
            // 'details.*.lc_cost_name_id' => 'required',
            // 'details.*.cost_value' => 'required|numeric',
        ], [
            'lc_others_charge_no.required' => 'This field is required.',
            'lc_opening_info_id.required' => 'The LC No field is required.',
            'lc_commercial_invoice_id.required' => 'The CI No field is required.',
            'cost_category_id.required' => 'The Cost Category field is required.',
            'cost_category_id.unique' => 'The Cost Category has already been taken for the CI No.',
            // 'details.*.lc_cost_name_id.required' => 'Required field.',
            // 'details.*.cost_value.required' => 'Required field.',
        ]);

        $input = $request->except('categories');
        $input['others_charge_date'] = dateConvertFormtoDB($request->others_charge_date);
        $input['created_by'] = Auth::user()->id;

        $approval = $request->approve;

        try {
            DB::beginTransaction();

            $result = LcOthersCharge::create($input);

            $charge_details = $this->dataFormatAndInsert($request, $result->id);
            // LcOthersChargeEntry::insert($charge_entry);
            LcOthersChargeDetails::insert($charge_details);

            if ($approval == 1) {
                $this->approve($result->id);

                if ($request->updated_by == Null) {
                    DB::table('lc_others_charges')->where('id', $result->id)->update(array('updated_by' => Auth::user()->id));
                }
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'LC Other Charge Successfully saved!']);

        } catch (Exception $e) {    

            DB::rollback();
            print_r($e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Something Error Found!, Please try again']);
        }
    }

    public function show($id)
    {

        try {
            $lcOthersChargeData = LcOthersCharge::with('get_lc_no', 'get_ci_no')->FindOrFail($id); 
            $lcOthersChargeEntryData = LcOthersChargeEntry::with('get_cost_cat','get_details')->where('lc_other_charge_id', $id)->get()->toArray();  
            $data = [
                'id'                  => $lcOthersChargeData->id,
                'others_charge_date'  => dateConvertDBtoForm($lcOthersChargeData->others_charge_date),
                'total_cost'          => $lcOthersChargeData->total_amount,
                'narration'           => $lcOthersChargeData->narration,
                'lc_others_charge_no' => $lcOthersChargeData->lc_others_charge_no,
                'approve_status'      => $lcOthersChargeData->approve_status,
                'get_lc_no'           => $lcOthersChargeData->get_lc_no,
                'get_ci_no'           => $lcOthersChargeData->get_ci_no,
                'category'            => $lcOthersChargeEntryData,  
            ];

            return response()->json(['status' => 'success', 'data' => $data]);
        } catch (Exception $e) {
             print_r($e->getMessage());
            return response()->json(['status' => 'error', 'data' => []]);
        }
    }

    public function edit($id)
    {
        try {
            $editModeData = LcOthersCharge::FindOrFail($id);
            $charge_entry = LcOthersChargeEntry::with('get_details')
                                    ->where('lc_other_charge_id', $id)
                                    ->get();

            $data = [
                'id' => $editModeData->id,
                'lc_opening_info_id' => $editModeData->lc_opening_info_id,
                'lc_commercial_invoice_id' => $editModeData->lc_commercial_invoice_id,
                'cost_category_id' => $editModeData->cost_category_id,
                'others_charge_date' => dateConvertDBtoForm($editModeData->others_charge_date),
                'total_amount' => $editModeData->total_amount,
                'narration' => $editModeData->narration,
                'lc_others_charge_no' => $editModeData->lc_others_charge_no,
                'status' => $editModeData->status,
                'charge_entry' => $charge_entry,
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
        $cost_id = $request->cost_category_id;

        $this->validate($request, [
            'lc_others_charge_no' => 'required',
            'lc_opening_info_id' => 'required',
            'lc_commercial_invoice_id' => 'required',
            // 'cost_category_id' => [
            //     'required',
            //     Rule::unique('lc_others_charge_entries')->whereNull('deleted_at')->where(function ($query) use ($ci_id, $cost_id) {
            //         return $query->where('lc_commercial_invoice_id', $ci_id)
            //             ->where('cost_category_id', $cost_id);
            //     }),
            // ],
            'others_charge_date' => 'required',
            'total_amount' => 'required|numeric|min:1',
            // 'details.*.lc_cost_name_id' => 'required',
            // 'details.*.cost_value' => 'required|numeric',
        ], [
            'lc_others_charge_no.required' => 'This field is required.',
            'lc_opening_info_id.required' => 'The LC No field is required.',
            'lc_commercial_invoice_id.required' => 'The CI No field is required.',
            'cost_category_id.required' => 'The Cost Category field is required.',
            'cost_category_id.unique' => 'The Cost Category has already been taken for the CI No.',
            // 'details.*.lc_cost_name_id.required' => 'Required field.',
            // 'details.*.cost_value.required' => 'Required field.',
        ]);

        $approval = $request->approve;

        try {
            DB::beginTransaction();

            $input = $request->except('categories');
            $input['others_charge_date'] = dateConvertFormtoDB($request->others_charge_date);
            if ($approval != 1) {
                $input['updated_by'] = Auth::user()->id;
            }

            $editModeData = LcOthersCharge::where('approve_status',0)
                                ->FindOrFail($id);
            $editModeData->update($input);

            /* After Delete - Insert Again  */
            $IDs = LcOthersChargeEntry::where('lc_other_charge_id',$id)->pluck('id');
            LcOthersChargeEntry::where('lc_other_charge_id',$id)->delete();
            LcOthersChargeDetails::whereIn('lc_other_charge_entry_id', $IDs)->delete();

            /* @ Insert Again */
            $charge_details = $this->dataFormatAndInsert($request, $id);
            // LcOthersChargeEntry::insert($charge_entry);
            LcOthersChargeDetails::insert($charge_details);

            if ($approval == 1) {
                $this->approve($id);
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'LC Other Charge successfully updated!']);
        } catch (Exception $e) {
            DB::rollback();
            $mm = $e->getMessage();
            print_r($e->getMessage());
            return response()->json(['status' => 'error', 'message' => $mm]);
        }
    }

    public function destroy($id)
    {
        try {
            $IDs = LcOthersChargeEntry::where('lc_other_charge_id',$id)->pluck('id');
            LcOthersChargeEntry::where('lc_other_charge_id',$id)->delete();
            LcOthersChargeDetails::whereIn('lc_other_charge_entry_id', $IDs)->delete();

            LcOthersCharge::FindOrFail($id)->delete();
            $bug = 0;
        } catch (Exception $e) {
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'LC Other Charge successfully Deleted !']);
        } elseif ($bug == 1451) {
            return response()->json(['status' => 'error', 'message' => 'This data is used another table,can not delete data']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }


    public function dataFormatAndInsert($request, $id)
    {
        $dataFormat = [];

        $categories = $request->categories;
        $count = count($categories);

        for ($i = 0; $i < $count; $i++) {

            $data = [     
                    'lc_other_charge_id'        => $id,               
                    'lc_others_charge_no'       => $request->lc_others_charge_no,                    
                    'lc_opening_info_id'        => $request->lc_opening_info_id,
                    'lc_commercial_invoice_id'  => $request->lc_commercial_invoice_id,
                    'cost_category_id'          => $categories[$i]['id'],
                    'others_charge_date'        => dateConvertFormtoDB($request->others_charge_date),
                    'total_cost'                => $categories[$i]['total_cost'],
                    'narration'                 => $request->narration,
                    'created_by'                => Auth::user()->id,
                ];
            $response = LcOthersChargeEntry::create($data);

            $details = $categories[$i]['get_cost_names'];

            for ($j=0; $j < count( $details ) ; $j++) { 

                $dataFormat[] = [
                                'lc_other_charge_entry_id' => $response->id,
                                'lc_cost_name_id' => $details[$j]['id'],
                                'cost_value' => $details[$j]['cost_value'],
                    ];
            }           
        }

        return $dataFormat;
    }

    public function approve($id)
    {
        $lcLatr = LcOthersCharge::Find($id);
        if ($lcLatr->approve_status == 0) {

            $lcLatr->approve_status = 1;
            $lcLatr->approve_by = Auth::user()->id;
            $lcLatr->approve_at = Carbon::now();
            $lcLatr->save();

            LcOthersChargeEntry::where('lc_other_charge_id', $id)
                                ->update(
                                    [
                                        'approve_status' => 1,
                                        'approve_by' => Auth::user()->id,
                                        'approve_at' => Carbon::now(),
                                    ]
                                );
        }
    }    
}
