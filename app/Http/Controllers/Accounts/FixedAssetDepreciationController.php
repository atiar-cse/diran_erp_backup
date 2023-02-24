<?php

namespace App\Http\Controllers\Accounts;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Model\Accounts\AccountsSubCode;
use App\Model\Accounts\AccountsChartofAccounts;

use App\Model\Accounts\AccountsFixedAssetDepreciation;
use App\Model\Accounts\AccountsFixedAssetDepreciationDetails;

use Illuminate\Support\Facades\Auth;
use DB;

class FixedAssetDepreciationController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = DB::table('accounts_fixed_asset_depreciations')
                ->leftJoin('accounts_sub_codes', 'accounts_fixed_asset_depreciations.sub_code_id', '=', 'accounts_sub_codes.id')
                ->leftJoin('users as ura', 'accounts_fixed_asset_depreciations.created_by','=','ura.id')
                ->leftJoin('users as ured', 'accounts_fixed_asset_depreciations.updated_by','=','ured.id')
                ->leftJoin('users as urea', 'accounts_fixed_asset_depreciations.approve_by','=','urea.id')
                ->whereNull('accounts_fixed_asset_depreciations.deleted_at')
                ->select(['accounts_fixed_asset_depreciations.id AS id',
                    'accounts_fixed_asset_depreciations.depreciations_date',
                    'accounts_fixed_asset_depreciations.depreciations_no',
                    'accounts_fixed_asset_depreciations.total_amount',
                    'accounts_fixed_asset_depreciations.total_current_amount',
                    'accounts_fixed_asset_depreciations.total_depreciations',
                    'accounts_fixed_asset_depreciations.approve_status',
                    'accounts_fixed_asset_depreciations.created_by',
                    'accounts_fixed_asset_depreciations.updated_by',
                    'accounts_fixed_asset_depreciations.approve_by',
                    'accounts_sub_codes.sub_code_title',
                    'accounts_sub_codes.sub_code',
                    'ura.user_name as ad_name',
                    'ured.user_name as ed_name',
                    'urea.user_name as ap_name'
                ]);

            return datatables()->of($query)->toJson();
        }

        $subCodeList      = AccountsSubCode::where('status', '1')->where('sub_code','31')->get();
        return view('accounts.accounts_section.fixed_asset_depreciation',compact('subCodeList'));
    }

    public function SlGenerate(){
        $id = AccountsFixedAssetDepreciation::withTrashed()->count();
        $currentId = $id+1;
        return 'AFAD'.date('Ym').$currentId;
    }

    public function chartlist($id){

        $codeVal  = AccountsSubCode::where('id', $id)->first();

        $chartList  = AccountsChartofAccounts::where('chart_of_account_code', 'like', $codeVal->sub_code.'%')
                    ->orderBy('sub_code2_id','asc')->get();

        $dataFormat = [];
        $sub_code='';

        foreach ($chartList as $key => $value) {

            if($sub_code==$value->sub_code2_id){
                $a=1;
            }else{
                $a=0;
                $span  = AccountsChartofAccounts::where('sub_code2_id', $value->sub_code2_id)->count();
            }
            $sub_code = $value->sub_code2_id;

            $dataFormat[] = [
                'chart_acc_id'                  => $value->id,
                'sub_code2_id'                  => $value->sub_code2_id,

                'sub_code_title2'               => $value->sub_code2->sub_code_title2,
                'sub_code2'                     => $value->sub_code2->sub_code2,

                'chart_of_accounts_title'       => $value->chart_of_accounts_title,
                'chart_of_account_code'         => $value->chart_of_account_code,

                'show'   => $a,
                'span'    =>$span,

                'amount'                        => $value->current_balance,
                'percentage'                    => 10,
                'depreciations'                 => 0,
                'current_amount'                => 0,
            ];
        }
        return $dataFormat;
    }

    public function chartlistForEditView($dep_id,$sub_code_id){

        $codeVal  = AccountsSubCode::where('id', $sub_code_id)->first();

        $chartList  = AccountsChartofAccounts::where('chart_of_account_code', 'like', $codeVal->sub_code.'%')
                    ->orderBy('sub_code2_id','asc')->get();

        $dataFormat = [];
        $sub_code='';

        foreach ($chartList as $key => $value) {

            if($sub_code==$value->sub_code2_id){
                $a=1;
            }else{
                $a=0;
                $span  = AccountsChartofAccounts::where('sub_code2_id', $value->sub_code2_id)->count();
            }
            $sub_code = $value->sub_code2_id;

            $depreciation_row = AccountsFixedAssetDepreciationDetails::where('depreciation_id',$dep_id)
                                            ->where('chart_acc_id',$value->id)
                                            ->First();

            $dataFormat[] = [
                'chart_acc_id'                  => $value->id,
                'sub_code2_id'                  => $value->sub_code2_id,

                'sub_code_title2'               => $value->sub_code2->sub_code_title2,
                'sub_code2'                     => $value->sub_code2->sub_code2,

                'chart_of_accounts_title'       => $value->chart_of_accounts_title,
                'chart_of_account_code'         => $value->chart_of_account_code,

                'show'   => $a,
                'span'    =>$span,

                'amount'                        => $value->current_balance,
                'percentage'                    => $depreciation_row ? $depreciation_row->percentage : 10,
                'depreciations'                 => $depreciation_row ? $depreciation_row->depreciations : 10,
                'current_amount'                => $depreciation_row ? $depreciation_row->current_amount : 10,
            ];
        }
        return $dataFormat;
    }



    public function store(Request $request)
    {

        $inv_no_like = 'AFAD';
        $rtn_val =InvStore($request->depreciations_no, $inv_no_like,
            'accounts_fixed_asset_depreciations','depreciations_no');

        $request->merge(['depreciations_no' => $rtn_val]) ;

        $this->validate($request, [
            'depreciations_no' => 'required|alpha_dash|unique:accounts_fixed_asset_depreciations,depreciations_no',
            'depreciations_date' => 'required',
            'sub_code_id' => 'required',
            'details.*.chart_acc_id' => 'required',
            'details.*.percentage' => 'required',
        ], [
            'sub_code_id.required' => 'The Sub-Code field is required.',
            'details.*.chart_acc_id.required' => 'Required field.',
            'details.*.percentage.required' => 'Required field.',
        ]);

        $input = $request->except('details','approve_status');
        $input['depreciations_date'] = dateConvertFormtoDB($request->depreciations_date);
        $input['created_by'] = Auth::user()->id;

        $approval = $request->approve;

        try {
            DB::beginTransaction();

            $result = AccountsFixedAssetDepreciation::create($input);
            $details = $this->dataFormat($request, $result->id);
            AccountsFixedAssetDepreciationDetails::insert($details);

            if($approval ==1){

                $this->approve($result->id, $request->details);

                if($request->updated_by == Null){
                    DB::table('accounts_fixed_asset_depreciations')->where('id', $result->id)->update(array('updated_by' => Auth::user()->id));
                }
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => "Fixed Asset Depreciation # " . $request->depreciations_no .' Successfully Saved!']);
        } catch (\Exception $e) {
            DB::rollback();
            $mm = $e->getMessage();
            return response()->json(['status' => 'error', 'message' => $mm]);
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    public function show($id)
    {
        try {
            $editModeData = AccountsFixedAssetDepreciation::FindOrFail($id);
            $editModeDetailsData = $this->chartlistForEditView($id,$editModeData->sub_code_id);
            $data = [
                'id'    => $editModeData->id,
                'depreciations_no' => $editModeData->depreciations_no,
                'depreciations_date' => dateConvertDBtoForm($editModeData->depreciations_date),
                'sub_code_id' => $editModeData->sub_code_id,

                'depreciations_narration' => $editModeData->depreciations_narration,
                'total_amount' => $editModeData->total_amount,
                'total_depreciations' => $editModeData->total_depreciations,
                'total_current_amount' => $editModeData->total_current_amount,
                'approve_status' => $editModeData->approve_status,
                'account_status' => $editModeData->account_status,
                'status' => $editModeData->status,
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
            $editModeData = AccountsFixedAssetDepreciation::FindOrFail($id);
            $editModeDetailsData = $this->chartlistForEditView($id,$editModeData->sub_code_id);
            $data = [
                'id'    => $editModeData->id,
                'depreciations_no' => $editModeData->depreciations_no,
                'depreciations_date' => dateConvertDBtoForm($editModeData->depreciations_date),
                'sub_code_id' => $editModeData->sub_code_id,

                'depreciations_narration' => $editModeData->depreciations_narration,
                'total_amount' => $editModeData->total_amount,
                'total_depreciations' => $editModeData->total_depreciations,
                'total_current_amount' => $editModeData->total_current_amount,
                'approve_status' => $editModeData->approve_status,
                'account_status' => $editModeData->account_status,
                'status' => $editModeData->status,
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
            'depreciations_no' => 'required|alpha_dash|unique:accounts_fixed_asset_depreciations,depreciations_no,'.$id.',id',
            'depreciations_date' => 'required',
            'sub_code_id' => 'required',
            'details.*.chart_acc_id' => 'required',
            'details.*.percentage' => 'required',
        ], [
            'sub_code_id.required' => 'The Sub-Code field is required.',
            'details.*.chart_acc_id.required' => 'Required field.',
            'details.*.percentage.required' => 'Required field.',
        ]);

        $input = $request->except('details','approve_status');
        $input['depreciations_date'] = dateConvertFormtoDB($request->depreciations_date);

        $approval = $request->approve;

        if($approval !=1){
            $input['updated_by'] = Auth::user()->id;
        }

        try {
            DB::beginTransaction();

            $result = AccountsFixedAssetDepreciation::FindOrFail($id);
            $result->update($input);

            AccountsFixedAssetDepreciationDetails::where('depreciation_id',$id)
                                                ->delete();

            $details = $this->dataFormat($request, $id);
            AccountsFixedAssetDepreciationDetails::insert($details);

            if($approval ==1){

                $this->approve($result->id, $request->details);
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Fixed Asset Depreciation Successfully updated!']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    public function destroy($id)
    {
        try{
            $response = AccountsFixedAssetDepreciation::FindOrFail($id);
            if ($response->approve_status==0) {
                $response->delete();
                AccountsFixedAssetDepreciationDetails::where('depreciation_id',$id)->delete();
                return response()->json(['status' => 'success', 'message' => 'Successfully Deleted !']);
            }

            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);

        }
        catch(\Exception $e){
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    public function dataFormat($request, $id)
    {
        $dataFormat = [];
        $count = count($request->details);
        for ($i = 0; $i < $count; $i++) {
            $dataFormat[$i] = [
                'depreciation_id' => $id,
                'sub_code2_id' => $request->details[$i]['sub_code2_id'],
                'chart_acc_id' => $request->details[$i]['chart_acc_id'],
                'amount' => $request->details[$i]['amount'],
                'percentage' => $request->details[$i]['percentage'],
                'depreciations' => $request->details[$i]['depreciations'],
                'current_amount' => $request->details[$i]['current_amount'],
            ];
        }

        return $dataFormat;
    }

    public function approve($id, $details)
    {
        $response = AccountsFixedAssetDepreciation::FindOrFail($id);
        if ($response->approve_status == 0) {

            $response->approve_status = 1;
            $response->approve_by = Auth::user()->id;
            $response->approve_at = Carbon::now();
            $response->save();

                foreach ($details as $row) {
                    $charOfAcc = AccountsChartofAccounts::FindOrFail($row['chart_acc_id']);
                    $charOfAcc->current_balance = $row['current_amount'];
                    $charOfAcc->save();
                }
        }
    }
}
