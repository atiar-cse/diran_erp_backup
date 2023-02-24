<?php

use App\Model\Accounts\AccountsChartofAccounts;
use App\Model\Accounts\AccountsMainCode;

use App\Model\Accounts\AccountsBankPaymentVoucher;
use App\Model\Accounts\AccountsBankReceiptVoucher;
use App\Model\Accounts\AccountsCashPaymentVoucher;
use App\Model\Accounts\AccountsCashReceiptVoucher;
use App\Model\Accounts\AccountsPurchaseOrderVoucher;
use App\Model\Accounts\AccountsSalesInvoiceVoucher;
use App\Model\Production\ProductionFinishedStock;
use App\Model\Purchase\PurchaseOrderReceive;
use App\Model\Purchase\PurchaseRequisition;
use App\Model\Sales\SalesDeliveryChallan;
use App\Model\Sales\SalesInvoice;
use App\Model\Inventory\InventoryCurrentStock;
use App\Model\Production\ProductionProducts;
use App\Model\HR\HrApplyForLeave;
use Illuminate\Support\Facades\DB;

function dateConvertFormtoDB($date){
    if(!empty($date)){
        return date("Y-m-d",strtotime(str_replace('/','-',$date)));
    }
}

function dateConvertDBtoForm($date){
    if(!empty($date)){
        $date = strtotime($date);
        return date('d/m/Y', $date);
    }
}

function dateFormat($date,$format){
    if(!empty($date)){
        $date = strtotime($date);
        return date($format, $date);
    }
}

function permissionCheck(){

    $route_uri = \Route::getCurrentRoute()->uri();
    $slash_status = strstr($route_uri, '/', true); //returns false if no "/" in URL

    if ($slash_status === false) {
        $route_uri = $route_uri;
    } else {
        $route_uri = $slash_status;
    }

    $role_id = session('logged_session_data.role_id');
    $results =  array_column(json_decode(DB::table('acl_menus')->select('menu_url')
        ->join('acl_menu_permissions', 'acl_menu_permissions.menu_id', '=', 'acl_menus.id')
        ->where('acl_menu_permissions.role_id', '=', $role_id)
        ->where('menu_url', '!=',null)
        ->where('menu_url', 'like', "%$route_uri%")
        ->get()->toJson(),true),'menu_url');

        if (Auth::user()->email == 'software@iventurebd.com') {
            array_push($results, 'super-admin');
        } elseif (Auth::user()->id == '10') {
            $check_module =  DB::table('acl_menus')
                                ->join('acl_menu_permissions', 'acl_menu_permissions.menu_id', '=', 'acl_menus.id')
                                ->where('acl_menu_permissions.role_id', '=', $role_id)
                                ->where('menu_url', '!=',null)
                                ->where('menu_url', 'like', "%$route_uri%")
                                ->first();
                if ($check_module && $check_module->module_id == 2) {
                    array_push($results, 'super-admin');
                }
        }
    return $results;
}

/*function permissionMenu(){

    return $permission_menu = array_column(json_decode(Menu::join('acl_menu_permissions', 'acl_menu_permission.menu_id', '=', 'acl_menus.id')
    ->where('role_id', Auth::user()->role_id)
    ->where('menu_url', '!=',null)
    ->get()->toJson(), true),'menu_url');

}*/

function permissionCheckForNotification(){

    $role_id = session('logged_session_data.role_id');
    return $result =  array_column(json_decode(DB::table('acl_menus')->select('menu_url')
        ->join('acl_menu_permissions', 'acl_menu_permissions.menu_id', '=', 'acl_menus.id')
        ->where('acl_menu_permissions.role_id', '=', $role_id)
        ->where('menu_url', '!=',null)
        ->get()->toJson(),true),'menu_url');
}


function showMenu(){
    $role_id = session('logged_session_data.role_id');
    $modules = json_decode(DB::table('acl_modules')->get()->toJson(), true);

    //DB::enableQueryLog();
    $menus =  json_decode(DB::table('acl_menus')
        ->select(DB::raw('acl_menus.id, acl_menus.menu_name, acl_menus.menu_url, acl_menus.parent_id, acl_menus.module_id'))
        ->join('acl_menu_permissions', 'acl_menu_permissions.menu_id', '=', 'acl_menus.id')
        ->where('acl_menu_permissions.role_id',$role_id)
        ->where('acl_menus.status',1)
        ->whereNull('action')
        ->orderBy('acl_menus.id','ASC')
        ->get()->toJson(),true);
    //print_r(DB::getQueryLog($menus));





    $sideMenu = [];
    if($menus){
        foreach ($menus as $menu){

            if(!isset($sideMenu[$menu['module_id']])){
                $moduleId = array_search($menu['module_id'], array_column($modules, 'id'));

                $sideMenu[$menu['module_id']] = [];
                $sideMenu[$menu['module_id']]['id'] = $modules[$moduleId]['id'];
                $sideMenu[$menu['module_id']]['module_name'] = $modules[$moduleId]['module_name'];
                $sideMenu[$menu['module_id']]['icon_class'] = $modules[$moduleId]['icon_class'];
                $sideMenu[$menu['module_id']]['menu_url'] = '#';
                $sideMenu[$menu['module_id']]['parent_id'] = '';
                $sideMenu[$menu['module_id']]['module_id'] = $modules[$moduleId]['id'];
                $sideMenu[$menu['module_id']]['sub_menu'] = [];
            }
            if($menu['parent_id'] == 0){
                $sideMenu[$menu['module_id']]['sub_menu'][$menu['id']] = $menu;
                $sideMenu[$menu['module_id']]['sub_menu'][$menu['id']]['sub_menu'] = [];
            }else{

                array_push($sideMenu[$menu['module_id']]['sub_menu'][$menu['parent_id']]['sub_menu'], $menu);
            }

        }
    }

    return $sideMenu;
}

function getCurrency($number)
{
    $decimal = round($number - ($no = floor($number)), 2) * 100;
    $hundred = null;
    $digits_length = strlen($no);
    $i = 0;
    $str = array();
    $words = array(0 => '', 1 => 'One', 2 => 'Two',
        3 => 'Three', 4 => 'Four', 5 => 'Five', 6 => 'Six',
        7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
        10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve',
        13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen',
        16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen',
        19 => 'Nineteen', 20 => 'Twenty', 30 => 'Thirty',
        40 => 'Forty', 50 => 'Fifty', 60 => 'Sixty',
        70 => 'Seventy', 80 => 'Eighty', 90 => 'Ninety');
    $digits = array('', 'Hundred','Thousand','Lac', 'Crore', "Hundred Crore","Thousand Crore", "Lac Crore");

    while( $i < $digits_length ) {
        $divider = ($i == 2) ? 10 : 100;
        $number = floor($no % $divider);
        $no = floor($no / $divider);
        $i += $divider == 10 ? 1 : 2;
        if ($number) {
            // $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
            $plural = (($counter = count($str)) && $number > 9) ? '' : null;
            $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
            $str [] = ($number < 21) ? $words[$number].' '. $digits[$counter]. $plural.' '.$hundred:$words[floor($number / 10) * 10].' '.$words[$number % 10]. ' '.$digits[$counter].$plural.' '.$hundred;
        } else $str[] = null;
    }

    $tks = implode('', array_reverse($str));
    $paise = '';

    if ($decimal) {
        $paise = ', ';
        $decimal_length = strlen($decimal);

        if ($decimal_length == 2) {
            if ($decimal >= 20) {
                $dc = $decimal % 10;
                $td = $decimal - $dc;
                $ps = ($dc == 0) ? '' : '-' . $words[$dc];

                $paise .= $words[$td] . $ps;
            } else {
                $paise .= $words[$decimal];
            }
        } else {
            $paise .= $words[$decimal % 10];
        }

        $paise .= ' Paisa';
    }

    return ($tks ? $tks . 'Taka ' : '') . $paise ;
}

function get_cat_wise_product($cat_id)
{
    $product_list = [];

    $details = ProductionProducts::where('status', 1)->where('category_id', $cat_id)
        ->select('id', 'product_name','product_code')
        ->orderBy('product_name','ASC')
        ->get();

    foreach ($details as $row) {
        $product_list[] = [
            'id' => $row->id,
            'product_name' => $row->product_name,
            'product_code' => $row->product_code,
        ];
    }

    return ['product_list' => $product_list];
}

function getChartOfAcc($sub_code2_id)
{
    $COAListData = [];

    $details = AccountsChartofAccounts::where('status', 1)->where('sub_code2_id', $sub_code2_id)
        ->select('id', 'chart_of_accounts_title', 'chart_of_account_code')
        ->orderBy('chart_of_account_code','ASC')
        ->orderBy('chart_of_accounts_title','ASC')
        ->get();
    foreach ($details as $row) {
        $COAListData[] = [
            'id' => $row->id,
            'chart_of_accounts_title' => $row->chart_of_accounts_title,
            'chart_of_account_code' => $row->chart_of_account_code,
        ];
    }

    return ['account_list' => $COAListData];
}

function getTypeList($type) {

    try {

        $type_name_title = 'Type Name';
        $typeListData = [];

        if ($type == 'customer') {

            $type_name_title = 'Customer Name';
            $details = \App\Model\Sales\SalesCustomer::where('status',1)
                ->select('id','sales_customer_name')
                ->get();
            foreach ($details as $row) {
                $typeListData[] = [
                    'id' => $row->id,
                    'type_name' => $row->sales_customer_name,
                ];
            }
        } elseif ($type == 'supplier') {

            $type_name_title = 'Supplier Name';
            $details = \App\Model\Purchase\PurchaseSupplierEntry::where('status',1)
                ->select('id','purchase_supplier_name')
                ->get();
            foreach ($details as $row) {
                $typeListData[] = [
                    'id' => $row->id,
                    'type_name' => $row->purchase_supplier_name,
                ];
            }
        } elseif ($type == 'employee') {

            $type_name_title = 'Employee Name';
            $details = \App\Model\HR\HrManageEmployee::select('id','first_name','last_name','emp_id')
                ->get();
            foreach ($details as $row) {
                $typeListData[] = [
                    'id' => $row->id,
                    'type_name' => "$row->emp_id - $row->first_name $row->last_name",
                ];
            }
        } elseif ($type == 'project') {

            $type_name_title = 'Project';
            $details = \App\Model\Accounts\AccountsProjectInfo::where('status',1)
                ->select('*')
                ->get();
            foreach ($details as $row) {
                $typeListData[] = [
                    'id' => $row->id,
                    'type_name' => "$row->project_name",
                ];
            }
        } elseif ($type == 'lc') {
            $type_name_title = 'LC';
            $details = \App\Model\LC\LcOpeningSection::where('approve_status',1)
                ->where('lc_closing_status',0)
                ->select('id','lc_no')
                ->get();
            foreach ($details as $row) {
                $typeListData[] = [
                    'id' => $row->id,
                    'type_name' => "$row->lc_no",
                ];
            }
        }

        return ['type_name_title' => $type_name_title, 'type_list' => $typeListData];
    }
    catch(\Exception $e){
        print_r($e->getMessage());
    }
}

/* @ return row object */
function getAccMainVoucherRow($transaction_type,$transaction_reference_id)
{

    switch ($transaction_type)
    {
        case 2: //Contra Voucher
            $result = DB::table('accounts_contra_entry_vouchers as acc')
                            ->leftJoin('check_book_leaf_gen_details as leaf', 'acc.cheque_leaf','=','leaf.id')
                            ->where('acc.id',$transaction_reference_id)
                            ->First();
            return $result;
            break;

        case 4: ////Bank Payment Voucher
            return NULL;

        case 6: ////Bank Received Voucher
            return NULL;

        case 7: //Bank Transfer/Deposit
            $result = DB::table('accounts_bank_transfer_deposits as acc')
                            ->leftJoin('check_book_leaf_gen_details as leaf', 'acc.cheque_leaf','=','leaf.id')
                            ->where('acc.id',$transaction_reference_id)
                            ->First();
            return $result;
            break;

        case 8: //Sales Voucher
            $result = DB::table('accounts_sales_invoice_vouchers as acc')
                            ->leftJoin('check_book_leaf_gen_details as leaf', 'acc.cheque_leaf','=','leaf.id')
                            ->where('acc.id',$transaction_reference_id)
                            ->First();
            return $result;
            break;

        case 9: //Sales Return Voucher
            $result = DB::table('accounts_sales_invoice_return_vouchers as acc')
                            ->leftJoin('check_book_leaf_gen_details as leaf', 'acc.cheque_leaf','=','leaf.id')
                            ->where('acc.id',$transaction_reference_id)
                            ->First();
            return $result;
            break;

        case 10: //Purchase Voucher
            $result = DB::table('accounts_purchase_order_vouchers as acc')
                            ->leftJoin('check_book_leaf_gen_details as leaf', 'acc.cheque_leaf','=','leaf.id')
                            ->where('acc.id',$transaction_reference_id)
                            ->First();
            return $result;
            break;

        case 11: //Purchase Return Voucher
            $result = DB::table('accounts_purchase_return_vouchers as acc')
                            ->leftJoin('check_book_leaf_gen_details as leaf', 'acc.cheque_leaf','=','leaf.id')
                            ->where('acc.id',$transaction_reference_id)
                            ->First();
            return $result;
            break;

        default:
            return NULL;
    }
}

function get_acc_sales_pending_voucher()
{
    return AccountsSalesInvoiceVoucher::where('status',1)->where('approve_status',0)->orderBy('id','DESC')->get();
}

function get_sales_pending_challan()
{
    return SalesDeliveryChallan::where('status',1)->where('approve_status',0)->orderBy('id','DESC')->get();
}

function get_sales_pending_order()
{
    return SalesInvoice::where('status',1)->where('approve_status',0)->orderBy('id','DESC')->get();
}
function get_acc_po_pending_voucher()
{
    return AccountsPurchaseOrderVoucher::where('status',1)->where('approve_status',0)->orderBy('id','DESC')->get();
}

function get_po_pending()
{
    return PurchaseOrderReceive::where('status',1)->where('approve_status',0)->orderBy('id','DESC')->get();
}

function get_po_req_pending()
{
    return PurchaseRequisition::where('status',1)->where('approve_status',0)->orderBy('id','DESC')->get();
}


function get_acc_bank_payment_pending_voucher()
{
    return AccountsBankPaymentVoucher::where('approve_status',0)->orderBy('id','DESC')->get();
}
function get_acc_cash_payment_pending_voucher()
{
    return AccountsCashPaymentVoucher::where('approve_status',0)->orderBy('id','DESC')->get();
}

function get_acc_bank_rev_pending_voucher()
{
    return AccountsBankReceiptVoucher::where('approve_status',0)->orderBy('id','DESC')->get();
}
function get_acc_cash_rev_pending_voucher()
{
    return AccountsCashReceiptVoucher::where('approve_status',0)->orderBy('id','DESC')->get();
}

function appSl()
{
    $year = date('Y');
    $month = date('m');
    //DB::enableQueryLog();
    $data = SalesDeliveryChallan::where('status',1)
        ->where('approve_status',1)
        ->whereYear('sales_delivery_date', '=', $year)
        ->whereMonth('sales_delivery_date', '=', $month)
        ->count();
    //print_r(DB::getQueryLog($data));
    return $data;
}

function appPo()
{
    //DB::enableQueryLog();
    $year = date('Y');
    $month = date('m');
    $data =  PurchaseOrderReceive::where('status',1)->where('approve_status',1)
        ->whereYear('po_receive_date', '=', $year)
        ->whereMonth('po_receive_date', '=', $month)
        ->count();
    //print_r(DB::getQueryLog($data));
    return $data;
}

function appPro()
{
    //DB::enableQueryLog();
    $year = date('Y');
    $month = date('m');
    $data = ProductionFinishedStock::where('status',1)
        ->whereYear('date', '=', $year)
        ->whereMonth('date', '=', $month)
        ->count();
    //print_r(DB::getQueryLog($data));
    return $data;
}

function sale_data(){
    //DB::enableQueryLog();
    $data = SalesDeliveryChallan::selectRaw('sum(sales_delivery_total_qty) as total,sales_delivery_date')
        ->where('status',1)->where('approve_status',1)
        ->OrderBy('sales_delivery_date','desc')
        ->groupby('sales_delivery_date')
        ->limit(4)
        ->get();
    //print_r(DB::getQueryLog($data));

    return $data;

}

function deduct_stock($warehouse,$product_id,$qty)
{
    $check = InventoryCurrentStock::where('inventory_current_stocks_warehouse_id',$warehouse)->where('inventory_current_stocks_product_id',$product_id)->first();

    if($check)
    {
        $current_qty = $check->inventory_stocks_current_qty;

        $data = array(
            'inventory_stocks_current_qty'    => $current_qty - $qty,
        );

        InventoryCurrentStock::where('inventory_current_stocks_warehouse_id',$warehouse)->where('inventory_current_stocks_product_id',$product_id)->update($data);

    }
    else{

        $data = array(
            'inventory_current_stocks_warehouse_id'     => $warehouse,
            'inventory_current_stocks_product_id'       => $product_id,
            'inventory_stocks_current_qty'              => - $qty,

        );

        InventoryCurrentStock::insert($data);

    }
}

function add_stock($warehouse,$product_id,$qty)
{
    $check = InventoryCurrentStock::where('inventory_current_stocks_warehouse_id',$warehouse)->where('inventory_current_stocks_product_id',$product_id)->first();

    if($check)
    {
        $current_qty = $check->inventory_stocks_current_qty;

        $data = array(
            'inventory_stocks_current_qty'    => $current_qty + $qty,
        );

        InventoryCurrentStock::where('inventory_current_stocks_warehouse_id',$warehouse)->where('inventory_current_stocks_product_id',$product_id)->update($data);

    }
    else{

        $data = array(
            'inventory_current_stocks_warehouse_id'     => $warehouse,
            'inventory_current_stocks_product_id'       => $product_id,
            'inventory_stocks_current_qty'              => $qty,

        );

        InventoryCurrentStock::insert($data);

    }
}

//Accounting
function reverseDrCrAccountBalanceIncDec($char_of_account_id,$amount,$dr_cr){

    $chartofAccountData = AccountsChartofAccounts::FindOrFail($char_of_account_id);
    $code = mb_substr($chartofAccountData->chart_of_account_code, 0, 1);
    $mainCode = AccountsMainCode::where('main_code',$code)->first();

    if ($dr_cr == 'dr') { //Debit Amount
        if($mainCode->main_code_title == 'Assets'){
            AccountsChartofAccounts::where('id', $char_of_account_id)
                ->decrement('current_balance', $amount);
        }
        elseif($mainCode->main_code_title == 'Expense'){
            AccountsChartofAccounts::where('id', $char_of_account_id)
                ->decrement('current_balance', $amount);
        }
        elseif($mainCode->main_code_title == 'Income'){
            AccountsChartofAccounts::where('id', $char_of_account_id)
                ->increment('current_balance', $amount);
        }
        elseif($mainCode->main_code_title == 'Liabilities'){
            AccountsChartofAccounts::where('id', $char_of_account_id)
                ->increment('current_balance', $amount);
        }
        elseif($mainCode->main_code_title == 'Equity'){ //Equity / Capital
            AccountsChartofAccounts::where('id', $char_of_account_id)
                ->increment('current_balance', $amount);
        }
    } else if($dr_cr == 'cr') { //Credit Amount
        if($mainCode->main_code_title == 'Assets'){
            AccountsChartofAccounts::where('id', $char_of_account_id)
                ->increment('current_balance', $amount);
        }
        elseif($mainCode->main_code_title == 'Expense'){
            AccountsChartofAccounts::where('id', $char_of_account_id)
                ->increment('current_balance', $amount);
        }
        elseif($mainCode->main_code_title == 'Income'){
            AccountsChartofAccounts::where('id', $char_of_account_id)
                ->decrement('current_balance', $amount);
        }
        elseif($mainCode->main_code_title == 'Liabilities'){
            AccountsChartofAccounts::where('id', $char_of_account_id)
                ->decrement('current_balance', $amount);
        }
        elseif($mainCode->main_code_title == 'Equity'){ //Equity / Capital
            AccountsChartofAccounts::where('id', $char_of_account_id)
                ->decrement('current_balance', $amount);
        }
    }
}


function debitAccountsBalanceIncDec($debit_account_id, $amount)
{
    $debitAccountData = AccountsChartofAccounts::FindOrFail($debit_account_id);
    $debitCode = mb_substr($debitAccountData->chart_of_account_code, 0, 1);
    $debitMainCode = AccountsMainCode::where('main_code', $debitCode)->first();

    if ($debitMainCode->main_code_title == 'Assets') {
        AccountsChartofAccounts::where('id', $debit_account_id)->increment('current_balance', $amount);
    } elseif ($debitMainCode->main_code_title == 'Expense') {
        AccountsChartofAccounts::where('id', $debit_account_id)->increment('current_balance', $amount);
    }  elseif ($debitMainCode->main_code_title == 'Income') {
        AccountsChartofAccounts::where('id', $debit_account_id)->decrement('current_balance', $amount);
    } elseif ($debitMainCode->main_code_title == 'Liabilities') {
        AccountsChartofAccounts::where('id', $debit_account_id)->decrement('current_balance', $amount);
    } elseif ($debitMainCode->main_code_title == 'Equity') { //Equity / Capital
        AccountsChartofAccounts::where('id', $debit_account_id)->decrement('current_balance', $amount);
    }
}

function creditAccountsBalanceIncDec($credit_account_id, $amount){

    $creditAccountData = AccountsChartofAccounts::FindOrFail($credit_account_id);
    $creditCode = mb_substr($creditAccountData->chart_of_account_code, 0, 1);
    $creditMainCode = AccountsMainCode::where('main_code', $creditCode)->first();

    if ($creditMainCode->main_code_title == 'Assets') {
        AccountsChartofAccounts::where('id', $credit_account_id)->decrement('current_balance', $amount);
    } elseif ($creditMainCode->main_code_title == 'Expense') {
        AccountsChartofAccounts::where('id', $credit_account_id)->decrement('current_balance', $amount);
    } elseif ($creditMainCode->main_code_title == 'Income') {
        AccountsChartofAccounts::where('id', $credit_account_id)->increment('current_balance', $amount);
    } elseif ($creditMainCode->main_code_title == 'Liabilities') {
        AccountsChartofAccounts::where('id', $credit_account_id)->increment('current_balance', $amount);
    } elseif ($creditMainCode->main_code_title == 'Equity') { //Equity / Capital
        AccountsChartofAccounts::where('id', $credit_account_id)->increment('current_balance', $amount);
    }
}


function inv_modify($st,$string,$field_name, $table, $date){
    $len = strlen($st.date('Ym'));
    $len2 = strlen($st.date('Y'));
    $str = $string;
    $dt = date('m', strtotime($date));
    $arr = (str_split($str, $len));
    $mp= substr($arr[0], $len2, 2);
    $strt = substr($arr[0], 0, -2);
    $last = $arr[1];

    if($dt == $mp) {
        $inv = $str;
    }else{
        $inv = $strt.$dt.$last;
    }

    $code_array = DB::table($table)->selectRaw($field_name)
        ->where($field_name,  $inv)->first();
    if($code_array){
        $inv = $inv;
    }else{
        $more= (int)$last + 1;
        $inv = $strt.$dt.$more;
    }

    return $inv;
}

function InvGenerate($string,$tbl_name,$field_name){
    $code=$string.date('Ym');
    $count = strlen($code)+1;

    $code_array = DB::table($tbl_name)->selectRaw("Max( substr($field_name,$count) * 1 ) as code_val")
        ->first();
    if(!$code_array){
        $val_return = $code.'1';
    }
    else{
        $val_will = (int)$code_array->code_val +1;
        $val_return = $code.$val_will;
    }
    return $val_return;
}

function InvStore($req, $likeq, $tbl_name,$field_name){

    $count = DB::table($tbl_name)->where($field_name,$req)->count();
    if($count> 0){
        $rtn_val = InvGenerate($likeq,$tbl_name,$field_name);
    }else{
        $rtn_val = $req;
    }

    return $rtn_val;
}

function configurationNotice()
{
    return \App\Model\Configuration::where('config_key','notice')->where('status',1)->orderBy('id','DESC')->get()->first();
}

function costOfGoodSoldProductionVoucher($journal_entry_id,$total_amount)
{
    $Data =[
        'journal_entry_id' => $journal_entry_id,
        'char_of_account_id' => 434,
        'debit_amount' => $total_amount,
    ];

    \App\Model\Accounts\AccountsJournalEntryDetails::insert($Data);
}
