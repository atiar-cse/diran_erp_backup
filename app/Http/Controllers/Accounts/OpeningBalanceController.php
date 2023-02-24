<?php

namespace App\Http\Controllers\Accounts;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Model\Accounts\AccountsMainCode;
use App\Model\Accounts\AccountsChartofAccounts;

use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use DB;

class OpeningBalanceController extends Controller
{

    public function index()
    {
        $mainLists      = AccountsMainCode::where('status', '1')
            ->where('open_status', '1')
            ->get();
        $chartAcLists   = AccountsChartofAccounts::where('status', '1')->get();
        return view('accounts.accounts_section.opening_balance',compact( 'mainLists','chartAcLists'));
    }


    public function load_chart_ac($main_id)
    {

        //DB::enableQueryLog();
        $main_value   = AccountsMainCode::where('id', $main_id)->first();
        $chartacList = Db::select("SELECT * FROM accounts_chartof_accounts where SUBSTRING(chart_of_account_code, 1, 1) = $main_value->main_code and open_bl_add_status=0");
        //DB::table('accounts_chartof_accounts')->whereRaw('SUBSTRING(chart_of_account_code, 1, 1)',$main_value->main_code)->get();
        //print_r(DB::getQueryLog($chartacList));

        $dataFormat = [];
        foreach ($chartacList as $key => $value) {
            $dataFormat[] = [
                'id' => $value->id,
                'chart_of_accounts_title' => $value->chart_of_accounts_title,
                'chart_of_account_code' => $value->chart_of_account_code,
                'opening_balance' => $value->opening_balance,
            ];
        }

        return $dataFormat;
    }


    public function create()
    {

    }


    public function store(Request $request)
    {
        $this->validate($request, [
            'main_code_id' => 'required',
            'acc.*.opening_balance' => 'required',
        ], [
            'acc.*.opening_balance.required' => 'Required field.',
        ]);


        try {
            DB::beginTransaction();
            $dataFormat = [];
            $count = count($request->acc);
            for ($i = 0; $i < $count; $i++) {
                if (isset($request->acc[$i]['id']) && $request->acc[$i]['opening_balance'] !=0) {
                    if($request->acc[$i]['opening_balance']!='' || $request->acc[$i]['opening_balance']!=0){
                        $state=1;
                    }else{$state=0;}
                    $updateData = [
                        'opening_balance' => $request->acc[$i]['opening_balance'],
                        'current_balance' => $request->acc[$i]['opening_balance'],
                        'opening_date' => date('Y-m-d'),
                        'open_bl_add_status' => $state,
                        'updated_by' => Auth::user()->id,
                    ];

                    AccountsChartofAccounts::where('id', $request->acc[$i]['id'])->update($updateData);
                }
            }


            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Chart of Account successfully saved!']);
        } catch (\Exception $e) {
            echo $e->getMessage();
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
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
