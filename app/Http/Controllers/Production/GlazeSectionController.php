<?php

namespace App\Http\Controllers\Production;

use App\Model\CompanyInfo;
use App\Model\Production\ProductionCurrentStock;
use App\Model\Production\ProductionGlazeDetails;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Model\Production\ProductionGlaze;
use App\Model\Production\ProductionProducts;
use DB;

use App\Model\Inventory\StockTransectionLog;
use App\Model\Inventory\StockTransectionLogDetails;
use App\Lib\Enumerations\StockTransactionType;

use App\Model\Production\ProductionRecycleChip;

class GlazeSectionController extends Controller
{

    public function index(Request $request)
    {
        if ($request->filled('token_last_price') && $request->token_last_price == 'yes') {
            return $this->getGlazeLastPrice($request->product_id);
        }

        if ($request->ajax()) {

            $query = DB::table('production_glazes')
                ->leftJoin('users as ura', 'production_glazes.created_by','=','ura.id')
                ->leftJoin('users as ured', 'production_glazes.updated_by','=','ured.id')
                ->leftJoin('users as urea', 'production_glazes.approve_by','=','urea.id')
                ->whereNull('production_glazes.deleted_at')
                ->select(['production_glazes.id AS id',
                    'production_glazes.glaze_no',
                    'production_glazes.glaze_date',
                    'production_glazes.narration',
                    'production_glazes.created_by',
                    'production_glazes.updated_by',
                    'production_glazes.approve_by',
                    'production_glazes.approve_status',
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

        $productList  = ProductionProducts::select('production_products.id','production_products.product_name','production_products.forming_weight','production_products.buying_price','production_products.dryer_weight','production_products.glaze_weight','production_current_stocks.glaze_current_qty','production_current_stocks.glaze_receive_qty','production_products.is_raw_material_status')
                                ->leftJoin('production_current_stocks', 'production_current_stocks.product_id', '=', 'production_products.id')
                                ->where('production_products.status', '1')
                                ->get();
        return view('production.production_section.glaze_section',compact('productList'));
    }


    public function glazeNoGenerate(){
        $id = ProductionGlaze::withTrashed()->count();
        $currentId = $id+1;
        return 'GLAZE-'.date('Ym').$currentId;
    }

    public function approve($id)
    {

        $appData = ProductionGlaze::where('id', $id)->first();
        if ($appData->approve_status == 0) {

            $appDetailsData = ProductionGlazeDetails::where('glaze_section_id', $id)->get();
            $this->incrementDecrementGlazeQTY($appDetailsData);


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


            $date = $appData ? $appData->glaze_date : date('Y-m-d');
            $log_type = StockTransactionType::$prodution_glaze;
            $sl = $appData->glaze_no;

            $ref_tbl = '';
            $ref_tbl_id = 0;
            $log_tbl = 'production_glazes';

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
            $details_tbl = 'production_glaze_details';

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

            if ( $item['waste_qty'] > 0) {

                $total_amount += $item['waste_qty'] * $item['unit_price'];
            }
        }

        return $total_amount;
    }

    public function store(Request $request)
    {
        $inv_no_like = 'GLAZE-';
        $rtn_val =InvStore($request->glaze_no, $inv_no_like,
            'production_glazes','glaze_no');

        $request->merge(['glaze_no' => $rtn_val]) ;

        $this->validate($request, [
            'glaze_no' => 'required',
            'glaze_date' => 'required',
            'total_trans_to_kiln_qty' => 'required',
            'total_trans_to_kiln_weight' => 'required',

            'details.*.product_id' => 'required',
            'details.*.glaze_weight' => 'required',
            'details.*.current_qty' => 'required',
            'details.*.receive_qty' => 'required',
            'details.*.trans_kiln_qty' => 'required',
            'details.*.trans_kiln_weight' => 'required',
            'details.*.waste_qty' => 'required',
            'details.*.waste_weight' => 'required',
            'details.*.unit_price' => 'required',
        ], [

            'details.*.product_id.required' => 'Required field.',
            'details.*.glaze_weight.required' => 'Required field.',
            'details.*.current_qty.required' => 'Required field.',
            'details.*.receive_qty.required' => 'Required field.',
            'details.*.trans_kiln_qty.required' => 'Required field.',
            'details.*.trans_kiln_weight.required' => 'Required field.',
            'details.*.waste_qty.required' => 'Required field.',
            'details.*.waste_weight.required' => 'Required field.',
            'details.*.unit_price.required' => 'Required field.',
        ]);

        $overhead_info = [
            'last_month_overhead_amt'  => $request->last_month_overhead_amt,
            'last_month_production_kg' => $request->last_month_production_kg,
            'overhead_per_kg'          => $request->overhead_per_kg,
        ];

        $glaze_mpkg = $request->glaze_material_price_per_kg;

        $data = [
            'glaze_no'                   => $request->glaze_no,
            'glaze_date'                 => dateConvertFormtoDB($request->glaze_date),
            'narration'                  => $request->narration,
            'total_trans_to_kiln_qty'    => $request->total_trans_to_kiln_qty,
            'total_trans_to_kiln_weight' => $request->total_trans_to_kiln_weight,
            'total_waste_qty'            => $request->total_waste_qty,
            'total_waste_weight'         => $request->total_waste_weight,
            'total_receive_qty'          => $request->total_receive_qty,
            'total_remain_qty'           => $request->total_remain_qty,
            'process_loss_percentage'    => $request->process_loss_percentage,
            'process_loss_weight'        => $request->process_loss_weight,
            'weight_after_process_loss'  => $request->weight_after_process_loss,
            'glaze_material_price_per_kg'=> $glaze_mpkg,
            'total_amount'               => $request->total_amount,
            'overhead_info'              => json_encode($overhead_info),
            'wastage_overhead_amt'       => $request->weight_after_process_loss * $request->overhead_per_kg,
            'created_by'                 => Auth::user()->id,
        ];

        try {
            DB::beginTransaction();

            $app = $request->approve_status;

            $result = ProductionGlaze::create($data);
            $details = $this->dataFormat($request, $result->id);

            ProductionGlazeDetails::insert($details);

            if($app == 1){
                $this->approve($result->id);

                if($request->updated_by == Null){
                    DB::table('production_glazes')->where('id', $result->id)->update(array('updated_by' => Auth::user()->id));
                }
            }

            DB::commit();

            return response()->json(['status' => 'success', 'message' => "Glaze # " . $request->glaze_no .' Successfully Saved!']);
        } catch (\Exception $e) {
            print_r($e->getMessage());
            DB::rollback();

            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    private function incrementDecrementGlazeQTY($productArray){
        foreach($productArray as $val) {

            $checkExists = ProductionCurrentStock::where('product_id',$val['product_id'])->first();

            if($checkExists){

                ProductionCurrentStock::where('product_id', $val['product_id'])
                    ->update(
                        [ 'glaze_current_qty' => $val['remain_qty']]
                    );

                ProductionCurrentStock::where('product_id', $val['product_id'])
                    ->decrement('glaze_receive_qty', $val['receive_qty']);
                ProductionCurrentStock::where('product_id', $val['product_id'])
                    ->increment('kiln_receive_qty', $val['trans_kiln_qty']);

            }else{
                $glazeQty= [
                    'product_id' => $val['product_id'],
                    'glaze_current_qty' => $val['remain_qty'],
                    'glaze_receive_qty' => $val['receive_qty'],
                    'kiln_receive_qty' => $val['trans_kiln_qty'],
                ];
                ProductionCurrentStock::insert($glazeQty);
            }
        }
    }

    public function show($id)
    {
        try {
            $editModeData = ProductionGlaze::FindOrFail($id);
            $editModeDetailsData = ProductionGlazeDetails::with('product')->where('glaze_section_id',$id)->get();
            $data = [
                'id'                        => $editModeData->id,
                'glaze_no'                  => $editModeData->glaze_no,
                'glaze_date'                => dateConvertDBtoForm($editModeData->glaze_date),
                'narration'                 => $editModeData->narration,
                'total_trans_to_kiln_qty'   => $editModeData->total_trans_to_kiln_qty,
                'total_trans_to_kiln_weight'=> $editModeData->total_trans_to_kiln_weight,
                'total_waste_qty'           => $editModeData->total_waste_qty,
                'total_waste_weight'        => $editModeData->total_waste_weight,
                'total_receive_qty'         => $editModeData->total_receive_qty,
                'total_remain_qty'          => $editModeData->total_remain_qty,
                'process_loss_percentage'   => $editModeData->process_loss_percentage,
                'process_loss_weight'       => $editModeData->process_loss_weight,
                'weight_after_process_loss' => $editModeData->weight_after_process_loss,
                'total_qty'                 => $editModeData->total_qty,
                'total_amount'              => $editModeData->total_amount,
                'deleteID'                  => [],
                'details'                   => $editModeDetailsData
            ];
            return response()->json(['status'=>'success','data'=>$data]);
        } catch(\Exception $e){
            return response()->json(['status'=>'error','data'=>[]]);
        }
    }


    public function edit($id)
    {
        try {
            $editModeData = ProductionGlaze::FindOrFail($id);
            $editModeDetailsData = ProductionGlazeDetails::where('glaze_section_id',$id)->get();
            $data = [
                'id'                         => $editModeData->id,
                'glaze_no'                   => $editModeData->glaze_no,
                'glaze_date'                 => dateConvertDBtoForm($editModeData->glaze_date),
                'narration'                  => $editModeData->narration,
                'total_trans_to_kiln_qty'    => $editModeData->total_trans_to_kiln_qty,
                'total_trans_to_kiln_weight' => $editModeData->total_trans_to_kiln_weight,
                'total_waste_qty'            => $editModeData->total_waste_qty,
                'total_waste_weight'         => $editModeData->total_waste_weight,
                'total_receive_qty'          => $editModeData->total_receive_qty,
                'total_remain_qty'           => $editModeData->total_remain_qty,
                'process_loss_percentage'    => $editModeData->process_loss_percentage,
                'process_loss_weight'        => $editModeData->process_loss_weight,
                'weight_after_process_loss'  => $editModeData->weight_after_process_loss,
                'glaze_material_price_per_kg'=> $editModeData->glaze_material_price_per_kg,
                'total_amount'               => $editModeData->total_amount,
                'last_month_overhead_amt'    => '',
                'last_month_production_kg'   => '',
                'overhead_per_kg'            => '',
                'approve_status'             => '',
                'deleteID'                   => [],
                'details'                    => $editModeDetailsData
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
                    $this->makeCorrectionGlaze($request,$id);
                    return response()->json(['status' => 'success', 'message' => 'Glaze Correction successfully updated!']);
                //}
            }
        }
        $this->validate($request, [
            'glaze_no' => 'required',
            'glaze_date' => 'required',
            'total_trans_to_kiln_qty' => 'required',
            'total_trans_to_kiln_weight' => 'required',

            'details.*.product_id' => 'required',
            'details.*.glaze_weight' => 'required',
            'details.*.current_qty' => 'required',
            'details.*.receive_qty' => 'required',
            'details.*.trans_kiln_qty' => 'required',
            'details.*.trans_kiln_weight' => 'required',
            'details.*.waste_qty' => 'required',
            'details.*.waste_weight' => 'required',
            'details.*.unit_price' => 'required',
        ], [

            'details.*.product_id.required' => 'Required field.',
            'details.*.glaze_weight.required' => 'Required field.',
            'details.*.current_qty.required' => 'Required field.',
            'details.*.receive_qty.required' => 'Required field.',
            'details.*.trans_kiln_qty.required' => 'Required field.',
            'details.*.trans_kiln_weight.required' => 'Required field.',
            'details.*.waste_qty.required' => 'Required field.',
            'details.*.waste_weight.required' => 'Required field.',
            'details.*.unit_price.required' => 'Required field.',
        ]);


        $overhead_info = [
            'last_month_overhead_amt'  => $request->last_month_overhead_amt,
            'last_month_production_kg' => $request->last_month_production_kg,
            'overhead_per_kg'          => $request->overhead_per_kg,
        ];

        try {
            DB::beginTransaction();

            $app = $request->approve_status;

            $editModeData = ProductionGlaze::FindOrFail($id);

            $editModeData->glaze_no                    = $request->glaze_no;
            $editModeData->glaze_date                  = dateConvertFormtoDB($request->glaze_date);
            $editModeData->narration                   = $request->narration;
            $editModeData->total_trans_to_kiln_qty     = $request->total_trans_to_kiln_qty;
            $editModeData->total_trans_to_kiln_weight  = $request->total_trans_to_kiln_weight;
            $editModeData->total_receive_qty           = $request->total_receive_qty;
            $editModeData->total_remain_qty            = $request->total_remain_qty;
            $editModeData->total_waste_qty             = $request->total_waste_qty;
            $editModeData->total_waste_weight          = $request->total_waste_weight;
            $editModeData->process_loss_percentage     = $request->process_loss_percentage;
            $editModeData->process_loss_weight         = $request->process_loss_weight;
            $editModeData->weight_after_process_loss   = $request->weight_after_process_loss;
            $editModeData->glaze_material_price_per_kg = $request->glaze_material_price_per_kg;
            $editModeData->total_amount                = $request->total_amount;
            $editModeData->overhead_info               = json_encode($overhead_info);
            $editModeData->wastage_overhead_amt        = $request->weight_after_process_loss * $request->overhead_per_kg;

            if ($app!=1) {
                $editModeData->updated_by = Auth::user()->id;
            }

            $editModeData->save();

            /* Insert update and delete rm requisition details  */

            if (count($request->deleteID) > 0) {
                ProductionGlazeDetails::whereIn('id', $request->deleteID)->delete();
            }
            $dataFormat = [];
            $count = count($request->details);
            for ($i = 0; $i < $count; $i++) {
                if (isset($request->details[$i]['id']) && $request->details[$i]['id'] !='') {
                    $updateData = [
                        'glaze_section_id' => $id,
                        'product_id'       => $request->details[$i]['product_id'],
                        'remarks'          => $request->details[$i]['remarks'],
                        'glaze_weight'     => $request->details[$i]['glaze_weight'],
                        'glaze_mat_weight' => $request->details[$i]['glaze_mat_weight'],
                        'current_qty'      => $request->details[$i]['current_qty'],
                        'receive_qty'      => $request->details[$i]['receive_qty'],
                        'remain_qty'       => $request->details[$i]['remain_qty'],
                        'trans_kiln_qty'   => $request->details[$i]['trans_kiln_qty'],
                        'trans_kiln_weight'=> $request->details[$i]['trans_kiln_weight'],
                        'waste_qty'        => $request->details[$i]['waste_qty'],
                        'waste_weight'     => $request->details[$i]['waste_weight'],
                        'unit_price'       => $request->details[$i]['unit_price'],
                        'overhead_price'   => $request->details[$i]['overhead_price'],
                        'glaze_mat_price'  => $request->details[$i]['glaze_mat_price'],
                        'total_price'      => $request->details[$i]['total_price'],
                    ];
                    ProductionGlazeDetails::where('id', $request->details[$i]['id'])->update($updateData);
                } else {
                    $dataFormat[$i] =[
                        'glaze_section_id' => $id,
                        'product_id'       => $request->details[$i]['product_id'],
                        'remarks'          => $request->details[$i]['remarks'],
                        'glaze_weight'     => $request->details[$i]['glaze_weight'],
                        'glaze_mat_weight' => $request->details[$i]['glaze_mat_weight'],
                        'current_qty'      => $request->details[$i]['current_qty'],
                        'receive_qty'      => $request->details[$i]['receive_qty'],
                        'remain_qty'       => $request->details[$i]['remain_qty'],
                        'trans_kiln_qty'   => $request->details[$i]['trans_kiln_qty'],
                        'trans_kiln_weight'=> $request->details[$i]['trans_kiln_weight'],
                        'waste_qty'        => $request->details[$i]['waste_qty'],
                        'waste_weight'     => $request->details[$i]['waste_weight'],
                        'unit_price'       => $request->details[$i]['unit_price'],
                        'overhead_price'   => $request->details[$i]['overhead_price'],
                        'glaze_mat_price'  => $request->details[$i]['glaze_mat_price'],
                        'total_price'      => $request->details[$i]['total_price'],
                    ];
                }
            }

            if(count($dataFormat) > 0){
                ProductionGlazeDetails::insert($dataFormat);
            }
            if($app == 1){
                $this->approve($id);
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Glaze successfully updated!']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }


    public function destroy($id)
    {
        try{

            ProductionGlazeDetails::where('glaze_section_id',$id)->delete();
            ProductionGlaze::FindOrFail($id)->delete();

            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->errorInfo[1];
        }


        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Glaze successfully Deleted !']);
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
                'glaze_section_id' => $id,
                'product_id'       => $request->details[$i]['product_id'],
                'remarks'          => $request->details[$i]['remarks'],
                'glaze_weight'     => $request->details[$i]['glaze_weight'],
                'glaze_mat_weight' => $request->details[$i]['glaze_mat_weight'],
                'current_qty'      => $request->details[$i]['current_qty'],
                'receive_qty'      => $request->details[$i]['receive_qty'],
                'remain_qty'       => $request->details[$i]['remain_qty'],
                'trans_kiln_qty'   => $request->details[$i]['trans_kiln_qty'],
                'trans_kiln_weight'=> $request->details[$i]['trans_kiln_weight'],
                'waste_qty'        => $request->details[$i]['waste_qty'],
                'waste_weight'     => $request->details[$i]['waste_weight'],
                'unit_price'       => $request->details[$i]['unit_price'],
                'overhead_price'   => $request->details[$i]['overhead_price'],
                'glaze_mat_price'  => $request->details[$i]['glaze_mat_price'],
                'total_price'      => $request->details[$i]['total_price'],
            ];
        }

        return $dataFormat;
    }

    public function getPdfData($id)
    {
        $editModeData = ProductionGlaze::FindOrFail($id);
        $editModeDetailsData = ProductionGlazeDetails::with('product')->where('glaze_section_id',$id)->get();
        $data = [
            'id'                        => $editModeData->id,
            'glaze_no'                  => $editModeData->glaze_no,
            'glaze_date'                => dateConvertDBtoForm($editModeData->glaze_date),
            'narration'                 => $editModeData->narration,
            'total_trans_to_kiln_qty'   => $editModeData->total_trans_to_kiln_qty,
            'total_trans_to_kiln_weight'=> $editModeData->total_trans_to_kiln_weight,
            'total_waste_qty'           => $editModeData->total_waste_qty,
            'total_waste_weight'        => $editModeData->total_waste_weight,
            'total_receive_qty'         => $editModeData->total_receive_qty,
            'total_remain_qty'          => $editModeData->total_remain_qty,
            'process_loss_percentage'   => $editModeData->process_loss_percentage,
            'process_loss_weight'       => $editModeData->process_loss_weight,
            'weight_after_process_loss' => $editModeData->weight_after_process_loss,
            'details'                   => $editModeDetailsData
        ];

        return $data;

    }

    public function exportAsPdf($data)
    {

        $company = CompanyInfo::Find(1);

        $data = array(
            'company'=> $company,
            'glaze'  => $data,

        );

        $html = \View::make('production.production_section.product_pdf.glaze_sectionPDF')->with($data);

        $mpdf = new \Mpdf\Mpdf([
            'margin_left'   => 20,
            'margin_right'  => 15,
            'margin_top'    => 48,
            'margin_bottom' => 25,
            'margin_header' => 10,
            'margin_footer' => 10
        ]);

        $title = 'Glaze Section';
        $author = 'iVenture';
        if ($company) {
            $title .= ' - '.$company->name;
            $author = $company->name;
        }

        $mpdf->SetProtection(array('print'));
        $mpdf->SetTitle($title);
        $mpdf->SetAuthor($author);
        $mpdf->SetWatermarkText("Glaze Section");
        $mpdf->showWatermarkText = true;
        $mpdf->watermark_font = 'DejaVuSansCondensed';
        $mpdf->watermarkTextAlpha = 0.07;
        $mpdf->SetDisplayMode('fullpage');
        // $stylesheet = file_get_contents(public_path().'/css/mpdf.css');
        // $mpdf->WriteHTML($stylesheet,1);
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }

    private function makeCorrectionGlaze(Request $request, $id){
        try{
            DB::beginTransaction();
            $editModeData = ProductionGlaze::FindOrFail($id);
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
                    ProductionGlazeDetails::where('id', $request->details[$i]['id'])->update($updateData);
                }
            }
            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Glaze Correction successfully Updated !']);
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
    public function getGlazeLastPrice($product_id)
    {
        $data = [];
        $data['unit_price']  = 0;
        $data['kiln_weight'] = 0;

        $product = ProductionGlazeDetails::where('product_id',$product_id)
                        ->where('approve_status',1)
                        ->leftJoin('production_glazes', 'production_glaze_details.glaze_section_id','=','production_glazes.id')
                        ->select('unit_price')
                        ->orderBy('glaze_date','DESC')
                        ->First();
        if ($product) {

            $data['unit_price'] = $product->unit_price;
        }
        else
        {
            $data['unit_price'] = ProductionProducts::where('id',$product_id)->first()->buying_price;
        }

        $data['kiln_weight'] = ProductionProducts::where('id',$product_id)->first()->kiln_weight;

        return $data;
    }
}
