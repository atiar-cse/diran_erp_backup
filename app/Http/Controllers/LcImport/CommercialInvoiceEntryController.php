<?php

namespace App\Http\Controllers\LcImport;

use App\Model\LC\LcCommercialInvoiceEntry;
use App\Model\LC\LcCommercialInvoiceDetails;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use App\Model\LC\LcOpeningSection;
use App\Model\LC\LcProformaInvoiceEntry;
use App\Model\LC\LcCnfAgent;
use App\Model\Production\ProductionProducts;
use App\Model\Country;
use App\Model\LC\LcProformaInvoiceDetails;
use App\Model\Production\ProductionMeasureUnit;
use Illuminate\Support\Facades\DB;

class CommercialInvoiceEntryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $query = DB::table('lc_commercial_invoice_entries')
                ->leftJoin('lc_opening_sections', 'lc_commercial_invoice_entries.lc_opening_info_id', '=', 'lc_opening_sections.id')
                ->leftJoin('users as ura', 'lc_commercial_invoice_entries.created_by','=','ura.id')
                ->leftJoin('users as ured', 'lc_commercial_invoice_entries.updated_by','=','ured.id')
                ->leftJoin('users as urea', 'lc_commercial_invoice_entries.approve_by','=','urea.id')
                ->whereNull('lc_commercial_invoice_entries.deleted_at')
                ->select(['lc_commercial_invoice_entries.id AS id',
                    'lc_commercial_invoice_entries.ci_no',
                    'lc_commercial_invoice_entries.receive_date',
                    'lc_commercial_invoice_entries.total_amount',
                    'lc_commercial_invoice_entries.total_qty',
                    'lc_commercial_invoice_entries.invoice_no',
                    'lc_commercial_invoice_entries.shipping_mode',
                    'lc_commercial_invoice_entries.created_by',
                    'lc_commercial_invoice_entries.updated_by',
                    'lc_commercial_invoice_entries.approve_by',
                    'lc_commercial_invoice_entries.approve_status',
                    'lc_opening_sections.lc_no',
                    'ura.user_name as ad_name',
                    'ured.user_name as ed_name',
                    'urea.user_name as ap_name'
                ]);

            return datatables()->of($query)->toJson();


        }

        $lcLists = LcOpeningSection::with('get_pi_no','get_cnf_margin')->select('id','lc_no','proforma_invoice_id','lc_total_value','lc_opening_charges','lc_bank_expenses','insurance_amount')
                                    ->where('status',1)
                                    ->where('approve_status',1)
                                    ->where('lc_closing_status',0)
                                    ->get();
        $piInvoiceLists = LcProformaInvoiceEntry::where('status',1)->selectRaw('id,pi_no,total_amount_taka')->get();
        $cnfAgentList = LcCnfAgent::where('status',1)->get();
        $productLists = ProductionProducts::where('status',1)->selectRaw('id,product_name,product_code,measure_unit_id')->get();
        $countryLists = Country::selectRaw('id,country_name')->get();
        $measureUnitList= ProductionMeasureUnit::where('status', '1')->get();

        return view('lc.lc_section.commercial_invoice_entry',compact('lcLists','piInvoiceLists','cnfAgentList','productLists','countryLists','measureUnitList'));
    }

    public function ciNoGenerate(){
        $id = LcCommercialInvoiceEntry::withTrashed()->count();
        $currentId = $id+1;
        return 'CI-'.date('Ym').$currentId;
    }



    public function store(Request $request)
    {
        $this->validate($request, [
            'lc_opening_info_id' => 'required',
            'ci_no' => 'required|alpha_dash|unique:lc_commercial_invoice_entries,ci_no',
            'shipping_mode' => 'required',
            'cnf_agent' => 'required',
            'ci_bill_entry_date' => 'required',
            'ci_delivery_type' => 'required',
            'ci_margin' => 'required',
            //'ci_opening_charge' => 'required',
            'ci_bank_expenses' => 'required',
            'ci_insurance' => 'required',
            'details.*.product_id' => 'required',
            'details.*.unit_price' => 'required|numeric',
            'details.*.net_weight' => 'required',
            'details.*.qty' => 'required|numeric',
        ], [
            'lc_opening_info_id.required' => 'The LC No field is required.',
            'ci_no.alpha_dash' => 'The CI No. field may only contain letters, numbers, dashes and underscores.',
            'ci_no.unique' => 'The CI No. has already been taken.',
            'details.*.product_id.required' => 'Required field.',
            'details.*.unit_price.required' => 'Required field.',
            'details.*.net_weight.required' => 'Required field.',
            'details.*.qty.required' => 'Required field.',
        ]);

        $input = $request->except('details'); // dd($request->details); get only details array
        $input['receive_date'] = dateConvertFormtoDB($request->receive_date);
        $input['ci_date'] = dateConvertFormtoDB($request->ci_date);
        $input['invoice_date'] = dateConvertFormtoDB($request->invoice_date);
        $input['ci_bill_entry_date'] = dateConvertFormtoDB($request->ci_bill_entry_date);
        $input['eta_date'] = dateConvertFormtoDB($request->eta_date);
        $input['created_by'] = Auth::user()->id;

        $approval = $request->approve;

        try {
            DB::beginTransaction();

            $result = LcCommercialInvoiceEntry::create($input);
            $details = $this->dataFormat($request, $result->id);
            LcCommercialInvoiceDetails::insert($details);

            if($approval ==1){

                $this->approve($result->id);

                if($request->updated_by == Null){
                    DB::table('lc_commercial_invoice_entries')->where('id', $result->id)->update(array('updated_by' => Auth::user()->id));
                }
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Proforma Invoice Successfully saved!']);
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => 'Something Went Wrong']);
        }
    }

    public function show($id)
    {
        try {
            $editModeData = LcCommercialInvoiceEntry::with('get_lc_no','get_cnf_agent','get_origin')->FindOrFail($id);
            $editModeDetailsData = LcCommercialInvoiceDetails::with('get_product','get_measure_unit')->where('lc_commercial_invoice_id',$id)->get();
            $data = [

                'ci_no' => $editModeData->ci_no,
                'ci_date' => dateConvertDBtoForm($editModeData->ci_date),
                'ci_bill_of_entry_no' => $editModeData->ci_bill_of_entry_no,
                'ci_bill_entry_date' => dateConvertDBtoForm($editModeData->ci_bill_entry_date),
                'invoice_no' => $editModeData->invoice_no,
                'invoice_date' => dateConvertDBtoForm($editModeData->invoice_date),
                'invoice_amount' => $editModeData->invoice_amount,
                'eta_date' => dateConvertDBtoForm($editModeData->eta_date),
                'cnf_agent' => $editModeData->cnf_agent,
                'shipping_mode' => $editModeData->shipping_mode,
                'receive_date' => dateConvertDBtoForm($editModeData->receive_date),
                'exp_no' => $editModeData->exp_no,
                'vassel_name' => $editModeData->vassel_name,
                'shipping_line' => $editModeData->shipping_line,
                'depart_from' => $editModeData->depart_from,
                'arrived_port' => $editModeData->arrived_port,
                'present_destination' => $editModeData->present_destination,
                'origin' => $editModeData->origin,
                'narration' => $editModeData->narration,
                'total_qty' => $editModeData->total_qty,
                'total_net_weight' => $editModeData->total_net_weight,
                'total_gross_weight' => $editModeData->total_gross_weight,
                'total_amount' => $editModeData->total_amount,
                'approve_status' => $editModeData->status,
                'status' => $editModeData->status,
                'ci_delivery_type' => $editModeData->ci_delivery_type,
                'ci_margin' => $editModeData->ci_margin,
                'ci_opening_charge' => $editModeData->ci_opening_charge,
                'ci_bank_expenses' => $editModeData->ci_bank_expenses,
                'ci_insurance' => $editModeData->ci_insurance,
                
                'details'    => $editModeDetailsData,

                'get_lc_no'    => $editModeData->get_lc_no,
                'get_cnf_agent'    => $editModeData->get_cnf_agent,
                'get_origin'    => $editModeData->get_origin,
            ];
            return response()->json(['status'=>'success','data'=>$data]);
        } catch(\Exception $e){
            return response()->json(['status'=>'error','data'=>[]]);
        }
    }

    public function edit($id)
    {
        try {
            $editModeData = LcCommercialInvoiceEntry::FindOrFail($id);
            $editModeDetailsData = LcCommercialInvoiceDetails::where('lc_commercial_invoice_id',$id)->get();
            $data = [
                'id'    => $editModeData->id,
                'lc_opening_info_id' => $editModeData->lc_opening_info_id,
                'ci_no' => $editModeData->ci_no,
                'ci_date' => dateConvertDBtoForm($editModeData->ci_date),
                'ci_bill_of_entry_no' => $editModeData->ci_bill_of_entry_no,
                'ci_bill_entry_date' => dateConvertDBtoForm($editModeData->ci_bill_entry_date),
                'invoice_no' => $editModeData->invoice_no,
                'invoice_date' => dateConvertDBtoForm($editModeData->invoice_date),
                'invoice_amount' => $editModeData->invoice_amount,
                'eta_date' => dateConvertDBtoForm($editModeData->eta_date),
                'cnf_agent' => $editModeData->cnf_agent,
                'shipping_mode' => $editModeData->shipping_mode,
                'receive_date' => dateConvertDBtoForm($editModeData->receive_date),
                'exp_no' => $editModeData->exp_no,
                'vassel_name' => $editModeData->vassel_name,
                'shipping_line' => $editModeData->shipping_line,
                'depart_from' => $editModeData->depart_from,
                'arrived_port' => $editModeData->arrived_port,
                'present_destination' => $editModeData->present_destination,
                'origin' => $editModeData->origin,
                'narration' => $editModeData->narration,
                'total_qty' => $editModeData->total_qty,
                'total_net_weight' => $editModeData->total_net_weight,
                'total_gross_weight' => $editModeData->total_gross_weight,
                'total_amount' => $editModeData->total_amount,
                'status' => $editModeData->status,
                'ci_delivery_type' => $editModeData->ci_delivery_type,
                'ci_margin' => $editModeData->ci_margin,
                'ci_opening_charge' => $editModeData->ci_opening_charge,
                'ci_bank_expenses' => $editModeData->ci_bank_expenses,
                'ci_insurance' => $editModeData->ci_insurance,
                'deleteID' => [],
                'details'    => $editModeDetailsData
            ];
            return response()->json(['status'=>'success','data'=>$data]);
        } catch(\Exception $e){
            return response()->json(['status'=>'error','data'=>[]]);
        }
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'lc_opening_info_id' => 'required',
            'ci_no' => 'required|unique:lc_commercial_invoice_entries,ci_no,'.$id,
            'shipping_mode' => 'required',
            'cnf_agent' => 'required',
            'ci_bill_entry_date' => 'required',
            'ci_delivery_type' => 'required',
            'ci_margin' => 'required',
            //'ci_opening_charge' => 'required',
            'ci_bank_expenses' => 'required',
            'ci_insurance' => 'required',            
            'details.*.product_id' => 'required',
            'details.*.unit_price' => 'required|numeric',
            'details.*.net_weight' => 'required',
            'details.*.qty' => 'required|numeric',
        ], [
            'lc_opening_info_id.required' => 'The LC No field is required.',
            'ci_no.alpha_dash' => 'The CI No. field may only contain letters, numbers, dashes and underscores.',
            'ci_no.unique' => 'The CI No. has already been taken.',
            'details.*.product_id.required' => 'Required field.',
            'details.*.unit_price.required' => 'Required field.',
            'details.*.net_weight.required' => 'Required field.',
            'details.*.qty.required' => 'Required field.',
        ]);

        $approval = $request->approve;

        try {
            DB::beginTransaction();

            $input = $request->except('details');
            $input['receive_date'] = dateConvertFormtoDB($request->receive_date);
            $input['ci_date'] = dateConvertFormtoDB($request->ci_date);
            $input['invoice_date'] = dateConvertFormtoDB($request->invoice_date);
            $input['ci_bill_entry_date'] = dateConvertFormtoDB($request->ci_bill_entry_date);
            $input['eta_date'] = dateConvertFormtoDB($request->eta_date);

            if($approval !=1){
                $input['updated_by'] = Auth::user()->id;
            }

            $editModeData = LcCommercialInvoiceEntry::FindOrFail($id);
            $editModeData->update($input);



            if($approval ==1){

                $this->approve($id);                 
            }

            /* Insert update and delete proforma-invoice details  */

            if (count($request->deleteID) > 0) {
                LcCommercialInvoiceDetails::whereIn('id', $request->deleteID)->delete();
            }
            $dataFormat = [];
            $count = count($request->details);
            for ($i = 0; $i < $count; $i++) {
                if (isset($request->details[$i]['id']) && $request->details[$i]['id'] !='') {
                    $updateData = [
                        'lc_commercial_invoice_id' => $id,
                        'product_id' => $request->details[$i]['product_id'],
                        'hs_code' => $request->details[$i]['hs_code'],
                        'unit_price' => $request->details[$i]['unit_price'],
                        'qty' => $request->details[$i]['qty'],
                        'measure_unit_id' => $request->details[$i]['measure_unit_id'],
                        'gross_weight' => $request->details[$i]['gross_weight'],
                        'net_weight' => $request->details[$i]['net_weight'],
                        'total_amount' => $request->details[$i]['total_amount'],
                    ];
                    LcCommercialInvoiceDetails::where('id', $request->details[$i]['id'])->update($updateData);
                } else {
                    $dataFormat[$i] =[
                        'lc_commercial_invoice_id' => $id,
                        'product_id' => $request->details[$i]['product_id'],
                        'hs_code' => $request->details[$i]['hs_code'],
                        'unit_price' => $request->details[$i]['unit_price'],
                        'qty' => $request->details[$i]['qty'],
                        'measure_unit_id' => $request->details[$i]['measure_unit_id'],
                        'gross_weight' => $request->details[$i]['gross_weight'],
                        'net_weight' => $request->details[$i]['net_weight'],
                        'total_amount' => $request->details[$i]['total_amount'],
                    ];
                }
            }

            if(count($dataFormat) > 0){
                LcCommercialInvoiceDetails::insert($dataFormat);
            }

            DB::commit();
            $bug = 0;
            return response()->json(['status' => 'success', 'message' => 'Commercial Invoice successfully updated!']);
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->errorInfo[1];
            if ($bug = 1062) {
                return response()->json(['status' => 'error', 'message' => 'CI No is Found Duplicate']);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
            }
        }
    }

    public function destroy($id)
    {
        try{

            LcCommercialInvoiceDetails::where('lc_commercial_invoice_id',$id)->delete();
            LcCommercialInvoiceEntry::FindOrFail($id)->delete();

            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->errorInfo[1];
        }


        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Commercial Invoice successfully Deleted !']);
        } elseif ($bug == 1451) {
            return response()->json(['status' => 'error', 'message' => 'This data is used another table,can not delete data']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    public function dataFormat($request, $id)
    {
        $dataFormat = [];
        $count = count($request->details);
        for ($i = 0; $i < $count; $i++) {
            $dataFormat[$i] = [
                'lc_commercial_invoice_id' => $id,
                'product_id' => $request->details[$i]['product_id'],
                'hs_code' => $request->details[$i]['hs_code'],
                'unit_price' => $request->details[$i]['unit_price'],
                'qty' => $request->details[$i]['qty'],
                'measure_unit_id' => $request->details[$i]['measure_unit_id'],
                'gross_weight' => $request->details[$i]['gross_weight'],
                'net_weight' => $request->details[$i]['net_weight'],
                'total_amount' => $request->details[$i]['total_amount'],
            ];
        }

        return $dataFormat;
    }  

    public function getLcProductList($lc_opening_info_id)
    {
        $dataFormat = [];

        $lcData = LcOpeningSection::FindOrFail($lc_opening_info_id); //proforma_invoice_id
        if ($lcData) {

            $piProducts = LcProformaInvoiceDetails::with('get_product','get_lc')->where('lc_proforma_invoice_id',$lcData->proforma_invoice_id)->get();

            foreach ($piProducts as $key => $value)
            {
                $lc_proforma_invoice_id = $value->lc_proforma_invoice_id;

                $lc_id = $value->get_lc->id;
                $CI = LcCommercialInvoiceEntry::select('id')->where('lc_opening_info_id',$lc_id)->where('approve_status',1)->get();

                if(count($CI) > 0)
                {
                    $ci_array = [];

                    foreach ($CI as $val)
                    {
                        array_push($ci_array,$val->id);
                    }

                    $qty = DB::table('lc_commercial_invoice_details')
                        ->whereIn('lc_commercial_invoice_id', $ci_array)
                        ->where('product_id',$value->product_id)
                        ->sum('qty');

                    $qty = $value->qty - $qty;
                }
                else
                {
                    $qty = $value->qty;
                }

                $dataFormat[] = [
                    'product_id' => $value->product_id,
                    'hs_code' => '',
                    'unit_price' => $value->total_amount_taka / $value->qty, //BDT
                    'qty' =>  $qty,//need to modify based on other CI
                    'measure_unit_id' => $value->get_product->measure_unit_id,
                    'gross_weight' => '',
                    'net_weight' => '',
                    'total_amount' =>  $value->total_amount_taka,
                ];
            }
        }

        return $dataFormat;
    }

    public function getCiProductList($lc_commercial_invoice_id)
    {
        $details = LcCommercialInvoiceDetails::where('lc_commercial_invoice_id',$lc_commercial_invoice_id)->get();

        return $details; 
    }


    public function approve($id)
    {
        $response = LcCommercialInvoiceEntry::FindOrFail($id);
        if ($response->approve_status == 0) {

            $response->approve_status = 1;
            $response->approve_by = Auth::user()->id;
            $response->approve_at = Carbon::now();
            $response->save();
        }
    }      
}
