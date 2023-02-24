<?php

namespace App\Repositories;
use App\Model\UserAccessControl\AclRole;
use App\Model\Accounts\AccountsChartofAccounts;
use App\Model\Accounts\AccountsMainCode;
use DB;

class CommonRepositories
{

    public  function selectRoleList(){
        $result = AclRole::get();
        $options = [''=>'---- Please select ----'];
        foreach ($result as $key => $value) {
            $options [$value->id] = $value->role_name;
        }
        return $options ;
    }

    public function all_menu(){
       $result = DB::table('menus')
           ->whereNotNull('menus.menu_url')
           ->whereNull('action')
           ->orderBy('module_id')
           ->orderBy('id')
           ->get();
       return $result;
    }
    public function all_sub_menus(){
        $result  = DB::table('menus')
            ->whereNotNull('action')
            ->get();
        return $result;
    }
    public function permission($role_id){
        $result = DB::table('menus')
            ->select(DB::raw('menus.id, menus.name, menus.menu_url, menus.parent_id, menus.module_id,menus_permission.menu_id'))
            ->join('menus_permission', 'menus_permission.menu_id', '=', 'menus.id')
            ->where('menus_permission.role_id', '=', $role_id)
            ->get();
        return $result;
    }

    public function currentBalanceIncrementDecrement($details){

        $count = count($details);
        for ($i = 0; $i < $count; $i++) {
            $code_find=AccountsChartofAccounts::where('id',($details[$i]['char_of_account_id']))->first();

            //echo $code_find->chart_of_account_code;
            $code=substr($code_find->chart_of_account_code,0,1);
            //echo $code.'<br/>'.$details[$i]['char_of_account_id'].'<br/>';
            $code_head=AccountsMainCode::where('main_code',$code)->first();
            //echo $code_head->main_code_title.'<br/>';

            if($code_head->main_code_title == 'Assets'){
                if($details[$i]['debit_amount'] =='' || $details[$i]['debit_amount'] ==0) {//cr
                    //echo 'cr ->dec';
                    AccountsChartofAccounts::where('id', $details[$i]['char_of_account_id'])
                        ->decrement('current_balance', $details[$i]['credit_amount']);
                }
                else{
                    //echo 'dr ->inc';
                    AccountsChartofAccounts::where('id', $details[$i]['char_of_account_id'])
                        ->increment('current_balance', $details[$i]['debit_amount']);
                }
            }
            elseif($code_head->main_code_title == 'Expense'){
                if($details[$i]['debit_amount'] =='' || $details[$i]['debit_amount'] ==0) { //cr
                    //echo 'cr ->dec';
                    AccountsChartofAccounts::where('id', $details[$i]['char_of_account_id'])
                        ->decrement('current_balance', $details[$i]['credit_amount']);
                }
                else{
                    //echo 'dr ->inc';
                    AccountsChartofAccounts::where('id', $details[$i]['char_of_account_id'])
                        ->increment('current_balance', $details[$i]['debit_amount']);
                }
            }
            elseif($code_head->main_code_title == 'Income'){
                if($details[$i]['debit_amount'] =='' || $details[$i]['debit_amount'] ==0) {//cr
                    //echo 'cr ->inc';
                    AccountsChartofAccounts::where('id', $details[$i]['char_of_account_id'])
                        ->increment('current_balance', $details[$i]['credit_amount']);
                }
                else{
                    //echo 'dr ->dec';
                    AccountsChartofAccounts::where('id', $details[$i]['char_of_account_id'])
                        ->decrement('current_balance', $details[$i]['debit_amount']);
                }
            }
            elseif($code_head->main_code_title == 'Liabilities'){
                if($details[$i]['debit_amount'] =='' || $details[$i]['debit_amount'] ==0) {//cr
                    //echo 'cr ->inc';
                    AccountsChartofAccounts::where('id', $details[$i]['char_of_account_id'])
                        ->increment('current_balance', $details[$i]['credit_amount']);
                }
                else{
                    //echo 'dr ->dec';
                    AccountsChartofAccounts::where('id', $details[$i]['char_of_account_id'])
                        ->decrement('current_balance', $details[$i]['debit_amount']);
                }
            }
            else{ //Equity / Capital
                if($details[$i]['debit_amount'] =='' || $details[$i]['debit_amount'] ==0) {//cr
                    //echo 'cr ->inc';
                    AccountsChartofAccounts::where('id', $details[$i]['char_of_account_id'])
                        ->increment('current_balance', $details[$i]['credit_amount']);
                }
                else{
                    //echo 'dr ->dec';
                    AccountsChartofAccounts::where('id', $details[$i]['char_of_account_id'])
                        ->decrement('current_balance', $details[$i]['debit_amount']);
                }
            }
        }
    }
}
