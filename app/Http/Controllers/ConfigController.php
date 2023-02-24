<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DB;
use App\Model\Config;

use App\Model\Accounts\AccountsChartofAccounts;

class ConfigController extends Controller
{
    public function index(Request $request){

    	$module = $request->module_id;
    	if ($module==7) {

    		$chartofAccountList = AccountsChartofAccounts::where('status', '1')
    														->select('id','chart_of_accounts_title','chart_of_account_code')
    														->get();
    		return view('config.lc',compact('chartofAccountList'));
    	} else {
    		abort(404);
    	}
    }

    public function update(Request $request){

    	$module_id = $request->module_id;

    	$config_name = $request->config_name;
    	$config_value = $request->config_value;

        try {
            DB::beginTransaction();

            foreach ($config_name as $key => $value) {

            	$configRow = Config::where('config_name',$config_name[$key])->First();
            	if ($configRow) {

            		$configRow->module_id = $module_id;
            		$configRow->config_name = $config_name[$key];
            		$configRow->config_value = $config_value[$key];
            		$configRow->updated_by = Auth::id();
            		$configRow->save();
            	} else {

            		$response = new Config;
            		$response->module_id = $module_id;
            		$response->config_name = $config_name[$key];
            		$response->config_value = $config_value[$key];
            		$response->updated_by = Auth::id();            		
            		$response->save();
            	}
			}

            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
            $message = $e->getMessage();
            dd($message);
        }

    	dd('Success');
    }
}
