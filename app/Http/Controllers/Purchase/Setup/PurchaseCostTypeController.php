<?php

namespace App\Http\Controllers\Purchase\Setup;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Model\Purchase\PurchaseCostType;
use App\Model\Accounts\AccountsSubCode2;
use App\Model\Accounts\AccountsChartofAccounts;

use Illuminate\Support\Facades\Auth;
use DB;

class PurchaseCostTypeController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            //return PurchaseCostType::orderBy('id', 'desc')->paginate($request->perPage);

            $query = DB::table('purchase_cost_types')
                ->leftJoin('accounts_chartof_accounts', 'purchase_cost_types.chart_ac_id', '=', 'accounts_chartof_accounts.id')
                ->leftJoin('users', 'purchase_cost_types.created_by', '=', 'users.id')
                ->whereNull('purchase_cost_types.deleted_at')
                ->select(['purchase_cost_types.id AS id',
                    'purchase_cost_types.purchase_cost_types_name',
                    'purchase_cost_types.cost_types_code',
                    'purchase_cost_types.status',
                    'purchase_cost_types.created_by',
                    'purchase_cost_types.updated_by',
                    'accounts_chartof_accounts.chart_of_accounts_title','accounts_chartof_accounts.chart_of_account_code',
                    'users.user_name',
                ]);

            return datatables()->of($query)->toJson();
        }
        /*$chat_lists = AccountsChartofAccounts::where('status','1')->where('chart_of_account_code','like', '21%')->get();*/
        $chat_lists = AccountsChartofAccounts::where('status','1')->get();

        return view('purchase.purchase_setup.purchase_cost_type',compact('chat_lists'));
    }


    public function create()
    {

    }


    public function store(Request $request)
    {
        $this->validate($request, [
            'purchase_cost_types_name'  =>'required|unique:purchase_cost_types,purchase_cost_types_name',
            'chart_ac_id'               =>'required',
            'status'                    =>'required',
        ]);

        $input = $request->all();
        $cost_types_code = AccountsChartofAccounts::where('id',$request->chart_ac_id)->first()->chart_of_account_code;
        $input['created_by'] = Auth::user()->id;
        $input['cost_types_code'] = $cost_types_code;

        try {
            DB::beginTransaction();

            /*$find_chart_acc=AccountsChartofAccounts::where('chart_of_account_code', $request->cost_types_code)->first();
            if($find_chart_acc!=''){
                $input['chart_ac_id'] = $find_chart_acc->id;
            }
            else{
                $sub = substr($request->cost_types_code, 0, 4);
                $sub_code= AccountsSubCode2::where('sub_code2', $sub)->first();
                $data = [
                    'sub_code2_id' => $sub_code->id,
                    'chart_of_accounts_title' => $request->purchase_cost_types_name,
                    'chart_of_account_code' => $request->cost_types_code,
                    'opening_date' => date('Y-m-d'),
                    'created_by' => Auth::user()->id,
                ];
                $cid= AccountsChartofAccounts::create($data);
                $input['chart_ac_id'] = $cid->id;
            }*/

            PurchaseCostType::create($input);

            DB::commit();
            $bug = 0;
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Purchase Cost Type successfully saved !']);
        } elseif ($bug == 1062) {
            return response()->json(['status' => 'error', 'message' => 'Purchase Cost Type is Found Duplicate']);
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
        return PurchaseCostType::findOrFail($id);
    }


    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'purchase_cost_types_name'  =>'required',
            'chart_ac_id'               =>'required',
            'status'                    =>'required',
        ]);

        $data = PurchaseCostType::findOrFail($id);
        $input = $request->all();
        $cost_types_code = AccountsChartofAccounts::where('id',$request->chart_ac_id)->first()->chart_of_account_code;
        $input['updated_by'] = Auth::user()->id;
        $input['cost_types_code'] = $cost_types_code;


        /*if($data->chart_ac_id == ''){
            $find_chart_acc=AccountsChartofAccounts::where('chart_of_account_code' , $request->cost_types_code)->first();
            if($find_chart_acc!=''){
                $input['chart_ac_id'] = $find_chart_acc->id;
            }
            else{
                $subn = substr($request->cost_types_code, 0, 4);
                $sub_coden= AccountsSubCode2::where('sub_code2', $subn)->first();
                $datan = [
                    'sub_code2_id' => $sub_coden->id,
                    'chart_of_accounts_title' => $request->purchase_cost_types_name,
                    'chart_of_account_code' => $request->cost_types_code,
                    'opening_date' => date('Y-m-d'),
                    'created_by' => Auth::user()->id,
                ];
                $cid= AccountsChartofAccounts::create($datan);
                $input['chart_ac_id'] = $cid->id;
            }
        } else{
            $find_chart_acc_ed=AccountsChartofAccounts::where('chart_of_account_code',$data->cost_types_code)->first();
            if($find_chart_acc_ed == ''){
                $sub_ed = substr($request->cost_types_code, 0, 4);
                $sub_code_ed= AccountsSubCode2::where('sub_code2',$sub_ed)->first();
                $datae = [
                    'sub_code2_id' => $sub_code_ed->id,
                    'chart_of_accounts_title' => $request->purchase_cost_types_name,
                    'chart_of_account_code' => $request->cost_types_code,
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
            return response()->json(['status' => 'success', 'message' => 'Purchase Cost Type successfully Updated !']);
        } elseif ($bug == 1062) {
            return response()->json(['status' => 'error', 'message' => 'Purchase Cost Type is Found Duplicate']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }


    public function destroy($id)
    {
        $data = PurchaseCostType ::FindOrFail($id);
        try{
            $data->delete();
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->errorInfo[1];
        }


        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Purchase Cost Type successfully Deleted !']);
        } elseif ($bug == 1451) {
            return response()->json(['status' => 'error', 'message' => 'This data is used another table,can not delete data']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }
}
