<?php

namespace App\Http\Controllers\LcImport;

use App\Model\Accounts\AccountsChartofAccounts;
use App\Model\LC\LcWorkOrderImport;
use App\Model\LC\LcWorkOrderImportDetails;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Model\Currency;
use App\Model\Purchase\PurchaseSupplierEntry;
use App\Model\Production\ProductionProducts;
use DB;

class WorkOrderImportController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = DB::table('lc_work_order_imports')
                ->leftJoin('purchase_supplier_entries', 'lc_work_order_imports.supplier_id', '=', 'purchase_supplier_entries.id')
                ->leftJoin('users as ura', 'lc_work_order_imports.created_by','=','ura.id')
                ->leftJoin('users as ured', 'lc_work_order_imports.updated_by','=','ured.id')
                ->leftJoin('users as urea', 'lc_work_order_imports.approve_by','=','urea.id')
                ->whereNull('lc_work_order_imports.deleted_at')
                ->select(['lc_work_order_imports.id AS id',
                    'lc_work_order_imports.work_order_no',
                    'lc_work_order_imports.order_date',
                    'lc_work_order_imports.total_qty',
                    'lc_work_order_imports.narration',
                    'lc_work_order_imports.total_amount_taka',
                    'lc_work_order_imports.created_by',
                    'lc_work_order_imports.updated_by',
                    'lc_work_order_imports.approve_by',
                    'lc_work_order_imports.approve_status',
                    'purchase_supplier_entries.purchase_supplier_name',
                    'ura.user_name as ad_name',
                    'ured.user_name as ed_name',
                    'urea.user_name as ap_name'
                ]);

            return datatables()->of($query)->toJson();

        }

        $coaLists = AccountsChartofAccounts::where('sub_code2_id', '>=',1)->where('sub_code2_id', '<=',74)->where('status',1)->orderBy('chart_of_accounts_title','ASC')->get();
        $supplierLists = PurchaseSupplierEntry::where('status',1)->where('is_importer',1)->selectRaw('id,purchase_supplier_name')->get();
        $productLists = ProductionProducts::where('status',1)->selectRaw('id,product_name,product_code')->get();
        $currecyLists = Currency::where('status',1)->selectRaw('id,name,symbol')->get();
        return view('lc.lc_section.work_order_import',compact('supplierLists','productLists','currecyLists','coaLists'));
    }

    public function workOrderNoGenerate(){
        $id = LcWorkOrderImport::withTrashed()->count();
        $currentId = $id+1;
        return 'WO-'.date('Ym').$currentId;
    }


    public function store(Request $request)
    {
        $this->validate($request, [
            'work_order_no' => 'required|alpha_dash|unique:lc_work_order_imports,work_order_no',
            'supplier_id' => 'required',
            'order_date' => 'required',
            'details.*.product_id' => 'required',
            'details.*.currency_id' => 'required',
            'details.*.unit_price' => 'required|numeric',
            'details.*.qty' => 'required|numeric',
            'details.*.bdt_convert_rate' => 'required|numeric',
        ], [
            'work_order_no.alpha_dash' => 'The Work Order No. field may only contain letters, numbers, dashes and underscores.',
            'supplier_id.required' => 'The Supplier field is required.',
            'details.*.product_id.required' => 'Required field.',
            'details.*.currency_id.required' => 'Required field.',
            'details.*.unit_price.required' => 'Required field.',
            'details.*.qty.required' => 'Required field.',
            'details.*.bdt_convert_rate.required' => 'Required field.',
        ]);

        $data = [
            'work_order_no' => $request->work_order_no,
            'supplier_id' => $request->supplier_id,
            'coa_id' => $request->coa_id,
            'order_date' => dateConvertFormtoDB($request->order_date),
            'narration' => $request->narration,
            'total_qty' => $request->total_qty,
            'total_amount' => $request->total_amount,
            'total_amount_taka' => $request->total_amount_taka,
            'created_by' => Auth::user()->id,
        ];

        $approval = $request->approve;

        try {
            DB::beginTransaction();

            $result = LcWorkOrderImport::create($data);
            $details = $this->dataFormat($request, $result->id);
            LcWorkOrderImportDetails::insert($details);

            if($approval ==1){
                $this->approve($result->id);

                if($request->updated_by == Null){
                    DB::table('lc_work_order_imports')->where('id', $result->id)->update(array('updated_by' => Auth::user()->id));
                }
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Work Order Import Successfully saved!']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    public function show($id)
    {
        try {
            $editModeData = LcWorkOrderImport::with('get_supplier','get_coa')->FindOrFail($id);
            $editModeDetailsData = LcWorkOrderImportDetails::with('get_product','get_currency')
                ->where('work_order_import_id',$id)->get();
            $data = [
                'work_order_no' => $editModeData->work_order_no,
                'get_supplier' => $editModeData->get_supplier,
                'get_coa' => $editModeData->get_coa,
                'order_date' => dateConvertDBtoForm($editModeData->order_date),
                'narration' => $editModeData->narration,
                'total_qty' => $editModeData->total_qty,
                'total_amount' => $editModeData->total_amount,
                'total_amount_taka' => $editModeData->total_amount_taka,
                'details'    => $editModeDetailsData
            ];
            return response()->json(['status'=>'success','data'=>$data]);
        } catch(\Exception $e){
            return response()->json(['status'=>'error','data'=>[]]);
        }
    }

    public function edit($id)
    {
        try {
            $editModeData = LcWorkOrderImport::FindOrFail($id);
            $editModeDetailsData = LcWorkOrderImportDetails::where('work_order_import_id',$id)->get();
            $data = [
                'id'    => $editModeData->id,
                'work_order_no' => $editModeData->work_order_no,
                'supplier_id' => $editModeData->supplier_id,
                'coa_id' => $editModeData->coa_id,
                'order_date' => dateConvertDBtoForm($editModeData->order_date),
                'narration' => $editModeData->narration,
                'total_qty' => $editModeData->total_qty,
                'total_amount' => $editModeData->total_amount,
                'total_amount_taka' => $editModeData->total_amount_taka,
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
            'work_order_no' => 'required|alpha_dash|unique:lc_work_order_imports,work_order_no,'.$id,
            'supplier_id' => 'required',
            'order_date' => 'required',
            'details.*.product_id' => 'required',
            'details.*.currency_id' => 'required',
            'details.*.unit_price' => 'required|numeric',
            'details.*.qty' => 'required|numeric',
            'details.*.bdt_convert_rate' => 'required|numeric',
        ], [
            'work_order_no.alpha_dash' => 'The Work Order No. field may only contain letters, numbers, dashes and underscores.',
            'supplier_id.required' => 'The Supplier field is required.',
            'details.*.product_id.required' => 'Required field.',
            'details.*.currency_id.required' => 'Required field.',
            'details.*.unit_price.required' => 'Required field.',
            'details.*.qty.required' => 'Required field.',
            'details.*.bdt_convert_rate.required' => 'Required field.',
        ]);

        $approval = $request->approve;

        try {
            DB::beginTransaction();


            $editModeData = LcWorkOrderImport::FindOrFail($id);

            $editModeData->work_order_no     = $request->work_order_no;
            $editModeData->supplier_id       = $request->supplier_id;
            $editModeData->coa_id            = $request->coa_id;
            $editModeData->order_date        = dateConvertFormtoDB($request->order_date);
            $editModeData->narration         = $request->narration;
            $editModeData->total_qty         = $request->total_qty;
            $editModeData->total_amount      = $request->total_amount;
            $editModeData->total_amount_taka = $request->total_amount_taka;

            if($approval !=1){
                $editModeData->updated_by = Auth::user()->id;
            }

            $editModeData->save();
            /* Insert update and delete work-order-import details  */

            if (count($request->deleteID) > 0) {
                LcWorkOrderImportDetails::whereIn('id', $request->deleteID)->delete();
            }
            $dataFormat = [];
            $count = count($request->details);
            for ($i = 0; $i < $count; $i++) {
                if (isset($request->details[$i]['id']) && $request->details[$i]['id'] !='') {
                    $updateData = [
                        'work_order_import_id' => $id,
                        'product_id' => $request->details[$i]['product_id'],
                        'remarks' => $request->details[$i]['remarks'],
                        'currency_id' => $request->details[$i]['currency_id'],
                        'unit_price' => $request->details[$i]['unit_price'],
                        'qty' => $request->details[$i]['qty'],
                        'total_price' => $request->details[$i]['total_price'],
                        'bdt_convert_rate' => $request->details[$i]['bdt_convert_rate'],
                        'total_amount_taka' => $request->details[$i]['total_amount_taka'],
                    ];
                    LcWorkOrderImportDetails::where('id', $request->details[$i]['id'])->update($updateData);
                } else {
                    $dataFormat[$i] =[
                        'work_order_import_id' => $id,
                        'product_id' => $request->details[$i]['product_id'],
                        'remarks' => $request->details[$i]['remarks'],
                        'currency_id' => $request->details[$i]['currency_id'],
                        'unit_price' => $request->details[$i]['unit_price'],
                        'qty' => $request->details[$i]['qty'],
                        'total_price' => $request->details[$i]['total_price'],
                        'bdt_convert_rate' => $request->details[$i]['bdt_convert_rate'],
                        'total_amount_taka' => $request->details[$i]['total_amount_taka'],
                    ];
                }
            }

            if(count($dataFormat) > 0){
                LcWorkOrderImportDetails::insert($dataFormat);
            }

            /* Approval */
            if($approval ==1){
                $this->approve($id);                
            }

            DB::commit();
            $bug = 0;
            return response()->json(['status' => 'success', 'message' => 'Work Order Import successfully updated!']);
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->errorInfo[1];
            if ($bug = 1062) {
                return response()->json(['status' => 'error', 'message' => 'Work Order No is Found Duplicate']);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
            }
        }
    }

    public function destroy($id)
    {
        try{

            LcWorkOrderImportDetails::where('work_order_import_id',$id)->delete();
            LcWorkOrderImport::FindOrFail($id)->delete();

            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->errorInfo[1];
        }


        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Work Order Import successfully Deleted !']);
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
                'work_order_import_id' => $id,
                'product_id' => $request->details[$i]['product_id'],
                'remarks' => $request->details[$i]['remarks'],
                'currency_id' => $request->details[$i]['currency_id'],
                'unit_price' => $request->details[$i]['unit_price'],
                'qty' => $request->details[$i]['qty'],
                'total_price' => $request->details[$i]['total_price'],
                'bdt_convert_rate' => $request->details[$i]['bdt_convert_rate'],
                'total_amount_taka' => $request->details[$i]['total_amount_taka'],
            ];
        }

        return $dataFormat;
    }  

    public function getProductList($work_order_import_id)
    {
        $details = LcWorkOrderImportDetails::where('work_order_import_id',$work_order_import_id)->get();
        return $details; 
    } 

    public function approve($id)
    {
        $lc = LcWorkOrderImport::Find($id);
        if ($lc->approve_status == 0) {

            $lc->approve_status = 1;
            $lc->approve_by = Auth::user()->id;
            $lc->approve_at = Carbon::now();
            $lc->save();
        }
    }         
}
