<?php

namespace App\Http\Controllers\Sales;

use App\Lib\Enumerations\AccountsTransactionType;
use App\Model\Accounts\AccountsJournalEntry;
use App\Model\Accounts\AccountsJournalEntryDetails;
use App\Model\Accounts\AccountsSalesInvoiceVoucher;
use App\Model\Accounts\CheckBookLeafGenDetails;
use App\Model\Inventory\StockTransectionLog;
use App\Model\Inventory\StockTransectionLogDetails;
use App\Model\Sales\SalesDeliveryChallan;
use App\Model\Sales\SalesDeliveryChallanDetails;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Model\Production\ProductionProducts;
use App\Model\Sales\SalesCustomer;

use App\Model\Sales\SalesInvoice;
use App\Model\Sales\SalesInvoiceDetails;

use Illuminate\Support\Facades\Auth;
use DB;


class SalesInvoiceController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $query = DB::table('sales_invoices')
                ->leftJoin('sales_customers', function($join){
                    $join->on('sales_invoices.sales_invoices_customer_id', '=', 'sales_customers.id');
                    $join->on('sales_invoices.sales_invoices_customer_id','!=',DB::raw("''"));
                })
                ->leftJoin('purchase_ware_houses', 'sales_invoices.sales_invoices_warehouse_id', '=', 'purchase_ware_houses.id')
                ->leftJoin('users as ura', 'sales_invoices.created_by','=','ura.id')
                ->leftJoin('users as ured', 'sales_invoices.updated_by','=','ured.id')
                ->leftJoin('users as urea', 'sales_invoices.approve_by','=','urea.id')
                ->whereNull('sales_invoices.deleted_at')
                ->select(['sales_invoices.id AS id',
                    'sales_invoices.sales_invoices_no',
                    'sales_invoices.sales_invoices_date',
                    'sales_invoices.sales_invoices_contract_no',
                    'sales_invoices.sales_invoices_type',
                    'sales_invoices.sales_invoices_narration',
                    'sales_invoices.sales_invoices_total_qty',
                    'sales_invoices.sales_invoices_total_price',
                    'sales_invoices.created_by',
                    'sales_invoices.updated_by',
                    'sales_invoices.approve_by',
                    'sales_invoices.approve_status',
                    'sales_customers.sales_customer_name',
                    'purchase_ware_houses.purchase_ware_houses_name',
                    'ura.user_name as ad_name',
                    'ured.user_name as ed_name',
                    'urea.user_name as ap_name'
                ]);

            return datatables()->of($query)->toJson();
        }

        $EnumVal =$this->EnumVal();
        $customerLists   = SalesCustomer::where('status', '1')->get();
        return view('sales.sales_section.sales_invoice',
            compact( 'EnumVal','customerLists'));
    }

    public function store(Request $request)
    {
        date_default_timezone_set("Asia/Dhaka");

        $inv_no_like = 'INV';
        $rtn_val =InvStore($request->sales_invoices_no, $inv_no_like,
            'sales_invoices','sales_invoices_no');

        $request->merge(['sales_invoices_no' => $rtn_val]) ;


        if($request->sales_invoices_type =='Local'){
            $this->validate($request, [
                'sales_invoices_no'             => 'required|unique:sales_invoices,sales_invoices_no',
                'sales_invoices_type'           => 'required',
                'sales_invoices_warehouse_id'   => 'required',
                'sales_invoices_customer_id'    => 'required',
                'sales_invoices_total_qty'      => 'required',
                'sales_invoices_total_price'    => 'required',

                'details.*.sales_invoice_details_product_id'    => 'required',
                'details.*.sales_invoice_details_unit_price'    => 'required',
                'details.*.sales_invoice_details_qty'           => 'required',
                'details.*.sales_invoice_details_total_price'   => 'required',
            ], [
                'details.*.sales_invoice_details_product_id.required'   => 'required',
                'details.*.sales_invoice_details_unit_price.required'   => 'required',
                'details.*.sales_invoice_details_qty.required'          => 'required',
                'details.*.sales_invoice_details_total_price.required'  => 'required',
            ]);
           }
        else{
            $this->validate($request, [
                'sales_invoices_no'             => 'required|unique:sales_invoices,sales_invoices_no',
                'sales_invoices_type'           => 'required',
                'sales_invoices_warehouse_id'   => 'required',
                'sales_invoices_customer_id'    => 'required',
                'sales_invoices_contract_no'    => 'required',
                'sales_invoices_total_qty'      => 'required',
                'sales_invoices_total_price'    => 'required',

                'details.*.sales_invoice_details_product_id'    => 'required',
                'details.*.sales_invoice_details_unit_price'    => 'required',
                'details.*.sales_invoice_details_qty'           => 'required',
                'details.*.sales_invoice_details_total_price'   => 'required',
            ], [
                'details.*.sales_invoice_details_product_id.required'   => 'required',
                'details.*.sales_invoice_details_unit_price.required'   => 'required',
                'details.*.sales_invoice_details_qty.required'          => 'required',
                'details.*.sales_invoice_details_total_price.required'  => 'required',
            ]);
            }

        if($request->sales_invoices_date!= ''){
            $date = str_replace('/', '-', $request->sales_invoices_date);
            $date_val =date('Y-m-d', strtotime($date));
        }else{
            $date_val =date('Y-m-d');
        }

       /* $str = $request->sales_invoices_no;
        $dt = date('m', strtotime($date_val));
        $arr = (str_split($str, 9));
        $mp= substr($arr[0], 7, 2);
        $strt = substr($arr[0], 0, -2);
        $last = $arr[1];

        if($dt == $mp) {
            $inv = $str;
        }else{
            $inv = $strt.$dt.$last;
        }

        $code_array = DB::table('sales_invoices')->selectRaw("sales_invoices_no")
            ->where('sales_invoices_no',  $inv)->first();
        if($code_array){
            $inv = $inv;
        }else{
            $more= (int)$last + 1;
            $inv = $strt.$dt.$more;
        }*/

        $inv = inv_modify('inv',$request->sales_invoices_no,'sales_invoices_no', 'sales_invoices', $date);



        $customer =$request->sales_invoices_customer_id;
        $contract = $request->sales_invoices_contract_no;

        $app= $request->approve;

        $data = [
            'sales_invoices_no'             => $inv,
            'sales_invoices_contract_no'    => $contract,
            'sales_invoices_date'           => $date_val,
            'sales_invoices_type'           => $request->sales_invoices_type,
            'sales_invoices_warehouse_id'   => $request->sales_invoices_warehouse_id,
            'sales_invoices_customer_id'    => $customer,
            'sales_invoices_narration'      => $request->sales_invoices_narration,
            'sales_invoices_total_qty'      => $request->sales_invoices_total_qty,
            'sales_invoices_total_price'    => $request->sales_invoices_total_price,
            'created_by'                    => Auth::user()->id,
        ];

        try {
            DB::beginTransaction();

            $result = SalesInvoice::create($data);
            $details = $this->dataFormat($request, $result->id);
            SalesInvoiceDetails::insert($details);


            ///////////////////////////////////// Approve /////////////////////////////////////////
            if($app ==1){
                $this->approve($result->id);

                if($request->updated_by == Null){
                    DB::table('sales_invoices')->where('id', $result->id)->update(array('updated_by' => Auth::user()->id));
                }
            }

            DB::commit();

            return response()->json(['status' => 'success', 'message' => "Sales Invoice # " . $request->sales_invoices_no .' Successfully Saved!']);
            //return response()->json(['status' => 'success', 'message' => 'Sales Invoice successfully saved!']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    public function show(Request $request, $id)
    {
        $id = (int) $id;

        if ($request->filled('isSuperAdmin')) {
            if ($request->isSuperAdmin == 'unapprove') {
                //if (Auth::user()->email == 'software@iventurebd.com'){

                $res = $this->unAppproveSalesData($id);

                if ($res)
                    $msg = ['status' => 'success', 'message' => 'All related Sales Voucher is successfully UnApproved for this voucher!'];
                else
                    $msg = ['status' => 'error', 'message' => 'Failed to UnApproved! Some error occured'];

                return response()->json($msg);
                //}

            }
        }

        try {
            $editModeData = SalesInvoice::with('get_customer','get_wh')->FindOrFail($id);

            $customer  = $editModeData->get_customer->sales_customer_name;
            $warehousue  = $editModeData->get_wh->purchase_ware_houses_name;

            $editModeDetailsData = SalesInvoiceDetails::with('get_prod')->where('sales_invoice_id',$id)->get();

            $data = [
                'id'    => $editModeData->id,
                'sales_invoices_no'           => $editModeData->sales_invoices_no,
                'sales_invoices_contract_no'  => $editModeData->sales_invoices_contract_no,
                'sales_invoices_date'         => date('d/m/Y', strtotime($editModeData->sales_invoices_date)),
                'sales_invoices_type'         => $editModeData->sales_invoices_type,
                'sales_invoices_warehouse_id' => $warehousue,
                'sales_invoices_customer_id'  => $customer,
                'sales_invoices_narration'    => $editModeData->sales_invoices_narration,
                'sales_invoices_total_qty'    => $editModeData->sales_invoices_total_qty,
                'sales_invoices_total_price'  => $editModeData->sales_invoices_total_price,
                'total_amount_word'           => getCurrency($editModeData->sales_invoices_total_price),

                'details'    => $editModeDetailsData,
            ];
            return response()->json(['status'=>'success','data'=>$data]);
        } catch(\Exception $e){

            return response()->json(['status'=>'error','data'=>[$e->getMessage()]]);
        }
    }

    public function showWithoutPrice($id){

        try {
            $showData = SalesInvoice::with('get_customer','get_wh')->FindOrFail($id);

            $customer  = $showData->get_customer->sales_customer_name;
            $warehousue  = $showData->get_wh->purchase_ware_houses_name;

            $showDetailsData = SalesInvoiceDetails::with('get_prod')->where('sales_invoice_id',$id)->get();

            $data = [
                'id'    => $showData->id,
                'sales_invoices_no'           => $showData->sales_invoices_no,
                'sales_invoices_contract_no'  => $showData->sales_invoices_contract_no,
                'sales_invoices_date'         => date('d/m/Y', strtotime($showData->sales_invoices_date)),
                'sales_invoices_type'         => $showData->sales_invoices_type,
                'sales_invoices_warehouse_id' => $warehousue,
                'sales_invoices_customer_id'  => $customer,
                'sales_invoices_narration'    => $showData->sales_invoices_narration,
                'sales_invoices_total_qty'    => $showData->sales_invoices_total_qty,
                'sales_invoices_total_price'  => $showData->sales_invoices_total_price,
                'total_amount_word'           => getCurrency($showData->sales_invoices_total_price),
                'details'                     => $showDetailsData,
            ];
            return response()->json(['status'=>'success','data'=>$data]);
        } catch(\Exception $e){
            return response()->json(['status'=>'error','data'=>[$e->getMessage()]]);
        }

    }

    public function edit($id)
    {
        try {
            //DB::enableQueryLog();
            $editModeData = SalesInvoice::FindOrFail($id);

            $editModeDetailsData= DB::table('sales_invoice_details as sal_del')
                ->leftJoin('production_products as pr','sal_del.sales_invoice_details_product_id', '=', 'pr.id')
                ->leftJoin('inventory_current_stocks as st', 'st.inventory_current_stocks_product_id','=','pr.id')
                ->select('sal_del.*', 'pr.product_name' ,'st.inventory_stocks_current_qty as current_qty')
                ->where('sal_del.sales_invoice_id',$id)
                ->where('st.inventory_current_stocks_warehouse_id', $editModeData->sales_invoices_warehouse_id)
                ->get();

            $pid_list = ProductionProducts::Select('production_products.*', 'production_measure_units.measure_unit','inventory_current_stocks.inventory_stocks_current_qty')
                ->leftJoin('production_measure_units', 'production_products.measure_unit_id', '=', 'production_measure_units.id')
                ->leftJoin('inventory_current_stocks', 'inventory_current_stocks.inventory_current_stocks_product_id','=','production_products.id')
                ->where('production_products.status', '1')
                ->where('production_products.is_raw_material_status', '0')
                ->where('inventory_current_stocks.inventory_current_stocks_warehouse_id', $editModeData->sales_invoices_warehouse_id)
                ->get();

            $data = [
                'id'    => $editModeData->id,
                'sales_invoices_no' => $editModeData->sales_invoices_no,
                'sales_invoices_contract_no' => $editModeData->sales_invoices_contract_no,
                'sales_invoices_date' => date('d/m/Y', strtotime($editModeData->sales_invoices_date)),
                'sales_invoices_type' => $editModeData->sales_invoices_type,
                'sales_invoices_warehouse_id' => $editModeData->sales_invoices_warehouse_id,
                'sales_invoices_customer_id' => $editModeData->sales_invoices_customer_id,
                'sales_invoices_narration' => $editModeData->sales_invoices_narration,
                'sales_invoices_total_qty' => $editModeData->sales_invoices_total_qty,
                'sales_invoices_total_price' => $editModeData->sales_invoices_total_price,
                'deleteID' => [],
                'approve' => '',
                'details'    => $editModeDetailsData
            ];
            $test_val = [
                'edit_mode' => $data,
                'pid_list' => $pid_list,
            ];
            return response()->json(['status'=>'success','data'=>$test_val]);
        } catch(\Exception $e){
            return response()->json(['status'=>'error','data'=>[]]);
        }
    }

    public function update(Request $request, $id)
    {
        if($request->sales_invoices_type =='Local'){
            $this->validate($request, [
                'sales_invoices_no' => 'required|unique:sales_invoices,sales_invoices_no,'.$id.',id',
                'sales_invoices_type' => 'required',
                'sales_invoices_warehouse_id' => 'required',
                'sales_invoices_customer_id' => 'required',
                'sales_invoices_total_qty' => 'required',
                'sales_invoices_total_price' => 'required',

                'details.*.sales_invoice_details_product_id' => 'required',
                'details.*.sales_invoice_details_unit_price' => 'required',
                'details.*.sales_invoice_details_qty' => 'required',
                'details.*.sales_invoice_details_total_price' => 'required',
            ], [
                'details.*.sales_invoice_details_product_id.required' => 'required',
                'details.*.sales_invoice_details_unit_price.required' => 'required',
                'details.*.sales_invoice_details_qty.required' => 'required',
                'details.*.sales_invoice_details_total_price.required' => 'required',
            ]);
        }
        else{
            $this->validate($request, [
                'sales_invoices_no' => 'required|unique:sales_invoices,sales_invoices_no,'.$id.',id',
                'sales_invoices_type' => 'required',
                'sales_invoices_warehouse_id' => 'required',
                'sales_invoices_customer_id' => 'required',
                'sales_invoices_contract_no' => 'required',
                'sales_invoices_total_qty' => 'required',
                'sales_invoices_total_price' => 'required',
                'details.*.sales_invoice_details_product_id' => 'required',
                'details.*.sales_invoice_details_unit_price' => 'required',
                'details.*.sales_invoice_details_qty' => 'required',
                'details.*.sales_invoice_details_total_price' => 'required',
            ], [
                'details.*.sales_invoice_details_product_id.required' => 'required',
                'details.*.sales_invoice_details_unit_price.required' => 'required',
                'details.*.sales_invoice_details_qty.required' => 'required',
                'details.*.sales_invoice_details_total_price.required' => 'required',
            ]);
        }

        date_default_timezone_set("Asia/Dhaka");
        $date = str_replace('/', '-', $request->sales_invoices_date);
        $date_val =date('Y-m-d', strtotime($date));
        $customer =$request->sales_invoices_customer_id;
        $contract = $request->sales_invoices_contract_no;

        $app= $request->approve;

        try {
            DB::beginTransaction();

            $editModeData = SalesInvoice::FindOrFail($id);

            $editModeData->sales_invoices_no            = $request->sales_invoices_no;
            $editModeData->sales_invoices_date          = $date_val;
            $editModeData->sales_invoices_contract_no   = $contract;
            $editModeData->sales_invoices_type          = $request->sales_invoices_type;
            $editModeData->sales_invoices_warehouse_id  = $request->sales_invoices_warehouse_id;
            $editModeData->sales_invoices_customer_id   = $customer;
            $editModeData->sales_invoices_narration     = $request->sales_invoices_narration;
            $editModeData->sales_invoices_total_qty     = $request->sales_invoices_total_qty;
            $editModeData->sales_invoices_total_price   = $request->sales_invoices_total_price;


            if ($app!=1) {
                $editModeData->updated_by = Auth::user()->id;
            }

            $editModeData->save();

            //// details transection ////
            if (count($request->deleteID) > 0) {
                SalesInvoiceDetails::whereIn('id', $request->deleteID)->delete();
            }
            $dataFormat = [];
            $count = count($request->details);
            for ($i = 0; $i < $count; $i++) {
                if (isset($request->details[$i]['id']) && $request->details[$i]['id'] !='') {
                    $updateData = [
                        'sales_invoice_id' => $id,
                        'sales_invoice_details_product_id' => $request->details[$i]['sales_invoice_details_product_id'],
                        'sales_invoice_details_description' => $request->details[$i]['sales_invoice_details_description'],
                        'sales_invoice_details_unit' => $request->details[$i]['sales_invoice_details_unit'],
                        'sales_invoice_details_unit_price' => $request->details[$i]['sales_invoice_details_unit_price'],
                        'sales_invoice_details_qty' => $request->details[$i]['sales_invoice_details_qty'],
                        'sales_invoice_details_total_price' => $request->details[$i]['sales_invoice_details_total_price']
                    ];
                    SalesInvoiceDetails::where('id', $request->details[$i]['id'])->update($updateData);
                } else {
                    $dataFormat[$i] =[
                        'sales_invoice_id' => $id,
                        'sales_invoice_details_product_id' => $request->details[$i]['sales_invoice_details_product_id'],
                        'sales_invoice_details_description' => $request->details[$i]['sales_invoice_details_description'],
                        'sales_invoice_details_unit' => $request->details[$i]['sales_invoice_details_unit'],
                        'sales_invoice_details_unit_price' => $request->details[$i]['sales_invoice_details_unit_price'],
                        'sales_invoice_details_qty' => $request->details[$i]['sales_invoice_details_qty'],
                        'sales_invoice_details_total_price' => $request->details[$i]['sales_invoice_details_total_price']
                    ];
                }
            }

            if(count($dataFormat) > 0){
                SalesInvoiceDetails::insert($dataFormat);
            }

            ///////////////////////////////////// Approve /////////////////////////////////////////
            if($app ==1){
                $this->approve($id);
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Sales Invoice successfully updated!']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    public function destroy($id)
    {
        try{
            SalesInvoiceDetails::where('sales_invoice_id',$id)->delete();
            SalesInvoice ::FindOrFail($id)->delete();
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Sales Invoice successfully Deleted !']);
        } elseif ($bug == 1451) {
            return response()->json(['status' => 'error', 'message' => 'This data is used another table,can not delete data']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    public function approve($id)
    {
        $req_id = $id;
        try {
            $approval = DB::table('sales_invoices')->where('id',$req_id)->first()->approve_status;
            if($approval == 0)
            {
                DB::table('sales_invoices')
                    ->where('id', $req_id)  // find your requisition
                    ->update(array(
                        'approve_status' => 1,
                        'approve_by'     => Auth::user()->id,
                        'approve_at'     => Carbon::now(),
                    ));
                return response()->json(['status' => 'success', 'message' => 'Sales Invoice successfully Approved!']);
            }
            else{
                return response()->json(['status' => 'success', 'message' => 'Alredy Approved!']);
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            return response()->json(['status' => 'error', 'message' => $message]);
        }
    }

    public function SalesInvoiceGenerate(){
        $str = InvGenerate('INV','sales_invoices','sales_invoices_no');
        return $str;

        /*$id = SalesInvoice::withTrashed()->count();
        $currentId = $id+1;
        return 'INV'.date('Ym').$currentId;*/
    }

    public function listProduct($warehouseId){

        if($warehouseId == '__'){
            $productLists = ProductionProducts::Select('production_products.*', 'production_measure_units.measure_unit')
                ->where('production_products.status', '1')
                ->where('production_products.is_raw_material_status', '0')
                ->leftJoin('production_measure_units', 'production_products.measure_unit_id', '=', 'production_measure_units.id')
                ->get();
        }else{
            $productLists = ProductionProducts::Select('production_products.*', 'production_measure_units.measure_unit','inventory_current_stocks.inventory_stocks_current_qty')
                ->leftJoin('production_measure_units', 'production_products.measure_unit_id', '=', 'production_measure_units.id')
                ->leftJoin('inventory_current_stocks', 'inventory_current_stocks.inventory_current_stocks_product_id','=','production_products.id')
                ->where('production_products.status', '1')
                ->where('production_products.is_raw_material_status', '0')
                ->where('inventory_current_stocks.inventory_current_stocks_warehouse_id', $warehouseId)
                ->get();

        }

        return $productLists;
    }

    private function EnumVal(){
        $table='sales_invoices';
        $name='sales_invoices_type';
        $type = DB::select( DB::raw('SHOW COLUMNS FROM '.$table.' WHERE Field = "'.$name.'"') )[0]->Type;
        preg_match('/^enum\((.*)\)$/', $type, $matches);
        $enum = array();
        foreach(explode(',', $matches[1]) as $value){
            $v = trim( $value, "'" );
            $enum[] = $v;
        }
        return $enum;
    }

    private function dataFormat($request, $id)
    {
        $dataFormat = [];
        $count = count($request->details);
        for ($i = 0; $i < $count; $i++) {
            $dataFormat[$i] = [
                'sales_invoice_id' => $id,
                'sales_invoice_details_product_id' => $request->details[$i]['sales_invoice_details_product_id'],
                'sales_invoice_details_description' => $request->details[$i]['sales_invoice_details_description'],
                'sales_invoice_details_unit' => $request->details[$i]['sales_invoice_details_unit'],
                'sales_invoice_details_unit_price' => $request->details[$i]['sales_invoice_details_unit_price'],
                'sales_invoice_details_qty' => $request->details[$i]['sales_invoice_details_qty'],
                'sales_invoice_details_total_price' => $request->details[$i]['sales_invoice_details_total_price']
            ];
        }
        return $dataFormat;
    }

    private function unAppproveSalesData($id)
    {
        $id = (int) $id;

        try {

            DB::beginTransaction();

            $voucher = AccountsSalesInvoiceVoucher::where('sales_invoice_id', $id)->first();

            if ($voucher && isset($voucher->id)) {

                //Get JournalEntry Table Data
                $journalData = AccountsJournalEntry::where('transaction_reference_id', $voucher->id)
                    ->where('transaction_type', AccountsTransactionType::$salesInvVoucer)
                    ->first();

                if ($journalData && isset($journalData->id))
                //REVERSE AMOUNT AND DELETE - JournalEntryDetails Table Data
                {
                    $allJournals = AccountsJournalEntryDetails::where('journal_entry_id', $journalData->id)->get();

                    foreach ($allJournals as $row) {
                        $amount = 0;
                        $dr_cr = Null;

                        if ($row->debit_amount && ($row->debit_amount > 0)) {
                            $amount = $row->debit_amount;
                            $dr_cr = 'dr';
                        } elseif ($row->credit_amount && ($row->credit_amount > 0)) {
                            $amount = $row->credit_amount;
                            $dr_cr = 'cr';
                        }

                        reverseDrCrAccountBalanceIncDec($row->char_of_account_id, $amount, $dr_cr);

                        $row->delete();
                    }
                }

                if ($journalData)
                    $journalData->delete();

                CheckBookLeafGenDetails::where('id', $voucher->cheque_leaf)->update(['use_status' => 0]);

                if ($voucher)
                    $voucher->delete();
            }


            //Sales Invoice
            $SalesDeliveryChallan = SalesDeliveryChallan::where('sales_invoice_id', $id)->first();

            if ($SalesDeliveryChallan) {
                $SalesDeliveryChallanDetails = SalesDeliveryChallanDetails::where('sales_delivery_id', $SalesDeliveryChallan->id)->get();

                foreach ($SalesDeliveryChallanDetails as $SalesDeliveryChallanDetail) {
                    add_stock($SalesDeliveryChallan->sales_warehouse_id, $SalesDeliveryChallanDetail->sales_delivery_details_product_id, $SalesDeliveryChallanDetail->sales_delivery_details_qty);
                    $SalesDeliveryChallanDetail->delete();
                }

                $SalesDeliveryChallan->delete();
            }

            //StockTransectionLog & Details Delete
            $StockTransectionLog = StockTransectionLog::where('stock_transection_logs_number', $SalesDeliveryChallan->sales_delivery_no)->first();


            if ($StockTransectionLog)
            {
                StockTransectionLogDetails::where('log_id',$StockTransectionLog->id)->delete();
                $StockTransectionLog->delete();
            }

            //Sales Chalan
            $SalesInvoice = SalesInvoice::where('approve_status',1)->FindOrFail($id);

            $SalesInvoice->approve_status = 0;
            $SalesInvoice->save();

            DB::commit();

            return TRUE;
        }
        catch(\Exception $e){
            $msg = $e->getMessage();
            print_r($msg);
            exit;
            return FALSE;
        }
    }
}
