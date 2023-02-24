<?php

namespace App\Http\Controllers\Sales\Setup;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Sales\SalesCustomer;

use App\Model\Accounts\AccountsSubCode2;
use App\Model\Accounts\AccountsChartofAccounts;

use Illuminate\Support\Facades\Auth;
use DB;


class SalesCustomerController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {

            $query = DB::table('sales_customers')
                ->leftJoin('accounts_chartof_accounts','sales_customers.chart_ac_id', '=', 'accounts_chartof_accounts.id')
                ->leftJoin('users as ura', 'sales_customers.created_by','=','ura.id')
                ->leftJoin('users as ured', 'sales_customers.updated_by','=','ured.id')
                ->whereNull('sales_customers.deleted_at')
                ->select(['sales_customers.id AS id',
                    'sales_customers.sales_customer_name',
                    'sales_customers.sales_customer_contact_person_name',
                    'sales_customers.sales_customer_business_phone',
                    'sales_customers.sales_customer_mobile',
                    'sales_customers.sales_customer_office_address',
                    'sales_customers.created_by',
                    'sales_customers.updated_by',
                    'sales_customers.status',
                    'accounts_chartof_accounts.chart_of_accounts_title',
                    'accounts_chartof_accounts.chart_of_account_code',
                    'ura.user_name as ad_name',
                    'ured.user_name as ed_name'
                ]);


            return datatables()->of($query)->toJson();
        }

        $chat_lists = AccountsChartofAccounts::where('status','1')->where('chart_of_account_code','like', '3205%')->get();
        return view('sales.sales_setup.sales_customer',compact('chat_lists'));
    }



    public function store(Request $request)
    {
        date_default_timezone_set("Asia/Dhaka");
        $this->validate($request, [
            'sales_customer_name'=>'required|unique:sales_customers,sales_customer_name',
            'chart_ac_id'=>'required',
            'sales_customer_contact_person_name'=>'required',
            'sales_customer_business_phone'=>'required',
            'sales_customer_mobile'=>'required',
            'sales_customer_email' =>'nullable|email',
            'status'=>'required',
        ]);




        $input = $request->all();
        $input['created_by'] = Auth::user()->id;

        $sales_customer_id = AccountsChartofAccounts::where('id',$request->chart_ac_id)->first()->chart_of_account_code;

        $date = str_replace('/', '-', $request->sales_customer_join_date);
        $date_val =date('Y-m-d', strtotime($date));
        $input['sales_customer_join_date'] = $date_val;
        $input['sales_customer_id'] = $sales_customer_id;


        try {
            DB::beginTransaction();
                /*$find_chart_acc=AccountsChartofAccounts::where('chart_of_account_code', $request->sales_customer_id)->first();
                if($find_chart_acc!=''){
                    $input['chart_ac_id'] = $find_chart_acc->id;
                }
                else{
                    $sub = substr($request->sales_customer_id, 0, 4);
                    $sub_code= AccountsSubCode2::where('sub_code2', $sub)->first();

                    $data = [
                        'sub_code2_id' => $sub_code->id,
                        'chart_of_accounts_title' => $request->sales_customer_name,
                        'chart_of_account_code' => $request->sales_customer_id,
                        'opening_date' => date('Y-m-d'),
                        'created_by' => Auth::user()->id,
                    ];
                    $cid= AccountsChartofAccounts::create($data);
                    $input['chart_ac_id'] = $cid->id;
                }*/
                SalesCustomer::create($input);
            DB::commit();
            $bug = 0;
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Customer successfully saved !']);
        } elseif ($bug == 1062) {
            return response()->json(['status' => 'error', 'message' => 'Customer is Found Duplicate']);
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
        $editModeData= SalesCustomer::findOrFail($id);
        $data = [
            'id'    => $editModeData->id,
            'sales_customer_name' =>$editModeData->sales_customer_name,
            'chart_ac_id' =>$editModeData->chart_ac_id,
            'sales_customer_join_date' => date('d/m/Y', strtotime($editModeData->sales_customer_join_date)),
            'sales_customer_contact_person_name' =>$editModeData->sales_customer_contact_person_name,
            'sales_customer_contact_person_job_title' =>$editModeData->sales_customer_contact_person_job_title,
            'sales_customer_business_phone' =>$editModeData->sales_customer_business_phone,
            'sales_customer_mobile' =>$editModeData->sales_customer_mobile,
            'sales_customer_email' =>$editModeData->sales_customer_email,
            'sales_customer_web_address' =>$editModeData->sales_customer_web_address,
            'sales_customer_credit_limit' =>$editModeData->sales_customer_credit_limit,
            'sales_customer_office_address' =>$editModeData->sales_customer_office_address,
            'sales_customer_narration' =>$editModeData->sales_customer_narration,
            'status' =>$editModeData->status,
        ];
        return $data;
        //return SalesCustomer::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        date_default_timezone_set("Asia/Dhaka");
        $this->validate($request, [
            'sales_customer_name'=>'required',
            'chart_ac_id'=>'required',
            'sales_customer_contact_person_name'=>'required',
            'sales_customer_business_phone'=>'required',
            'sales_customer_mobile'=>'required',
            'sales_customer_email' =>'nullable|email',
            'status'=>'required',
        ]);

        $input = $request->all();
        $data = SalesCustomer::findOrFail($id);

        $sales_customer_id = AccountsChartofAccounts::where('id',$request->chart_ac_id)->first()->chart_of_account_code;
        $date = str_replace('/', '-', $request->sales_customer_join_date);
        $date_val =date('Y-m-d', strtotime($date));
        $input['sales_customer_join_date'] = $date_val;
        $input['updated_by'] = Auth::user()->id;
        $input['sales_customer_id'] = $sales_customer_id;

       /* if($data->chart_ac_id == ''){
            $find_chart_acc=AccountsChartofAccounts::where('chart_of_account_code' , $request->sales_customer_id)->first();
            if($find_chart_acc!=''){
                $input['chart_ac_id'] = $find_chart_acc->id;
            }
            else{
                $subn = substr($request->sales_customer_id, 0, 4);
                $sub_coden= AccountsSubCode2::where('sub_code2', $subn)->first();
                $datan = [
                    'sub_code2_id' => $sub_coden->id,
                    'chart_of_accounts_title' => $request->sales_customer_name,
                    'chart_of_account_code' => $request->sales_customer_id,
                    'opening_date' => date('Y-m-d'),
                    'created_by' => Auth::user()->id,
                ];
                $cid= AccountsChartofAccounts::create($datan);
                $input['chart_ac_id'] = $cid->id;
            }
        } else{
            $find_chart_acc_ed=AccountsChartofAccounts::where('chart_of_account_code',$data->sales_customer_id)->first();
            if($find_chart_acc_ed == ''){
                $sub_ed = substr($request->sales_customer_id, 0, 4);
                $sub_code_ed= AccountsSubCode2::where('sub_code2',$sub_ed)->first();
                $datae = [
                    'sub_code2_id' => $sub_code_ed->id,
                    'chart_of_accounts_title' => $request->sales_customer_name,
                    'chart_of_account_code' => $request->sales_customer_id,
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
            return response()->json(['status' => 'success', 'message' => 'Customer successfully Updated !']);
        } elseif ($bug == 1062) {
            return response()->json(['status' => 'error', 'message' => 'Customer is Found Duplicate']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    public function destroy($id)
    {
        $data = SalesCustomer ::FindOrFail($id);
        try{

            DB::table('accounts_chartof_accounts')->where('id', $data->chart_ac_id)->delete();
            DB::table('sales_customers')->where('id', $id)->delete();

            $data->delete();
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->errorInfo[1];
        }


        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Customer successfully Deleted !']);
        } elseif ($bug == 1451) {
            return response()->json(['status' => 'error', 'message' => 'This data is used another table,can not delete data']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }
}
