<?php

namespace App\Http\Controllers\Accounts;

use App\Model\Accounts\AccountsMonthlyClosing;
use App\Model\Accounts\AccountsSubCode;
use App\Model\Accounts\AccountsSubCode2;
use App\Model\Accounts\AccountsYearEnd;
use App\Model\Accounts\AccountsChartofAccounts;
use App\Model\Accounts\AccountsMainCode;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;
use DB;

class YearEndController extends Controller
{

    public function index()
    {
        return view('accounts.accounts_section.year_end');
    }

    public function create()
    {

    }

    public function store(Request $request)
    {
        $chart_of_acc= AccountsChartofAccounts::where('status',1)->get();

        $yearEndFormat[]='';

        foreach($chart_of_acc as $acc){
            $yearEndFormat = [
                'chart_account_id' => $acc->id,
                'year_end_date' => date('Y-m-d'),
                'closing_year' => date('Y'),
                'closing_balance' => $acc->current_balance,

                'created_by' => Auth::user()->id,
            ];
        }
        //
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

    function yearClosing(Request $request)
    {
        //print_r($request->date);

        $accIDS = ['1','4'];

        $AccountsSubCodeIDS = AccountsSubCode::select('id')->whereIn('main_code_id',$accIDS)->get();

        $AccountsSubSubCodeIDS = AccountsSubCode2::select('id')->whereIn('sub_code_id',$AccountsSubCodeIDS)->get();

        $chartofAccID = AccountsChartofAccounts::select('id','chart_of_account_code','opening_balance','current_balance')->whereIn('sub_code2_id',$AccountsSubSubCodeIDS)->get();

        try {
            DB::beginTransaction();

            $data = [];

            foreach ($chartofAccID as $ids)
            {
                $closing_bl = $ids->current_balance + $ids->opening_balance;

                $data[] = [

                    'date' => dateConvertFormtoDB($request->date),
                    'chart_of_account_id' => $ids->id,
                    'chart_of_account_code' => $ids->chart_of_account_code,
                    'opening_balance' => $closing_bl,
                    'current_balance' => $closing_bl,
                    'closing_balance' => '',
                ];

            }

            AccountsMonthlyClosing::insert($data);

            DB::commit();

            //return response()->json(['status' => 'success', 'message' => 'Closing Successfully Done!']);
            $StatusMsg = 'Closing Successfully Done!';
            return redirect('year-end')->withErrors($StatusMsg);

        } catch (\Exception $e) {

            $msg = $e->getMessage();

            DB::rollback();

            $StatusMsg = 'Not Done! Something Wrong';

            return redirect('year-end')->withErrors($StatusMsg);
        }

    }
}
