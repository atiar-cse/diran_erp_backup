<?php

namespace App\Http\Controllers\LcImport;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\LC\LcCustomDutyCostNameEntry;
use Illuminate\Support\Facades\Auth;
use DB;
use App\Model\Accounts\AccountsChartofAccounts;

class LcCustomDutyCostNameEntryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            //return LcCustomDutyCostNameEntry::with('get_chart_acc')->orderBy('id','desc')->paginate($request->perPage);
            $query = DB::table('lc_custom_duty_cost_name_entries')
                ->leftJoin('accounts_chartof_accounts', 'lc_custom_duty_cost_name_entries.chart_ac_id', '=', 'accounts_chartof_accounts.id')
                ->select(['lc_custom_duty_cost_name_entries.id AS id',
                    'lc_custom_duty_cost_name_entries.duty_cost_name',
                    'lc_custom_duty_cost_name_entries.status',
                    'accounts_chartof_accounts.chart_of_accounts_title',
                    'accounts_chartof_accounts.chart_of_account_code',
                ]);

            return datatables()->of($query)->toJson();

        }
        $chartofAccountList = AccountsChartofAccounts::where('status', '1')
                                                        ->select('id','chart_of_accounts_title','chart_of_account_code')
                                                        ->get();
        return view('lc.lc_setup.lc_custom_duty_cost_name_entry',compact('chartofAccountList'));
    }

    public function create()
    {
        
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'duty_cost_name'=>'required|unique:lc_custom_duty_cost_name_entries,duty_cost_name',
            'chart_ac_id'=>'required',
            'status'=>'required',
        ],  [
                'chart_ac_id.required' => 'The Chart Of Accounts field is required.'
        ]);

        $input = $request->all();
        $input['created_by'] = Auth::user()->id;

        try {
            DB::beginTransaction();

            LcCustomDutyCostNameEntry::create($input);

            DB::commit();
            $bug = 0;
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'LC Custom Duty Cost Name successfully saved !']);
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
        return LcCustomDutyCostNameEntry::FindOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'duty_cost_name'=>'required|unique:lc_custom_duty_cost_name_entries,duty_cost_name,'.$id,
            'status'=>'required',
            'chart_ac_id'=>'required',
        ],  [
                'chart_ac_id.required' => 'The Chart Of Accounts field is required.'
        ]);

        $data = LcCustomDutyCostNameEntry::findOrFail($id);
        $input = $request->all();
        $input['updated_by'] = Auth::user()->id;

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
            return response()->json(['status' => 'success', 'message' => 'LC Custom Duty Cost Name successfully Updated !']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    public function destroy($id)
    {
        $data = LcCustomDutyCostNameEntry::FindOrFail($id);
        try{
            $data->delete();
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'LC Custom Duty Cost Name successfully Deleted !']);
        } elseif ($bug == 1451) {
            return response()->json(['status' => 'error', 'message' => 'This data is used another table,can not delete data']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }

    }   
}
