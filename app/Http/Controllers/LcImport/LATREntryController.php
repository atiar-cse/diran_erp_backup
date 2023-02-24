<?php

namespace App\Http\Controllers\LcImport;

use App\Http\Controllers\Controller;
use App\Model\LC\LcCommercialInvoiceEntry;
use App\Model\LC\LcLatrDetails;
use App\Model\LC\LcLatrEntry;
use App\Model\LC\LcOpeningSection;
use App\Model\Production\ProductionMeasureUnit;
use App\Model\Production\ProductionProducts;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LATREntryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $query = DB::table('lc_latr_entries')
                ->leftJoin('lc_opening_sections', 'lc_latr_entries.lc_opening_info_id', '=', 'lc_opening_sections.id')
                ->leftJoin('lc_commercial_invoice_entries', 'lc_latr_entries.lc_commercial_invoice_id', '=', 'lc_commercial_invoice_entries.id')
                ->leftJoin('users as ura', 'lc_latr_entries.created_by', '=', 'ura.id')
                ->leftJoin('users as ured', 'lc_latr_entries.updated_by', '=', 'ured.id')
                ->leftJoin('users as urea', 'lc_latr_entries.approve_by', '=', 'urea.id')
                ->whereNull('lc_latr_entries.deleted_at')
                ->select(['lc_latr_entries.id AS id',
                    'lc_latr_entries.bank_latr_no',
                    'lc_latr_entries.latr_date',
                    'lc_latr_entries.lc_margin_percentage',
                    'lc_latr_entries.total_qty',
                    'lc_latr_entries.total_amount',
                    'lc_latr_entries.latr_percentage',
                    'lc_latr_entries.latr_total_amount',
                    'lc_latr_entries.created_by',
                    'lc_latr_entries.updated_by',
                    'lc_latr_entries.approve_by',
                    'lc_latr_entries.approve_status',
                    'lc_opening_sections.lc_no',
                    'lc_commercial_invoice_entries.ci_no',
                    'ura.user_name as ad_name',
                    'ured.user_name as ed_name',
                    'urea.user_name as ap_name'
                ]);

            return datatables()->of($query)->toJson();

        }

        $lcLists = LcOpeningSection::with('get_cnf_margin')
            ->select('id', 'lc_no')->where('status', 1)->where('approve_status', 1)
            ->where('lc_closing_status', 0)->get();
        $productLists = ProductionProducts::where('status', 1)->selectRaw('id,product_name,product_code,measure_unit_id')->get();
        $measureUnitList = ProductionMeasureUnit::where('status', '1')->get();

        return view('lc.lc_section.latr_entry', compact('lcLists',
            'productLists', 'measureUnitList'));
    }


    public function store(Request $request)
    {
        $this->validate($request, [
            'lc_opening_info_id' => 'required',
            'lc_commercial_invoice_id' => 'required',
            'latr_date' => 'required',
            'latr_total_amount' => 'required|numeric',
            'bank_latr_no' => 'required',
            // 'details.*.product_id' => 'required',
            // 'details.*.unit_price' => 'required|numeric',
            // 'details.*.qty' => 'required|numeric',
        ], [
            'lc_opening_info_id.required' => 'The LC No field is required.',
            'lc_commercial_invoice_id.required' => 'The Commercial Invoice No field is required.',
            'latr_date.required' => 'The LATR Date field is required.',
            'latr_total_amount.required' => 'Required field.',
            'bank_latr_no.required' => 'Required field.',
            // 'details.*.product_id.required' => 'Required field.',
            // 'details.*.unit_price.required' => 'Required field.',
            // 'details.*.qty.required' => 'Required field.',
        ]);

        $input = $request->except('details');
        $input['latr_date'] = dateConvertFormtoDB($request->latr_date);
        $input['created_by'] = Auth::user()->id;

        $approval = $request->approve;

        try {
            DB::beginTransaction();

            $result = LcLatrEntry::create($input);
            $details = $this->dataFormat($request, $result->id);
            LcLatrDetails::insert($details);

            if ($approval == 1) {
                $this->approve($result->id);

                if ($request->updated_by == Null) {
                    DB::table('lc_latr_entries')->where('id', $result->id)->update(array('updated_by' => Auth::user()->id));
                }
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'LATR Payment Successfully saved!']);
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
                'lc_latr_payment_entry_id' => $id,
                'product_id' => $request->details[$i]['product_id'],
                'hs_code' => $request->details[$i]['hs_code'],
                'unit_price' => $request->details[$i]['unit_price'],
                'qty' => $request->details[$i]['qty'],
                'measure_unit_id' => $request->details[$i]['measure_unit_id'],
                'total_amount' => $request->details[$i]['total_amount'],
            ];
        }

        return $dataFormat;
    }

    public function approve($id)
    {
        $lcLatr = LcLatrEntry::FindOrFail($id);
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
            $editModeData = LcLatrEntry::with('get_lc_no', 'get_ci_no')->FindOrFail($id);
            $editModeDetailsData = LcLatrDetails::with('get_product', 'get_measure_unit')->where('lc_latr_payment_entry_id', $id)->get();

            $data = [
                'id' => $editModeData->id,
                'lc_opening_info_id' => $editModeData->lc_opening_info_id,
                'lc_commercial_invoice_id' => $editModeData->lc_commercial_invoice_id,
                'latr_date' => dateConvertDBtoForm($editModeData->latr_date),
                'lc_margin_percentage' => $editModeData->lc_margin_percentage,
                'narration' => $editModeData->narration,
                'total_qty' => $editModeData->total_qty,
                'total_amount' => $editModeData->total_amount,
                'latr_percentage' => $editModeData->latr_percentage,
                'bank_percentage' => $editModeData->bank_percentage,
                'bank_percentage_amount' => $editModeData->bank_percentage_amount,
                'latr_total_amount' => $editModeData->latr_total_amount,
                'status' => $editModeData->status,
                'approve_status' => $editModeData->approve_status,
                'details' => $editModeDetailsData,

                'get_lc_no' => $editModeData->get_lc_no,
                'get_ci_no' => $editModeData->get_ci_no,
            ];

            return response()->json(['status' => 'success', 'data' => $data]);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'data' => []]);
        }
    }

    public function edit($id)
    {
        try {
            $editModeData = LcLatrEntry::FindOrFail($id);
            $editModeDetailsData = LcLatrDetails::where('lc_latr_payment_entry_id', $id)->get();

            $data = [
                'id' => $editModeData->id,
                'lc_opening_info_id' => $editModeData->lc_opening_info_id,
                'lc_commercial_invoice_id' => $editModeData->lc_commercial_invoice_id,
                'latr_date' => dateConvertDBtoForm($editModeData->latr_date),
                'lc_margin_percentage' => $editModeData->lc_margin_percentage,
                'bank_latr_no' => $editModeData->bank_latr_no,
                'narration' => $editModeData->narration,
                'total_qty' => $editModeData->total_qty,
                'total_amount' => $editModeData->total_amount,
                'latr_percentage' => $editModeData->latr_percentage,
                'latr_total_amount' => $editModeData->latr_total_amount,
                'status' => $editModeData->status,

                'bank_percentage' => $editModeData->bank_percentage,
                'bank_percentage_amount' => $editModeData->bank_percentage_amount,

                'deleteID' => [],
                'details' => $editModeDetailsData,

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
            'lc_commercial_invoice_id' => 'required',
            'latr_date' => 'required',
            'bank_latr_no' => 'required',
            // 'details.*.product_id' => 'required',
            // 'details.*.unit_price' => 'required|numeric',
            // 'details.*.qty' => 'required|numeric',
        ], [
            'lc_opening_info_id.required' => 'The LC No field is required.',
            'lc_commercial_invoice_id.required' => 'The Commercial Invoice No field is required.',
            'latr_date.required' => 'The LATR Date field is required.',
            'bank_latr_no.required' => 'Required field.',
            // 'details.*.product_id.required' => 'Required field.',
            // 'details.*.unit_price.required' => 'Required field.',
            // 'details.*.qty.required' => 'Required field.',
        ]);

        $approval = $request->approve;

        try {
            DB::beginTransaction();

            $input = $request->except('details');
            $input['latr_date'] = dateConvertFormtoDB($request->latr_date);

            if ($approval != 1) {
                $input['updated_by'] = Auth::user()->id;
            }

            $editModeData = LcLatrEntry::FindOrFail($id);
            $editModeData->update($input);


            if ($approval == 1) {
                $this->approve($id);
            }

            /* Insert update and delete proforma-invoice details  */

            if (count($request->deleteID) > 0) {
                LcLatrDetails::whereIn('id', $request->deleteID)->delete();
            }
            $dataFormat = [];
            $count = count($request->details);
            for ($i = 0; $i < $count; $i++) {
                if (isset($request->details[$i]['id']) && $request->details[$i]['id'] != '') {
                    $updateData = [
                        'lc_latr_payment_entry_id' => $id,
                        'product_id' => $request->details[$i]['product_id'],
                        'hs_code' => $request->details[$i]['hs_code'],
                        'unit_price' => $request->details[$i]['unit_price'],
                        'qty' => $request->details[$i]['qty'],
                        'measure_unit_id' => $request->details[$i]['measure_unit_id'],
                        'total_amount' => $request->details[$i]['total_amount'],
                    ];
                    LcLatrDetails::where('id', $request->details[$i]['id'])->update($updateData);
                } else {
                    $dataFormat[$i] = [
                        'lc_latr_payment_entry_id' => $id,
                        'product_id' => $request->details[$i]['product_id'],
                        'hs_code' => $request->details[$i]['hs_code'],
                        'unit_price' => $request->details[$i]['unit_price'],
                        'qty' => $request->details[$i]['qty'],
                        'measure_unit_id' => $request->details[$i]['measure_unit_id'],
                        'total_amount' => $request->details[$i]['total_amount'],
                    ];
                }
            }

            if (count($dataFormat) > 0) {
                LcLatrDetails::insert($dataFormat);
            }

            DB::commit();
            $bug = 0;
            return response()->json(['status' => 'success', 'message' => 'LATR Payment successfully updated!']);
        } catch (Exception $e) {
            DB::rollback();
            print_r($e->getMessage());
            $bug = $e->errorInfo[1];
            if ($bug = 1062) {
                return response()->json(['status' => 'error', 'message' => 'Found Duplicate']);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
            }
        }
    }

    public function destroy($id)
    {
        try {

            LcLatrDetails::where('lc_latr_payment_entry_id', $id)->delete();
            LcLatrEntry::FindOrFail($id)->delete();

            $bug = 0;
        } catch (Exception $e) {
            $bug = $e->errorInfo[1];
        }


        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'LATR Payment successfully Deleted !']);
        } elseif ($bug == 1451) {
            return response()->json(['status' => 'error', 'message' => 'This data is used another table,can not delete data']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    public function getCommercialInvoicesByLcNo($lc_opening_info_id)
    {
        $ciLists = LcCommercialInvoiceEntry::select('id', 'ci_no','cnf_agent')
            ->where('lc_opening_info_id', $lc_opening_info_id)
            ->where('approve_status', 1)
            ->get();
        return $ciLists;
    }

}
