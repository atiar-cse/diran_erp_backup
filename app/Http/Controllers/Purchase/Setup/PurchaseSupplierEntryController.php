<?php

namespace App\Http\Controllers\Purchase\Setup;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Purchase\PurchaseSupplierEntry;

use App\Model\Accounts\AccountsSubCode2;
use App\Model\Accounts\AccountsChartofAccounts;

use Illuminate\Support\Facades\Auth;
use DB;

class PurchaseSupplierEntryController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = DB::table('purchase_supplier_entries')
                ->leftJoin('accounts_chartof_accounts', 'purchase_supplier_entries.chart_ac_id', '=', 'accounts_chartof_accounts.id')
                ->leftJoin('users as ura', 'purchase_supplier_entries.created_by','=','ura.id')
                ->leftJoin('users as ured', 'purchase_supplier_entries.updated_by','=','ured.id')
                ->whereNull('purchase_supplier_entries.deleted_at')
                ->select(['purchase_supplier_entries.id AS id',
                    'purchase_supplier_entries.purchase_supplier_name',
                    'purchase_supplier_entries.purchase_supplier_contact_person_name',
                    'purchase_supplier_entries.purchase_supplier_business_phone',
                    'purchase_supplier_entries.purchase_supplier_mobile',
                    'purchase_supplier_entries.purchase_supplier_office_address',
                    'purchase_supplier_entries.created_by',
                    'purchase_supplier_entries.updated_by',
                    'purchase_supplier_entries.status',
                    'purchase_supplier_entries.is_importer',
                    'accounts_chartof_accounts.chart_of_accounts_title',
                    'accounts_chartof_accounts.chart_of_account_code',
                    'ura.user_name as ad_name',
                    'ured.user_name as ed_name'
                ]);

            return datatables()->of($query)->toJson();
        }
        $chat_lists = AccountsChartofAccounts::where('status','1')->where('chart_of_account_code','like', '4301%')->get();
        return view('purchase.purchase_setup.purchase_supplier_entry',compact('chat_lists'));
    }

    public function create()
    {

    }


    public function store(Request $request)
    {
        date_default_timezone_set("Asia/Dhaka");
        $this->validate($request, [
            'purchase_supplier_name'=>'required|unique:purchase_supplier_entries,purchase_supplier_name',
            'chart_ac_id'=>'required',
            'purchase_supplier_contact_person_name'=>'required',
            'purchase_supplier_business_phone'=>'required',
            'purchase_supplier_mobile'=>'required',
            'purchase_supplier_email' =>'nullable|email',
            'is_importer'=>'required',
            'status'=>'required',
        ]);

        $input = $request->all();
        $input['created_by'] = Auth::user()->id;

        $purchase_supplier_id = AccountsChartofAccounts::where('id',$request->chart_ac_id)->first()->chart_of_account_code;

        $date = str_replace('/', '-', $request->purchase_supplier_join_date);
        $date_val =date('Y-m-d', strtotime($date));
        $input['purchase_supplier_join_date'] = $date_val;
        $input['purchase_supplier_id'] = $purchase_supplier_id;


        try {
            DB::beginTransaction();

            /*$find_chart_acc=AccountsChartofAccounts::where('chart_of_account_code', $request->purchase_supplier_id)->first();
            if($find_chart_acc!=''){
                $input['chart_ac_id'] = $find_chart_acc->id;
            }
            else{
                $sub = substr($request->purchase_supplier_id, 0, 4);
                $sub_code= AccountsSubCode2::where('sub_code2', $sub)->first();
                $data = [
                    'sub_code2_id' => $sub_code->id,
                    'chart_of_accounts_title' => $request->purchase_supplier_name,
                    'chart_of_account_code' => $request->purchase_supplier_id,
                    'opening_date' => date('Y-m-d'),
                    'created_by' => Auth::user()->id,
                ];
                $cid= AccountsChartofAccounts::create($data);
                $input['chart_ac_id'] = $cid->id;
            }*/

            PurchaseSupplierEntry::create($input);

            DB::commit();
            $bug = 0;
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Purchase Supplier Entry successfully saved !']);
        } elseif ($bug == 1062) {
            return response()->json(['status' => 'error', 'message' => 'Purchase Supplier Entry is Found Duplicate']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }


    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $editModeData= PurchaseSupplierEntry::findOrFail($id);
        $data = ['id' =>$editModeData->id,
            'purchase_supplier_name' =>$editModeData->purchase_supplier_name ,
            'chart_ac_id' =>$editModeData->chart_ac_id,
            'purchase_supplier_join_date' => date('d/m/Y', strtotime($editModeData->purchase_supplier_join_date)),
            'purchase_supplier_contact_person_name' =>$editModeData->purchase_supplier_contact_person_name ,
            'purchase_supplier_contact_person_job_title' =>$editModeData->purchase_supplier_contact_person_job_title,
            'purchase_supplier_business_phone' =>$editModeData->purchase_supplier_business_phone ,
            'purchase_supplier_mobile' =>$editModeData->purchase_supplier_mobile ,
            'purchase_supplier_email' =>$editModeData->purchase_supplier_email,
            'purchase_supplier_web_address' =>$editModeData->purchase_supplier_web_address,
            'purchase_supplier_credit_limit' =>$editModeData->purchase_supplier_credit_limit,
            'purchase_supplier_office_address' =>$editModeData->purchase_supplier_office_address,
            'purchase_supplier_narration' =>$editModeData->purchase_supplier_narration,
            'is_importer' =>$editModeData->is_importer,
            'status' =>$editModeData->status
        ];
        return $data;
    }

    public function update(Request $request, $id)
    {
        date_default_timezone_set("Asia/Dhaka");
        $this->validate($request, [
            'purchase_supplier_name'=>'required',
            'chart_ac_id'=>'required',
            'purchase_supplier_contact_person_name'=>'required',
            'purchase_supplier_business_phone'=>'required',
            'purchase_supplier_mobile'=>'required',
            'purchase_supplier_email' =>'nullable|email',
            'is_importer'=>'required',
            'status'=>'required',
        ]);

        $input = $request->all();

        $purchase_supplier_id = AccountsChartofAccounts::where('id',$request->chart_ac_id)->first()->chart_of_account_code;
        //echo $purchase_supplier_id;

        $date = str_replace('/', '-', $request->purchase_supplier_join_date);
        $date_val =date('Y-m-d', strtotime($date));
        $input['purchase_supplier_join_date'] = $date_val;
        $input['updated_by'] = Auth::user()->id;

        $input['purchase_supplier_id'] = $purchase_supplier_id;

        $data = PurchaseSupplierEntry::findOrFail($id);

        /*if($data->chart_ac_id == ''){
            $find_chart_acc=AccountsChartofAccounts::where('chart_of_account_code' , $request->purchase_supplier_id)->first();
            if($find_chart_acc!=''){
                $input['chart_ac_id'] = $find_chart_acc->id;
            }
            else{
                $subn = substr($request->purchase_supplier_id, 0, 4);
                $sub_coden= AccountsSubCode2::where('sub_code2', $subn)->first();
                $datan = [
                    'sub_code2_id' => $sub_coden->id,
                    'chart_of_accounts_title' => $request->purchase_supplier_name,
                    'chart_of_account_code' => $request->purchase_supplier_id,
                    'opening_date' => date('Y-m-d'),
                    'created_by' => Auth::user()->id,
                ];
                $cid= AccountsChartofAccounts::create($datan);
                $input['chart_ac_id'] = $cid->id;
            }
        } else{
            $find_chart_acc_ed=AccountsChartofAccounts::where('chart_of_account_code',$data->purchase_supplier_id)->first();
            if($find_chart_acc_ed == ''){
                $sub_ed = substr($request->purchase_supplier_id, 0, 4);
                $sub_code_ed= AccountsSubCode2::where('sub_code2',$sub_ed)->first();
                $datae = [
                    'sub_code2_id' => $sub_code_ed->id,
                    'chart_of_accounts_title' => $request->purchase_supplier_name,
                    'chart_of_account_code' => $request->purchase_supplier_id,
                    'opening_date' => date('Y-m-d'),
                    'created_by' => Auth::user()->id,
                ];
                $cid_ed= AccountsChartofAccounts::create($datae);
                $input['chart_ac_id'] = $cid_ed->id;
            }else{
                if($data->chart_ac_id != $find_chart_acc_ed->id){
                    $input['chart_ac_id'] = $find_chart_acc_ed->id;
                }
            }

        }*/


        //dd($input);
        try {
            DB::beginTransaction();
                $data->update($input);
            DB::commit();
            $bug = 0;
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Purchase Supplier Entry successfully Updated !']);
        } elseif ($bug == 1062) {
            return response()->json(['status' => 'error', 'message' => 'Purchase Supplier Entry is Found Duplicate']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    public function destroy($id)
    {
        $data = PurchaseSupplierEntry ::FindOrFail($id);
        try{

            //DELETE FROM CUSTOMERS WHERE ID = 6;

            DB::table('accounts_chartof_accounts')->where('id', $data->chart_ac_id)->delete();
            DB::table('purchase_supplier_entries')->where('id', $id)->delete();

            $data->delete();
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Purchase Supplier Entry successfully Deleted !']);
        } elseif ($bug == 1451) {
            return response()->json(['status' => 'error', 'message' => 'This data is used another table,can not delete data']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }
}
