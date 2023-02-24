<?php

namespace App\Http\Controllers\Accounts\BasicSetup;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Model\Accounts\AccountsChartofAccounts;
use App\Model\Accounts\CheckBookLeaf;
use App\Model\Accounts\CheckBookLeafGenDetails;
use Illuminate\Support\Facades\Auth;
use DB;

class CheckBookLeafController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {

            if ($request->looking_for == 'getNewCheckBookNumber') {
                $result = CheckBookLeaf::where('chart_ac_id',$request->chart_ac_id)
                                ->max('book_no');
                return $result+1;
            }

            $query = DB::table('check_book_leafs')
                ->leftJoin('accounts_chartof_accounts', 'check_book_leafs.chart_ac_id', '=', 'accounts_chartof_accounts.id')
                ->leftJoin('users as ura', 'check_book_leafs.created_by','=','ura.id')
                ->leftJoin('users as ured', 'check_book_leafs.updated_by','=','ured.id')
                ->leftJoin('users as urea', 'check_book_leafs.approve_by','=','urea.id')
                ->whereNull('check_book_leafs.deleted_at')
                ->select(['check_book_leafs.id AS id',
                    'check_book_leafs.book_no',
                    'check_book_leafs.check_start',
                    'check_book_leafs.check_range',
                    'check_book_leafs.check_gen_date',
                    'check_book_leafs.details',
                    'check_book_leafs.approve_status',
                    'check_book_leafs.created_by',
                    'check_book_leafs.updated_by',
                    'check_book_leafs.approve_by',
                    'check_book_leafs.status',
                    'accounts_chartof_accounts.chart_of_accounts_title',
                    'accounts_chartof_accounts.chart_of_account_code',
                    'ura.user_name as ad_name',
                    'ured.user_name as ed_name',
                    'urea.user_name as ap_name'

                ]);

            return datatables()->of($query)->toJson();
        }

        $chartofAccountBankWiseList = AccountsChartofAccounts::where('chart_of_account_code','like','3207%')
                                            ->orWhere('chart_of_account_code','like', '45%')
                                            ->where('status', '1')
                                            ->select('id','chart_of_accounts_title','chart_of_account_code')
                                            ->get();

        return view('accounts.accounts_setup.check_book_leaf',compact('chartofAccountBankWiseList'));
    }

    public function store(Request $request)
    {

        $this->validate($request, [
            'chart_ac_id'   =>'required',
            'account_no'   =>'required',
            'book_no'   =>'required',
            'check_start'   =>'required',
            'check_range'   =>'required',
            'status'        =>'required',
        ]);

        $input = $request->except('approve');
        $date = str_replace('/', '-', $request->check_gen_date);
        $date_val =date('Y-m-d', strtotime($date));
        $input['check_gen_date'] = $date_val;
        $input['created_by'] = Auth::user()->id;

        $app = $request->approve;

        try {
            DB::beginTransaction();

             $result = CheckBookLeaf::create($input);

            $dataFormat = [];

            for ($t=0;$t<count($request->chequeList);$t++)
            {
                if($request->chequeList[$t] != null)
                {
                    $dataFormat[$t] = [
                        'check_book_leaf_id'    => $result->id,
                        'chart_ac_id'           => $request->chart_ac_id,
                        'leaf_number'           => ($request->chequeList[$t]),
                    ];
                }

            }

            if(count($dataFormat) > 0)
            {
                CheckBookLeafGenDetails::insert($dataFormat);
            }

            if($app ==1)
            {
                $this->approve($result->id);

                if($request->updated_by == Null){
                    DB::table('check_book_leafs')->where('id', $result->id)->update(array('updated_by' => Auth::user()->id));
                }
            }

            DB::commit();
            $bug = 0;
        } catch (\Exception $e) {
            print($e->getMessage());
            DB::rollback();
            $bug = 1;
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Check Book Leaf successfully saved !']);
        }else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }


    public function approve($id){
        $appData = CheckBookLeaf::FindOrFail($id);
        if ($appData->approve_status == 0) {

            $appData->approve_status = 1;
            $appData->approve_by = Auth::user()->id;
            $appData->approve_at = Carbon::now();
            $appData->save();
        }

    }


    public function show($id)
    {
        try {
            $showData          = CheckBookLeaf::with('get_coa')->FindOrFail($id);
            $showDetailsData   = CheckBookLeafGenDetails::where('check_book_leaf_id',$id)->get();

            $data = [
                'id'                 => $showData->id,
                'account_no'         => $showData->account_no,
                'book_no'            => $showData->book_no,
                'get_coa'            => $showData->get_coa,
                'check_gen_date'     => date('d/m/Y', strtotime($showData->check_gen_date)),
                'details'            => $showData->details,
                'approve'            => $showData->approve_status,
                'check_leaf_details' => $showDetailsData,
            ];
            return response()->json(['status'=>'success','data'=>$data]);
        } catch(\Exception $e){
            return response()->json(['status'=>'error','data'=>[]]);
        }
    }


    public function edit($id)
    {

        $editModeData = CheckBookLeaf::FindOrFail($id);

        $data = [
            'id'                => $editModeData->id,
            'chart_ac_id'       => $editModeData->chart_ac_id,
            'account_no'        => $editModeData->account_no,
            'book_no'           => $editModeData->book_no,
            'prefix'            => $editModeData->prefix,
            'suffix'            => $editModeData->suffix,
            //'cheque_no_length'  => $editModeData->cheque_no_length,
            'check_start'       => $editModeData->check_start,
            'check_range'       => $editModeData->check_range,
            'details'           => $editModeData->details,
            'check_gen_date'    => date('d/m/Y', strtotime($editModeData->check_gen_date)),
            'approve'           => $editModeData->approve_status,
            'status'            => $editModeData->status,
        ];

        return response()->json(['status'=>'success','data'=>$data]);
    }


    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'chart_ac_id'=>'required',
            'check_start'   =>'required',
            'check_range'   =>'required',
            'status'        =>'required',
        ]);

        $data =  CheckBookLeaf::FindOrFail($id);

        //$input = $request->all();
        $input = $request->except('approve');
        $date = str_replace('/', '-', $request->check_gen_date);
        $date_val =date('Y-m-d', strtotime($date));
        $input['check_gen_date'] = $date_val;
        $input['updated_by'] = Auth::user()->id;


        $app= $request->approve;

        try {
            DB::beginTransaction();

            $data->update($input);

            CheckBookLeafGenDetails::where('check_book_leaf_id', $id)->delete();

            $dataFormat = [];

            for ($t=0;$t<count($request->chequeList);$t++)
            {
                if($request->chequeList[$t] != null)
                {
                    $dataFormat[$t] = [
                        'check_book_leaf_id'    => $id,
                        'chart_ac_id'           => $request->chart_ac_id,
                        'leaf_number'           => ($request->chequeList[$t]),
                    ];
                }

            }

           /* $a=0;
            $dataFormat = [];

            for ($i = 1; $i <= $request->check_range; $i++) {
                $dataFormat[$a] = [
                    'check_book_leaf_id' => $id,
                    'chart_ac_id'     => $request->chart_ac_id,
                    'leaf_number'        => ($request->check_start-1)+$i,
                ];
                $a++;
            }*/


            if(count($dataFormat) > 0){
                CheckBookLeafGenDetails::insert($dataFormat);
            }

            if($app ==1){
                $this->approve($id);
            }

            DB::commit();
            $bug = 0;
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Check Book Leaf successfully Updated !']);
        } elseif ($bug == 1062) {
            return response()->json(['status' => 'error', 'message' => 'Check Book Leaf is Found Duplicate']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }


    public function destroy($id)
    {
        $data = CheckBookLeaf::FindOrFail($id);
        try{
            CheckBookLeafGenDetails::where('check_book_leaf_id', $id)->delete();
            $data->delete();
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->errorInfo[1];
        }


        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Check Book Leaf successfully Deleted !']);
        } elseif ($bug == 1451) {
            return response()->json(['status' => 'error', 'message' => 'This data is used another table,can not delete data']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }
}
