<?php

namespace App\Http\Controllers\Production;

use App\Model\CompanyInfo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Model\Production\ProductionShapping;
use App\Model\Production\ProductionShappingDetails;
use App\Model\Production\ProductionProducts;
use App\Model\Production\ProductionCurrentStock;

use App\Model\Inventory\StockTransectionLog;
use App\Model\Inventory\StockTransectionLogDetails;

use App\Lib\Enumerations\StockTransactionType;

use Illuminate\Support\Facades\Auth;
use DB;
use App\Model\Production\ProductionRecycleChip;

class ShappingSectionController extends Controller
{
    public function index(Request $request)
    {
        if ($request->filled('token_last_price') && $request->token_last_price == 'yes') {
            return $this->getShappingLastPrice($request->product_id);
        }

        if ($request->ajax()) {

            $query = DB::table('production_shappings')
                ->leftJoin('users as ura', 'production_shappings.created_by','=','ura.id')
                ->leftJoin('users as ured', 'production_shappings.updated_by','=','ured.id')
                ->leftJoin('users as urea', 'production_shappings.approve_by','=','urea.id')
                ->whereNull('production_shappings.deleted_at')
                ->select(['production_shappings.id AS id',
                    'production_shappings.shapping_no',
                    'production_shappings.shapping_date',
                    'production_shappings.narration',
                    'production_shappings.created_by',
                    'production_shappings.updated_by',
                    'production_shappings.approve_by',
                    'production_shappings.approve_status',
                    'ura.user_name as ad_name',
                    'ured.user_name as ed_name',
                    'urea.user_name as ap_name'
                ]);

            return datatables()->of($query)->toJson();

        }

        /*-------- PDF Generate-----------------*/
        if ($request->filled('export')) {

            $file_type = $request->export;

            $data = $this->getPdfData($request->id);

            if ($file_type=='pdf') {

                $this->exportAsPdf($data);

            }else {
                dd('Something went wrong.');
            }
        }

        /*-------- PDF Generate End-----------------*/

        $productList[] = '';
        $product  = ProductionProducts::select('production_products.id','production_products.product_name',
            'production_products.shapping_weight', 'production_current_stocks.shapping_current_qty', 'production_current_stocks.shapping_rececive_qty',
            'production_products.category_id')
            ->leftJoin('production_current_stocks', 'production_current_stocks.product_id', '=', 'production_products.id')
            ->where('production_products.status', '1')
            ->with('category')
            ->orderBy('production_products.category_id','asc')
            ->get();
        $a=0;
        foreach ($product as $row){
            $qty= '';
            if($row->shapping_current_qty == '' ||  $row->shapping_current_qty == 0){
                $qty= 0;
            }
            else{
                $qty= $row->shapping_current_qty;
            }
            $productList[$a] = [
                'id'                    => $row->id,
                'product_name'          => $row->product_name,
                'shapping_weight'        => $row->shapping_weight,
                'shapping_current_qty'   => $qty,
                'shapping_rececive_qty'   => $row->shapping_rececive_qty ? $row->shapping_rececive_qty : 0,
                'category_id'           => $row->category_id,
                'product_category_code' => $row->category ? $row->category->product_category_code : '',
            ];
            $a++;
        }

        return view('production.production_section.shapping_section',compact('productList'));
    }

    public function shappingNoGenerate(){
        $id = ProductionShapping::withTrashed()->count();
        $currentId = $id+1;
        return 'SPG-'.date('Ym').$currentId;
    }

    public function store(Request $request)
    {
        $inv_no_like = 'SPG-';
        $rtn_val =InvStore($request->shapping_no, $inv_no_like,
            'production_shappings','shapping_no');

        $request->merge(['shapping_no' => $rtn_val]) ;

        $this->validate($request, [
            'shapping_no' => 'required',
            'shapping_date' => 'required',
            'total_trans_to_dryer_qty' => 'required',
            'total_trans_to_dryer_weight' => 'required',
            'total_waste_qty' => 'required',
            'total_waste_weight' => 'required',

            'details.*.product_id' => 'required',
            'details.*.shapping_product_weight' => 'required',
            'details.*.current_qty' => 'required',
            'details.*.receive_qty' => 'required',
            'details.*.trans_to_dry_qty' => 'required',
            'details.*.trans_to_dry_weight' => 'required',
            'details.*.unit_price' => 'required',
            'details.*.total_price' => 'required',
        ], [

            'details.*.product_id.required' => 'Required field.',
            'details.*.shapping_product_weight.required' => 'Required field.',
            'details.*.current_qty.required' => 'Required field.',
            'details.*.receive_qty.required' => 'Required field.',
            'details.*.trans_to_dry_qty.required' => 'Required field.',
            'details.*.trans_to_dry_weight.required' => 'Required field.',
            'details.*.unit_price.required' => 'Required field.',
            'details.*.total_price.required' => 'Required field.',
        ]);

        $overhead_info = [
            'last_month_overhead_amt'  => $request->last_month_overhead_amt,
            'last_month_production_kg' => $request->last_month_production_kg,
            'overhead_per_kg'          => $request->overhead_per_kg,
        ];

        $data = [
            'shapping_no' => $request->shapping_no,
            'shapping_date' => dateConvertFormtoDB($request->shapping_date),
            'narration' => $request->narration,
            'total_trans_to_dryer_qty' => $request->total_trans_to_dryer_qty,
            'total_trans_to_dryer_weight' => $request->total_trans_to_dryer_weight,
            'total_receive_qty' => $request->total_receive_qty,
            'total_remain_qty' => $request->total_remain_qty,
            'total_waste_qty' => $request->total_waste_qty,
            'total_waste_weight' => $request->total_waste_weight,
            'process_loss_percentage' => $request->process_loss_percentage,
            'process_loss_weight' => $request->process_loss_weight,
            'weight_after_process_loss' => $request->weight_after_process_loss,
            'total_amount' => $request->total_amount,
            'overhead_info' => json_encode($overhead_info),
            'wastage_overhead_amt' => $request->weight_after_process_loss * $request->overhead_per_kg,
            'created_by' => Auth::user()->id,
        ];

        try {
            DB::beginTransaction();

            $app = $request->approve_status;

            $result = ProductionShapping::create($data);
            $details = $this->dataFormat($request, $result->id);
            ProductionShappingDetails::insert($details);

            if($app == 1){
                $this->approve($result->id);

                if($request->updated_by == Null){
                    DB::table('production_shappings')->where('id', $result->id)->update(array('updated_by' => Auth::user()->id));
                }
            }
            //$this->incrementDecrementShappingQTY($details);

            DB::commit();
            return response()->json(['status' => 'success', 'message' => "Shapping # " . $request->shapping_no .' Successfully Saved!']);
        } catch (\Exception $e) {
            DB::rollback();

            $mm = $e->getMessage();

            return response()->json(['status' => 'error', 'message' => $mm]);
            //return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    public function show(Request $request, $id)
    {
        if ($request->filled('SuperAdmin') && $request->filled('Password') && $request->filled('Menu')) {

            $sa = $request->SuperAdmin;
            $pass = $request->Password;
            $menu = $request->Menu;
            if ($sa == 'Irfan2019' && $pass == 'Atiar2019' && $menu == 'shapping-section') {

                // $this->deleteAppprovedShappingEntryData($id);
            }
        }

        try {
            $editModeData = ProductionShapping::FindOrFail($id);
            $editModeDetailsData = ProductionShappingDetails::with('product')->where('shapping_section_id',$id)->get();
            $data = [
                'id'    => $editModeData->id,
                'shapping_no' => $editModeData->shapping_no,
                'shapping_date' => dateConvertDBtoForm($editModeData->shapping_date),
                'narration' => $editModeData->narration,
                'total_trans_to_dryer_qty' => $editModeData->total_trans_to_dryer_qty,
                'total_trans_to_dryer_weight' => $editModeData->total_trans_to_dryer_weight,
                'total_receive_qty' => $editModeData->total_receive_qty,
                'total_remain_qty' => $editModeData->total_remain_qty,
                'total_waste_qty' => $editModeData->total_waste_qty,
                'total_waste_weight' => $editModeData->total_waste_weight,
                'process_loss_percentage' => $editModeData->process_loss_percentage,
                'process_loss_weight' => $editModeData->process_loss_weight,
                'weight_after_process_loss' => $editModeData->weight_after_process_loss,
                'total_amount' => $editModeData->total_amount,
                'deleteID' => [],
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
            $editModeData = ProductionShapping::FindOrFail($id);
            $editModeDetailsData = ProductionShappingDetails::where('shapping_section_id',$id)->get();
            $data = [
                'id'                            => $editModeData->id,
                'shapping_no'                   => $editModeData->shapping_no,
                'shapping_date'                 => dateConvertDBtoForm($editModeData->shapping_date),
                'narration'                     => $editModeData->narration,
                'total_trans_to_dryer_qty'      => $editModeData->total_trans_to_dryer_qty,
                'total_trans_to_dryer_weight'   => $editModeData->total_trans_to_dryer_weight,
                'total_receive_qty'             => $editModeData->total_receive_qty,
                'total_remain_qty'              => $editModeData->total_remain_qty,
                'total_waste_qty'               => $editModeData->total_waste_qty,
                'total_waste_weight'            => $editModeData->total_waste_weight,
                'process_loss_percentage'       => $editModeData->process_loss_percentage,
                'process_loss_weight'           => $editModeData->process_loss_weight,
                'weight_after_process_loss'     => $editModeData->weight_after_process_loss,
                'total_amount'                  => $editModeData->total_amount,

                'last_month_overhead_amt'       => '',
                'last_month_production_kg'      => '',
                'overhead_per_kg'                => '',

                'approve_status'                => '',
                'deleteID'                      => [],
                'details'                       => $editModeDetailsData
            ];
            return response()->json(['status'=>'success','data'=>$data]);
        } catch(\Exception $e){
            return response()->json(['status'=>'error','data'=>[]]);
        }
    }

    public function update(Request $request, $id)
    {
        if ($request->filled('isSuperAdmin')) {
            if ($request->isSuperAdmin == 'correction') {
                //if (Auth::user()->email == 'software@iventurebd.com') {
                    $this->makeCorrectionShapping($request,$id);
                    return response()->json(['status' => 'success', 'message' => 'Shapping Correction successfully updated!']);
                //}
            }
        }

        $this->validate($request, [
            'shapping_no' => 'required',
            'shapping_date' => 'required',
            'total_trans_to_dryer_qty' => 'required',
            'total_trans_to_dryer_weight' => 'required',
            'total_waste_qty' => 'required',
            'total_waste_weight' => 'required',

            'details.*.product_id' => 'required',
            'details.*.shapping_product_weight' => 'required',
            'details.*.current_qty' => 'required',
            'details.*.receive_qty' => 'required',
            'details.*.trans_to_dry_qty' => 'required',
            'details.*.trans_to_dry_weight' => 'required',
            'details.*.unit_price' => 'required',
            'details.*.total_price' => 'required',
        ], [

            'details.*.product_id.required' => 'Required field.',
            'details.*.shapping_product_weight.required' => 'Required field.',
            'details.*.current_qty.required' => 'Required field.',
            'details.*.receive_qty.required' => 'Required field.',
            'details.*.trans_to_dry_qty.required' => 'Required field.',
            'details.*.trans_to_dry_weight.required' => 'Required field.',
            'details.*.unit_price.required' => 'Required field.',
            'details.*.total_price.required' => 'Required field.',
        ]);


        $overhead_info = [
            'last_month_overhead_amt'  => $request->last_month_overhead_amt,
            'last_month_production_kg' => $request->last_month_production_kg,
            'overhead_per_kg'          => $request->overhead_per_kg,
        ];

        try {
            DB::beginTransaction();

            $app = $request->approve_status;

            $editModeData = ProductionShapping::FindOrFail($id);

            $editModeData->shapping_no                 = $request->shapping_no;
            $editModeData->shapping_date               = dateConvertFormtoDB($request->shapping_date);
            $editModeData->narration                   = $request->narration;
            $editModeData->total_trans_to_dryer_qty    = $request->total_trans_to_dryer_qty;
            $editModeData->total_trans_to_dryer_weight = $request->total_trans_to_dryer_weight;
            $editModeData->total_receive_qty           = $request->total_receive_qty;
            $editModeData->total_remain_qty            = $request->total_remain_qty;
            $editModeData->total_waste_qty             = $request->total_waste_qty;
            $editModeData->total_waste_weight          = $request->total_waste_weight;
            $editModeData->process_loss_percentage     = $request->process_loss_percentage;
            $editModeData->process_loss_weight         = $request->process_loss_weight;
            $editModeData->weight_after_process_loss   = $request->weight_after_process_loss;
            $editModeData->total_amount                = $request->total_amount;
            $editModeData->overhead_info               = json_encode($overhead_info);
            $editModeData->wastage_overhead_amt        = $request->weight_after_process_loss * $request->overhead_per_kg;

            if ($app!=1) {
                $editModeData->updated_by = Auth::user()->id;
            }

            $editModeData->save();


            /* Insert update and delete rm requisition details  */

            if (count($request->deleteID) > 0) {
                ProductionShappingDetails::whereIn('id', $request->deleteID)->delete();
            }
            $dataFormat = [];
            $count = count($request->details);
            for ($i = 0; $i < $count; $i++) {
                if (isset($request->details[$i]['id']) && $request->details[$i]['id'] !='') {
                    $updateData = [
                        'shapping_section_id'     => $id,
                        'product_id'              => $request->details[$i]['product_id'],
                        'remarks'                 => $request->details[$i]['remarks'],
                        'shapping_product_weight' => $request->details[$i]['shapping_product_weight'],
                        'current_qty'             => $request->details[$i]['current_qty'],
                        'receive_qty'             => $request->details[$i]['receive_qty'],
                        'remain_qty'              => $request->details[$i]['remain_qty'],
                        'trans_to_dry_qty'        => $request->details[$i]['trans_to_dry_qty'],
                        'trans_to_dry_weight'     => $request->details[$i]['trans_to_dry_weight'],
                        'dry_westage_qty'         => $request->details[$i]['dry_westage_qty'],
                        'dry_westage_weight'      => $request->details[$i]['dry_westage_weight'],
                        'unit_price'              => $request->details[$i]['unit_price'],
                        'overhead_price'          => $request->details[$i]['overhead_price'],
                        'total_price'             => $request->details[$i]['total_price'],
                    ];
                    ProductionShappingDetails::where('id', $request->details[$i]['id'])->update($updateData);
                } else {
                    $dataFormat[$i] =[
                        'shapping_section_id'     => $id,
                        'product_id'              => $request->details[$i]['product_id'],
                        'remarks'                 => $request->details[$i]['remarks'],
                        'shapping_product_weight' => $request->details[$i]['shapping_product_weight'],
                        'current_qty'             => $request->details[$i]['current_qty'],
                        'receive_qty'             => $request->details[$i]['receive_qty'],
                        'remain_qty'              => $request->details[$i]['remain_qty'],
                        'trans_to_dry_qty'        => $request->details[$i]['trans_to_dry_qty'],
                        'trans_to_dry_weight'     => $request->details[$i]['trans_to_dry_weight'],
                        'dry_westage_qty'         => $request->details[$i]['dry_westage_qty'],
                        'dry_westage_weight'      => $request->details[$i]['dry_westage_weight'],
                        'unit_price'              => $request->details[$i]['unit_price'],
                        'overhead_price'          => $request->details[$i]['overhead_price'],
                        'total_price'             => $request->details[$i]['total_price'],
                    ];
                }
            }

            if(count($dataFormat) > 0){
                ProductionShappingDetails::insert($dataFormat);
            }

            if($app == 1){
                $this->approve($id);
            }
            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Shapping successfully updated!']);
        } catch (\Exception $e) {
            echo $e->getMessage();
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    public function approve($id)
    {
        $appData = ProductionShapping::FindOrFail($id);
        if ($appData->approve_status == 0) {

            $appDetailsData = ProductionShappingDetails::where('shapping_section_id', $id)
                                    ->get();
            $this->incrementDecrementShappingQTY($appDetailsData);


            if ($appData->weight_after_process_loss > 0) {
                ProductionRecycleChip::increment('recycle_chip_current_weight',$appData->weight_after_process_loss);


                $wastage_total_amount = $this->getWastageTotalAmout($appDetailsData);
                if ( $appData->process_loss_weight > 0 ) {
                    $wastage_price_per_kg = $wastage_total_amount / $appData->total_waste_weight;
                    $wastage_total_amount = $wastage_total_amount - ($appData->process_loss_weight * $wastage_price_per_kg);
                }
                ProductionRecycleChip::increment('recycle_chip_total_amt',$wastage_total_amount);

                $recycleChip = ProductionRecycleChip::First();
                $calc_unit_price = $recycleChip->recycle_chip_total_amt / $recycleChip->recycle_chip_current_weight;
                $recycleChip->recycle_chip_unit_price =  round($calc_unit_price, 2);
                $recycleChip->save();
            }


            $date = $appData ? $appData->shapping_date : date('Y-m-d');
            $log_type = StockTransactionType::$prodution_shapping;
            $sl = $appData->shapping_no;

            $ref_tbl = '';
            $ref_tbl_id = 0;
            $log_tbl = 'production_shappings';

            $sup_cus_tabl = '';
            $supplier = 0;
            $warehouseId = 0;
            $total_qty = $appData->total_receive_qty;


            /**************************************** STOCK LOG INFO START ********************************************************/
            $stock_log = [
                'stock_transection_logs_date' => $date,
                'stock_transection_logs_type' => $log_type,
                'stock_transection_logs_number' => $sl,
                'stock_transection_logs_ref_table_name' => $ref_tbl,
                'stock_transection_logs_ref_table_id' => $ref_tbl_id,
                'stock_transection_logs_table_name' => $log_tbl,
                'stock_transection_logs_table_id' => $id,
                'stock_trans_supp_cus_table_name' => $sup_cus_tabl,
                'stock_trans_supp_cus_table_id' => $supplier,
                'stock_trans_warehouse_id' => $warehouseId,
                'stock_trans_qty' => $total_qty,
                'created_by' => Auth::user()->id,
            ];
            $mainStock = StockTransectionLog::create($stock_log);


            $Stockdetails[] = '';
            $details_tbl = 'production_shapping_details';

            $a=0;
            foreach($appDetailsData as $row){
                $Stockdetails[$a] = [
                    'log_id'            => $mainStock->id,
                    'log_table_name'    => $details_tbl,
                    'log_table_id'      => $row->id,
                    'product_id'        => $row->product_id,
                    'warehouse_id'      => $warehouseId,
                    'log_entry_qty'     => $row->receive_qty,
                    'log_open_qty'      => $row->current_qty,
                    'log_current_qty'   => $row->remain_qty,
                    'log_close_qty'     => $row->remain_qty,
                    'entry_date'        => $date,
                    'created_by'        => Auth::user()->id,
                ];
                $a++;
            }
            StockTransectionLogDetails::insert($Stockdetails);

            $appData->approve_status = 1;
            $appData->approve_by = Auth::user()->id;
            $appData->approve_at = Carbon::now();
            $appData->save();
        }
    }

    private function getWastageTotalAmout($details)
    {
        $total_amount = 0;

        foreach($details as $item) {

            if ( $item['dry_westage_qty'] > 0) {

                $total_amount += $item['dry_westage_qty'] * $item['unit_price'];
            }
        }

        return $total_amount;
    }

    private function incrementDecrementShappingQTY($productArray){
        foreach($productArray as $val) {

            $checkExists = ProductionCurrentStock::where('product_id',$val['product_id'])->first();

            if($checkExists){

                ProductionCurrentStock::where('product_id', $val['product_id'])
                    ->update(
                        [ 'shapping_current_qty' => $val['remain_qty']]
                    );

                ProductionCurrentStock::where('product_id', $val['product_id'])
                    ->decrement('shapping_rececive_qty', $val['receive_qty']);
                ProductionCurrentStock::where('product_id', $val['product_id'])
                    ->increment('dryer_receive_qty', $val['trans_to_dry_qty']);

            }else{
                $shappingQty= [
                    'product_id' => $val['product_id'],
                    'shapping_current_qty' => $val['remain_qty'],
                    'shapping_rececive_qty' => $val['receive_qty'],
                    'dryer_receive_qty' => $val['trans_to_dry_qty'],
                ];
                ProductionCurrentStock::insert($shappingQty);

            }
        }
    }

    public function destroy($id)
    {
        try{

            ProductionShappingDetails::where('shapping_section_id',$id)->delete();
            ProductionShapping::FindOrFail($id)->delete();

            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->errorInfo[1];
        }


        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Shapping successfully Deleted !']);
        } elseif ($bug == 1451) {
            return response()->json(['status' => 'error', 'message' => 'This data is used another table,can not delete data']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    public function dataFormat($request, $id)
    {
        $dataFormat = [];
        $count = count($request->details);
        for ($i = 0; $i < $count; $i++) {
            $dataFormat[$i] = [
                'shapping_section_id'     => $id,
                'product_id'              => $request->details[$i]['product_id'],
                'remarks'                 => $request->details[$i]['remarks'],
                'shapping_product_weight' => $request->details[$i]['shapping_product_weight'],
                'current_qty'             => $request->details[$i]['current_qty'],
                'receive_qty'             => $request->details[$i]['receive_qty'],
                'remain_qty'              => $request->details[$i]['remain_qty'],
                'trans_to_dry_qty'        => $request->details[$i]['trans_to_dry_qty'],
                'trans_to_dry_weight'     => $request->details[$i]['trans_to_dry_weight'],
                'dry_westage_qty'         => $request->details[$i]['dry_westage_qty'],
                'dry_westage_weight'      => $request->details[$i]['dry_westage_weight'],
                'unit_price'              => $request->details[$i]['unit_price'],
                'overhead_price'          => $request->details[$i]['overhead_price'],
                'total_price'             => $request->details[$i]['total_price'],

            ];
        }

        return $dataFormat;
    }


    public function getPdfData($id)
    {
        $editModeData = ProductionShapping::FindOrFail($id);
        $editModeDetailsData = ProductionShappingDetails::with('product')->where('shapping_section_id',$id)->get();
        $data = [
            'id'                          => $editModeData->id,
            'shapping_no'                 => $editModeData->shapping_no,
            'shapping_date'               => dateConvertDBtoForm($editModeData->shapping_date),
            'narration'                   => $editModeData->narration,
            'total_trans_to_dryer_qty'    => $editModeData->total_trans_to_dryer_qty,
            'total_trans_to_dryer_weight' => $editModeData->total_trans_to_dryer_weight,
            'total_receive_qty'           => $editModeData->total_receive_qty,
            'total_remain_qty'            => $editModeData->total_remain_qty,
            'total_waste_qty'             => $editModeData->total_waste_qty,
            'total_waste_weight'          => $editModeData->total_waste_weight,
            'process_loss_percentage'     => $editModeData->process_loss_percentage,
            'process_loss_weight'         => $editModeData->process_loss_weight,
            'weight_after_process_loss'   => $editModeData->weight_after_process_loss,
            'details'                     => $editModeDetailsData
        ];

        return $data;

    }

    public function exportAsPdf($data)
    {

        $company = CompanyInfo::Find(1);

        $data = array(
            'company'   => $company,
            'shapping'  => $data,

        );

        $html = \View::make('production.production_section.product_pdf.shapping_sectionPDF')->with($data);

        $mpdf = new \Mpdf\Mpdf([
            'margin_left'   => 20,
            'margin_right'  => 15,
            'margin_top'    => 48,
            'margin_bottom' => 25,
            'margin_header' => 10,
            'margin_footer' => 10
        ]);

        $title = 'Shapping Section';
        $author = 'iVenture';
        if ($company) {
            $title .= ' - '.$company->name;
            $author = $company->name;
        }

        $mpdf->SetProtection(array('print'));
        $mpdf->SetTitle($title);
        $mpdf->SetAuthor($author);
        $mpdf->SetWatermarkText("Shapping Section");
        $mpdf->showWatermarkText = true;
        $mpdf->watermark_font = 'DejaVuSansCondensed';
        $mpdf->watermarkTextAlpha = 0.07;
        $mpdf->SetDisplayMode('fullpage');
        // $stylesheet = file_get_contents(public_path().'/css/mpdf.css');
        // $mpdf->WriteHTML($stylesheet,1);
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }

    private function deleteAppprovedShappingEntryData($id) {

        try{
            $data = ProductionShapping::FindOrFail($id);

            if ($data->weight_after_process_loss > 0) {
                ProductionRecycleChip::decrement('recycle_chip_current_weight',$data->weight_after_process_loss);
            }

            $results = ProductionShappingDetails::where('shapping_section_id',$id)->get();
            foreach ($results as $row) {

                ProductionCurrentStock::where('product_id', $row->product_id)
                    ->update(
                        [ 'shapping_current_qty' => $row->current_qty]
                    );

                ProductionCurrentStock::where('product_id', $row->product_id)
                    ->increment('shapping_rececive_qty', $row->receive_qty);
                ProductionCurrentStock::where('product_id', $row->product_id)
                    ->decrement('dryer_receive_qty', $row->trans_to_dry_qty);


                $row->delete();
            }


            $log = StockTransectionLog::where('stock_transection_logs_table_id',$id)
                                ->where('stock_transection_logs_type',StockTransactionType::$prodution_shapping)
                                ->where('stock_transection_logs_table_name','production_shappings')
                                ->First();
                if($log) {
                    StockTransectionLogDetails::where('log_id',$log->id)->delete();
                    $log->delete();
                }

            $data->delete();

            dd('Yes - deleted successfully, And also Recycle Chip was decremented!');
        }
        catch(\Exception $e){
            dd($e->getMessage());
        }
    }

    private function makeCorrectionShapping(Request $request, $id){
        try{
            DB::beginTransaction();
            $editModeData = ProductionShapping::FindOrFail($id);
            $editModeData->total_amount = $request->total_amount;
            $editModeData->narration    = $request->narration;
            $editModeData->save();

            $count = count($request->details);
            for ($i = 0; $i < $count; $i++) {
                if (isset($request->details[$i]['id']) && $request->details[$i]['id'] !='') {
                    $updateData = [
                        'unit_price'    => $request->details[$i]['unit_price'],
                        'total_price'   => $request->details[$i]['total_price'],
                        'remarks'       => $request->details[$i]['remarks']
                    ];
                    ProductionShappingDetails::where('id', $request->details[$i]['id'])->update($updateData);
                }
            }
            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Shapping Correction successfully Updated !']);
        }
        catch(\Exception $e){
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    /**
     * @ Get Ajax Last Production Info - Unit Price
     *
     * @param $product_id
     * @return Product row with last price
     */
    public function getShappingLastPrice($product_id)
    {
        $data = [];
        $data['unit_price'] = 0;

        $product = ProductionShappingDetails::where('product_id',$product_id)
                        ->where('approve_status',1)
                        ->leftJoin('production_shappings', 'production_shapping_details.shapping_section_id','=','production_shappings.id')
                        ->select('unit_price')
                        ->orderBy('shapping_date','DESC')
                        ->First();
        if ($product) {

            $data['unit_price'] = $product->unit_price;
        }
        else
        {
            $data['unit_price'] = ProductionProducts::where('id',$product_id)->first()->buying_price;
        }
        
        return $data;
    }
}
