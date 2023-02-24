<?php

namespace App\Http\Controllers\Accounts\ChartOfAccounts;

use App\Http\Controllers\HR\Employee\BranchController;
use App\Model\Accounts\AccountsChartofAccounts;
use App\Model\Accounts\AccountsSubCode2;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use DB;

class ChartOfAccountsController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = DB::table('accounts_chartof_accounts')
                ->leftJoin('accounts_sub_code2s', 'accounts_chartof_accounts.sub_code2_id', '=', 'accounts_sub_code2s.id')
                ->leftJoin('accounts_sub_codes', 'accounts_sub_code2s.sub_code_id', '=', 'accounts_sub_codes.id')
                ->leftJoin('accounts_main_codes', 'accounts_sub_codes.main_code_id', '=', 'accounts_main_codes.id')
                ->leftJoin('users as ura', 'accounts_chartof_accounts.created_by','=','ura.id')
                ->leftJoin('users as ured', 'accounts_chartof_accounts.updated_by','=','ured.id')
                ->orderBy('accounts_chartof_accounts.chart_of_accounts_title','ASC')
                ->whereNull('accounts_chartof_accounts.deleted_at')
                ->select(['accounts_chartof_accounts.id AS id',
                    'accounts_chartof_accounts.chart_of_accounts_title',
                    'accounts_chartof_accounts.chart_of_account_code',
                    'accounts_chartof_accounts.opening_balance',
                    'accounts_chartof_accounts.current_balance',
                    'accounts_chartof_accounts.opening_date',
                    'accounts_chartof_accounts.created_by',
                    'accounts_chartof_accounts.updated_by',
                    'accounts_chartof_accounts.status',
                    'accounts_sub_code2s.sub_code_title2','accounts_sub_code2s.sub_code2',
                    'accounts_sub_codes.sub_code_title','accounts_sub_codes.sub_code',
                    'accounts_main_codes.main_code_title','accounts_main_codes.main_code',
                    'ura.user_name as ad_name',
                    'ured.user_name as ed_name'
                ]);

            return datatables()->of($query)->toJson();
        }

        $subCode2List      = AccountsSubCode2::where('status', '1')->get();
        return view('accounts.chart_of_accounts.chart_of_accounts',compact('subCode2List'));
    }

    public function chart_code(Request $request){
        $sid= $request->sub_code2_id;
        $sub_code= AccountsSubCode2::where('id',$sid)->first()->sub_code2;
        $count = strlen($sub_code);
        $sub_code_two= DB::table('accounts_chartof_accounts')
            ->selectRaw("max(chart_of_account_code) as code_val")
            ->whereRaw("chart_of_account_code like '$sub_code%' ")
            ->first();

        if($sub_code_two->code_val =='' || $sub_code_two->code_val == 0){
            $val_return = $sub_code.'01';
        }
        else{
            $val= substr($sub_code_two->code_val, $count);
            $val_will = (int)$val+ 1;
            if(strlen($val_will) == 1){$val_will = '0'.$val_will;}else{$val_will = $val_will;}
            $val_return = $sub_code.$val_will;
        }
        return $val_return;
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'sub_code2_id'=>'required',
            'chart_of_accounts_title'=>'required|unique:accounts_chartof_accounts,chart_of_accounts_title',
            'chart_of_account_code'=>'required|unique:accounts_chartof_accounts,chart_of_account_code',
        ]);

        if($request->opening_balance == ''){ $balance = 0;}else{$balance = $request->opening_balance;}
        $date=$request->opening_date?dateConvertFormtoDB($request->opening_date):date('Y-m-d');
        if($request->opening_balance!='' || $request->opening_balance!=0){
            $state=1;
        }else{$state=0;}

        $data = [
            'sub_code2_id'                  => $request->sub_code2_id,

            'chart_of_accounts_title'       => $request->chart_of_accounts_title,
            'chart_of_account_code'         => $request->chart_of_account_code,

            'current_balance'               => $balance,
            'opening_date'                  => $date,
            'opening_balance'               => $balance,
            'open_bl_add_status'            => $state,
            'status'                        => $request->status,

            'created_by' => Auth::user()->id,
        ];

        try {
            DB::beginTransaction();
                $result= AccountsChartofAccounts::create($data);
            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Chart of Accounts successfully saved !']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }


    public function show($id)
    {
        //
    }


    public function edit($id)
    {
        return AccountsChartofAccounts::FindOrFail($id);
    }


    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'sub_code2_id'=>'required',
            'chart_of_accounts_title'=>'required',
            'chart_of_account_code'=>'required',
        ]);

        $data = AccountsChartofAccounts::findOrFail($id);


        if($request->opening_balance == ''){ $balance = 0;}else{$balance = $request->opening_balance;}
        $date=$request->opening_date?dateConvertFormtoDB($request->opening_date):date('Y-m-d');
        if($request->opening_balance!='' || $request->opening_balance!=0){
            $state=1;
        }else{$state=0;}


        $input['opening_date'] = dateConvertFormtoDB($request->opening_date);

        if($data->current_balance > 0)
        {
            $cur_balance= $data->current_balance;
        }
        else {
            //echo $data->current_balance;
            $cur_balance = $request->opening_balance;
        }


        $input = [
            'sub_code2_id'                  => $request->sub_code2_id,

            'chart_of_accounts_title'       => $request->chart_of_accounts_title,
            'chart_of_account_code'         => $request->chart_of_account_code,

            'current_balance'               => $cur_balance,
            'opening_date'                  => $date,
            'opening_balance'               => $balance,
            'open_bl_add_status'            => $state,
            'status'                        => $request->status,

            'updated_by'                    => Auth::user()->id,
        ];

        try {
            DB::beginTransaction();
            $data->update($input);
            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Chart of Accounts successfully Updated !']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }


    public function destroy($id)
    {
        $data = AccountsChartofAccounts ::FindOrFail($id);
        try{
            $data->delete();
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->errorInfo[1];
        }


        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Chart of Accounts successfully Deleted !']);
        } elseif ($bug == 1451) {
            return response()->json(['status' => 'error', 'message' => 'This data is used another table,can not delete data']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    public function getPrintChartOfAccount(){
        try {

            $coaDetails = DB::table('accounts_chartof_accounts AS coa')
                            ->leftJoin('accounts_sub_code2s AS sbc2','coa.sub_code2_id','=','sbc2.id')
                            ->leftJoin('accounts_sub_codes AS sbc','sbc2.sub_code_id','=','sbc.id')
                            ->leftJoin('accounts_main_codes AS amc','sbc.main_code_id','=','amc.id')
                            ->select('coa.chart_of_accounts_title','coa.chart_of_account_code','coa.sub_code2_id',
                                'sbc2.sub_code_title2','sbc2.sub_code2','sbc2.sub_code_id',
                                'sbc.main_code_id','sbc.sub_code_title','sbc.sub_code',
                                'amc.main_code_title','amc.main_code')
                            ->orderBy('coa.chart_of_account_code','asc')
                            ->orderBy('sbc2.sub_code2','asc')
                            ->orderBy('sbc.sub_code','asc')
                            ->orderBy('amc.main_code','asc')
                            ->get();

            $data =[
                'coa_details' => $coaDetails,
            ];

            return response()->json(['status'=>'success','data'=>$data]);
        } catch(\Exception $e){
            return response()->json(['status'=>'error','data'=>[]]);

        }

    }
}
