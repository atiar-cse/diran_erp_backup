<?php

namespace App\Http\Controllers\LcImport;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\LC\LcCostNameEntry;
use Illuminate\Support\Facades\Auth;
use App\Model\LC\LcCostNameCategory;
use DB;
use App\Model\Accounts\AccountsChartofAccounts;

class LcCostNameEntryController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {

            $query = DB::table('lc_cost_name_entries')
                ->leftJoin('lc_cost_name_categories', 'lc_cost_name_entries.cost_category_id', '=', 'lc_cost_name_categories.id')
                ->leftJoin('accounts_chartof_accounts', 'lc_cost_name_entries.chart_ac_id', '=', 'accounts_chartof_accounts.id')
                ->whereNull('lc_cost_name_entries.deleted_at')
                ->select(['lc_cost_name_entries.id AS id',
                    'lc_cost_name_entries.cost_name',
                    'lc_cost_name_entries.status',
                    'lc_cost_name_categories.cost_category_name',
                    'accounts_chartof_accounts.chart_of_accounts_title',
                    'accounts_chartof_accounts.chart_of_account_code',

                ]);

            return datatables()->of($query)->toJson();

        }

        $costCategoryLists = LcCostNameCategory::where('status',1)->get();
        $chartofAccountList = AccountsChartofAccounts::where('status', '1')
                                                        ->select('id','chart_of_accounts_title','chart_of_account_code')
                                                        ->get();
        return view('lc.lc_setup.lc_cost_name_entry',compact('costCategoryLists','chartofAccountList'));
    }

    public function create()
    {
        
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'cost_category_id'=>'required',
            'cost_name'=>'required|unique:lc_cost_name_entries,cost_name',
            'chart_ac_id'=>'required',
            'status'=>'required',
        ],  [
                'cost_category_id.required' => 'The Cost Category field is required.',
                'chart_ac_id.required' => 'The Chart Of Accounts field is required.'
        ]);

        $input = $request->all();
        $input['created_by'] = Auth::user()->id;

        try {
            DB::beginTransaction();

            LcCostNameEntry::create($input);

            DB::commit();
            $bug = 0;
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'LC Cost Name successfully saved !']);
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
        return LcCostNameEntry::FindOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'cost_category_id'=>'required',
            'cost_name'=>'required|unique:lc_cost_name_entries,cost_name,'.$id,
            'chart_ac_id'=>'required',
            'status'=>'required',
        ],  [
                'cost_category_id.required' => 'The Cost Category field is required.',
                'chart_ac_id.required' => 'The Chat of Accounts field is required.'
        ]);

        $data = LcCostNameEntry::findOrFail($id);
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
            return response()->json(['status' => 'success', 'message' => 'LC Cost Name successfully Updated !']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    public function destroy($id)
    {
        $data = LcCostNameEntry::FindOrFail($id);
        try{
            $data->delete();
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'LC Cost Name successfully Deleted !']);
        } elseif ($bug == 1451) {
            return response()->json(['status' => 'error', 'message' => 'This data is used another table,can not delete data']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }

    }

    public function getListByCategory($cost_category_id)
    {
        $details = LcCostNameEntry::where('status',1)->where('cost_category_id',$cost_category_id)->get();
        $datatv = [];


        foreach ($details as $cat){
            $datatv[] = [
                'lc_cost_name_id' => $cat->id,
                'cost_value'      => '0'
            ];
        }

        $data = [
            'detailsArray'  => $datatv,
            'lsits'         => $details,
        ];

        return response()->json($data);
    }     
}
