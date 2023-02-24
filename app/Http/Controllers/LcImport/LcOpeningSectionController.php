<?php

namespace App\Http\Controllers\LcImport;

use App\Model\LC\LcOpeningSection;
use App\Model\LC\LcProformaInvoiceEntry;
use App\Model\Accounts\AccountsBankName;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;
use DB;

class LcOpeningSectionController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $query = DB::table('lc_opening_sections')
                ->leftJoin('purchase_supplier_entries', 'lc_opening_sections.supplier_id', '=', 'purchase_supplier_entries.id')
                ->leftJoin('lc_proforma_invoice_entries', 'lc_opening_sections.proforma_invoice_id', '=', 'lc_proforma_invoice_entries.id')
                ->leftJoin('accounts_bank_names', 'lc_opening_sections.lc_opening_bank_id', '=', 'accounts_bank_names.id')
                ->leftJoin('users as ura', 'lc_opening_sections.created_by','=','ura.id')
                ->leftJoin('users as ured', 'lc_opening_sections.updated_by','=','ured.id')
                ->leftJoin('users as urea', 'lc_opening_sections.approve_by','=','urea.id')
                ->whereNull('lc_opening_sections.deleted_at')
                ->select(['lc_opening_sections.id AS id',
                    'lc_opening_sections.lc_no',
                    'lc_opening_sections.lc_opening_date',
                    'lc_opening_sections.lc_total_value',
                    'lc_opening_sections.lc_category',
                    'lc_opening_sections.lc_type',
                    'lc_opening_sections.created_by',
                    'lc_opening_sections.updated_by',
                    'lc_opening_sections.approve_by',
                    'lc_opening_sections.approve_status',
                    'purchase_supplier_entries.purchase_supplier_name',
                    'lc_proforma_invoice_entries.pi_no',
                    'accounts_bank_names.accounts_bank_names',
                    'ura.user_name as ad_name',
                    'ured.user_name as ed_name',
                    'urea.user_name as ap_name'
                ]);
            return datatables()->of($query)->toJson();
        }

        $opendIDs = LcOpeningSection::pluck('proforma_invoice_id');
        $piInvoiceLists = LcProformaInvoiceEntry::with('get_supplier','get_work_order')
                            ->selectRaw('id,pi_no,supplier_id,work_order_id,total_amount_taka')
                            ->whereNotIn('id',$opendIDs)
                            ->where('status',1)->where('approve_status',1)->get();
        $bankLists = AccountsBankName::where('status',1)->selectRaw('id,accounts_bank_names')->get();

        return view('lc.lc_section.lc_opening_section',compact('piInvoiceLists','bankLists'));
    }

    public function lcNoGenerate(){
        $id = LcOpeningSection::withTrashed()->count();
        $currentId = $id+1;
        return 'LC-'.date('Ym').$currentId;
    }

    public function create()
    {
        
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'lc_no'=>'required|alpha_dash|unique:lc_opening_sections,lc_no',
            'proforma_invoice_id'=>'required|unique:lc_opening_sections,proforma_invoice_id',
            'lc_category'=>'required',
            'lc_type'=>'required',
            'lc_opening_date'=>'required',

            'lc_opening_bank_id'=>'required',
            'lc_bank_expenses'=>'required',

        ],  [
            'lc_no.alpha_dash' => 'The LC No. field may only contain letters, numbers, dashes and underscores.',
            'proforma_invoice_id.required' => 'The Proforma Invoice No field is required.',
            'proforma_invoice_id.unique' => 'The Proforma Invoice No field has already been taken.',
            'lc_opening_bank_id.required' => 'The LC Opening Bank field is required.',
        ]);

        $input = $request->all(); 
        $input['lc_opening_date'] = dateConvertFormtoDB($request->lc_opening_date);
        $input['lc_latest_shipment_date'] = dateConvertFormtoDB($request->lc_latest_shipment_date);
        $input['lc_expire_date'] = dateConvertFormtoDB($request->lc_expire_date);
        $input['created_by'] = Auth::user()->id;

        $approval = $request->approve;

        try {
            DB::beginTransaction();

            $lcResult = LcOpeningSection::create($input);

            if($approval ==1){
                $this->approve($lcResult->id);
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'LC Opening Section successfully saved!']);
        } catch (\Exception $e) {
            print_r($e->getMessage());
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function show($id)
    {        
        try {
            $editModeData = LcOpeningSection::with('get_supplier','get_pi_no','get_opening_bank','get_baneficiary_bank')
                ->FindOrFail($id);
         
            return response()->json(['status'=>'success','data'=>$editModeData]);
        } catch(\Exception $e){
            return response()->json(['status'=>'error','data'=>[]]);
        }        
    }

    public function edit($id)
    {        
        try {
            $editModeData = LcOpeningSection::with('get_supplier')->FindOrFail($id);

            $data = [
                'id'    => $editModeData->id,
                'lc_no' => $editModeData->lc_no,
                'supplier_id' => $editModeData->supplier_id,
                'supplier_info_display' =>  $editModeData->get_supplier ? $editModeData->get_supplier->purchase_supplier_name  : '',
                'proforma_invoice_id' => $editModeData->proforma_invoice_id,
                'lc_category' => $editModeData->lc_category,
                'lc_type' => $editModeData->lc_type,
                'lc_opening_date' => dateConvertDBtoForm($editModeData->lc_opening_date),
                'lc_opening_charges' => $editModeData->lc_opening_charges,
                'lc_opening_bank_id' => $editModeData->lc_opening_bank_id,
                'lc_bank_expenses' => $editModeData->lc_bank_expenses,
                'baneficiary_bank' => $editModeData->baneficiary_bank,
                'lc_latest_shipment_date' => dateConvertDBtoForm($editModeData->lc_latest_shipment_date),
                'lc_expire_date' => dateConvertDBtoForm($editModeData->lc_expire_date),
                'lc_total_value' => $editModeData->lc_total_value,

                'lc_status' => $editModeData->lc_status,
                'status' => $editModeData->status,
                'amount' => $editModeData->lc_opening_charges+$editModeData->lc_bank_expenses,
            ];

            return response()->json(['status'=>'success','data'=>$data]);
        } catch(\Exception $e){
            return response()->json(['status'=>'error','data'=>[]]);
        }        
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'lc_no'=>'required|unique:lc_opening_sections,lc_no,'.$id,
            'proforma_invoice_id'=>'required|unique:lc_opening_sections,proforma_invoice_id,'.$id,
            'lc_category'=>'required',
            'lc_type'=>'required',
            'lc_opening_date'=>'required',
            'lc_opening_bank_id'=>'required',
            'lc_bank_expenses'=>'required',

        ],  [
            'lc_no.alpha_dash' => 'The LC No. field may only contain letters, numbers, dashes and underscores.',
            'proforma_invoice_id.required' => 'The Proforma Invoice No field is required.',
            'proforma_invoice_id.unique' => 'The Proforma Invoice No field has already been taken.',
            'lc_opening_bank_id.required' => 'The LC Opening Bank field is required.',
        ]);

        $input = $request->all(); 
        $input['lc_opening_date'] = dateConvertFormtoDB($request->lc_opening_date);
        $input['lc_latest_shipment_date'] = dateConvertFormtoDB($request->lc_latest_shipment_date);
        $input['lc_expire_date'] = dateConvertFormtoDB($request->lc_expire_date);

        $approval = $request->approve;

        if($approval !=1){
            $input['updated_by'] = Auth::user()->id;
        }

        try {
            DB::beginTransaction();

            $editModeData = LcOpeningSection::FindOrFail($id);
            $editModeData->update($input);

            if($approval == 1)
            {
                $this->approve($id);
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'LC Opening Section successfully updated!']);
        } catch (\Exception $e) {
            DB::rollback();
            print_r($e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Something Went Wrong!!!']);
        }
    }

    public function destroy($id)
    {
        try{
            LcOpeningSection::FindOrFail($id)->delete();

            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'LC Opening Section successfully Deleted !']);
        } elseif ($bug == 1451) {
            return response()->json(['status' => 'error', 'message' => 'This data is used another table,can not delete data']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    public function approve($id)
    {
        $lc = LcOpeningSection::Find($id);
        if ($lc->approve_status == 0) {

            $lc->approve_status = 1;
            $lc->approve_by = Auth::user()->id;
            $lc->approve_at = Carbon::now();
            $lc->save();
        }
    } 

}
