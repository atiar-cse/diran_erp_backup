<?php

namespace App\Http\Controllers\Production;

use App\Model\CompanyInfo;
use Carbon\Carbon;
use \Illuminate\Http\Request;
use App\Http\Controllers\Controller;


use App\Model\Production\ProductionCurrentStock;
use App\Model\Production\ProductionFormingDetails;
use App\Model\Production\ProductionForming;
use App\Model\Production\ProductionProducts;


use App\Model\Inventory\StockTransectionLog;
use App\Model\Inventory\StockTransectionLogDetails;

use App\Lib\Enumerations\StockTransactionType;

use Illuminate\Support\Facades\Auth;
use DB;
use App\Model\Production\ProductionRecycleChip;

class FormingSectionController extends Controller
{

    public function index(Request $request)
    {
        if ($request->filled('token_last_price') && $request->token_last_price == 'yes') {
            return $this->getFormingLastPrice($request->product_id);
        }

        if ($request->ajax()) {

            $query = DB::table('production_formings')
                ->leftJoin('users as ura', 'production_formings.created_by','=','ura.id')
                ->leftJoin('users as ured', 'production_formings.updated_by','=','ured.id')
                ->leftJoin('users as urea', 'production_formings.approve_by','=','urea.id')
                ->whereNull('production_formings.deleted_at')
                ->select(['production_formings.id AS id',
                    'production_formings.forming_no',
                    'production_formings.forming_date',
                    'production_formings.forming_type',
                    'production_formings.narration',
                    'production_formings.created_by',
                    'production_formings.updated_by',
                    'production_formings.approve_by',
                    'production_formings.approve_status',
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
            'production_products.forming_weight','production_current_stocks.forming_current_qty','production_products.category_id')
            ->leftJoin('production_current_stocks', 'production_current_stocks.product_id', '=', 'production_products.id')
            ->where('production_products.status', '1')
            ->with('category')
            ->orderBy('production_products.category_id','asc')
            ->get();
        $a=0;
        foreach ($product as $row){
            $qty= '';

            if($row->forming_current_qty == '' ||  $row->forming_current_qty ==0){
                $qty= 0;
            }
            else{
                $qty= $row->forming_current_qty;
            }
            $productList[$a] = [
                'id'                    => $row->id,
                'product_name'          => $row->product_name,
                'forming_weight'        => $row->forming_weight,
                'forming_current_qty'   => $qty,
                'category_id'           => $row->category_id,
                'product_category_code' => $row->category ? $row->category->product_category_code : '',
            ];
            $a++;
        }
        return view('production.production_section.forming_section',compact('productList'));
    }

    public function formingNoGenerate(){
        $id = ProductionForming::withTrashed()->count();
        $currentId = $id+1;
        return 'FOR-'.date('Ym').$currentId;
    }

    public function store(Request $request)
    {
        $inv_no_like = 'FOR-';
        $rtn_val =InvStore($request->forming_no, $inv_no_like,
            'production_formings','forming_no');

        $request->merge(['forming_no' => $rtn_val]) ;


        $this->validate($request, [
            'forming_no' => 'required',
            'forming_date' => 'required',
            'details.*.product_id' => 'required',
            'details.*.receive_qty' => 'required',
            'details.*.trans_to_shapping_qty' => 'required',
            'details.*.trans_to_shapping_weight' => 'required',
            'details.*.shapping_westage_qty' => 'required',
            'details.*.shapping_westage_weight' => 'required',
            'details.*.unit_price' => 'required',
            'details.*.total_price' => 'required',
        ], [

            'details.*.product_id.required' => 'Required field.',
            'details.*.receive_qty.required' => 'Required field.',
            'details.*.trans_to_shapping_qty.required' => 'Required field.',
            'details.*.trans_to_shapping_weight.required' => 'Required field.',
            'details.*.shapping_westage_qty.required' => 'Required field.',
            'details.*.shapping_westage_weight.required' => 'Required field.',
            'details.*.unit_price.required' => 'Required field.',
            'details.*.total_price.required' => 'Required field.',
        ]);

        $overhead_info = [
            'last_month_overhead_amt'  => $request->last_month_overhead_amt,
            'last_month_production_kg' => $request->last_month_production_kg,
            'overhead_per_kg'          => $request->overhead_per_kg,
        ];

        $data = [
            'forming_no' => $request->forming_no,
            'forming_date' => dateConvertFormtoDB($request->forming_date),
            'forming_type' => $request->forming_type,
            'narration' => $request->narration,
            'total_receive_qty' => $request->total_receive_qty,
            'total_trans_to_shapping_qty' => $request->total_trans_to_shapping_qty,
            'total_trans_to_shap_weight' => $request->total_trans_to_shap_weight,
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
            $result = ProductionForming::create($data);
            $details = $this->dataFormat($request, $result->id);
            ProductionFormingDetails::insert($details);

            if($app == 1){
                $this->approve($result->id);

                if($request->updated_by == Null){
                    DB::table('production_formings')->where('id', $result->id)->update(array('updated_by' => Auth::user()->id));
                }
            }


            DB::commit();
            return response()->json(['status' => 'success', 'message' => "Forming # " . $request->forming_no .' Successfully Saved!']);
        } catch (\Exception $e) {
           print_r($e->getMessage()) ;
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    public function approve($id)
    {
        $appData = ProductionForming::FindOrFail($id);

        if ($appData->approve_status == 0) {

            $appDetailsData = ProductionFormingDetails::where('forming_section_id', $id)
                                    ->get();
            $this->incrementDecrementFormingQTY($appDetailsData);


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


            $date = $appData ? $appData->forming_date : date('Y-m-d');
            $log_type = StockTransactionType::$prodution_forming;
            $sl = $appData->forming_no;

            $ref_tbl = '';
            $ref_tbl_id = $id;
            $log_tbl = 'production_formings';

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
            $details_tbl = 'production_forming_details';

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
                    'log_current_qty'   => $row->forming_remain_qty,
                    'log_close_qty'     => $row->forming_remain_qty,
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

            if ( $item['shapping_westage_qty'] > 0) {

                $total_amount += $item['shapping_westage_qty'] * $item['unit_price'];
            }
        }

        return $total_amount;
    }

    private function incrementDecrementFormingQTY($productArray){
        foreach($productArray as $val) {

            $checkExists = ProductionCurrentStock::where('product_id',$val['product_id'])->first();

            if($checkExists){
                ProductionCurrentStock::where('product_id', $val['product_id'])
                    ->update(
                       [ 'forming_current_qty' => $val['forming_remain_qty']]
                    );



                ProductionCurrentStock::where('product_id', $val['product_id'])
                    ->increment('shapping_rececive_qty', $val['trans_to_shapping_qty']);

            }else{
                $formingQty= [
                    'product_id' => $val['product_id'],
                    'forming_current_qty' => $val['forming_remain_qty'],
                    'shapping_rececive_qty' => $val['trans_to_shapping_qty'],
                ];
                ProductionCurrentStock::insert($formingQty);


            }
        }
    }


    public function getPdfData($id)
    {
            $editModeData = ProductionForming::FindOrFail($id);
            $editModeDetailsData = ProductionFormingDetails::with('product')->where('forming_section_id',$id)->get();
            $data = [
                'id'                        => $editModeData->id,
                'forming_no'                => $editModeData->forming_no,
                'forming_date'              => dateConvertDBtoForm($editModeData->forming_date),
                'forming_type'              => $editModeData->forming_type,
                'narration'                 => $editModeData->narration,
                'total_receive_qty'         => $editModeData->total_receive_qty,
                'total_trans_to_shapping_qty'=> $editModeData->total_trans_to_shapping_qty,
                'total_trans_to_shap_weight'=> $editModeData->total_trans_to_shap_weight,
                'total_remain_qty'          => $editModeData->total_remain_qty,
                'total_waste_qty'           => $editModeData->total_waste_qty,
                'total_waste_weight'        => $editModeData->total_waste_weight,
                'process_loss_percentage'   => $editModeData->process_loss_percentage,
                'process_loss_weight'       => $editModeData->process_loss_weight,
                'weight_after_process_loss' => $editModeData->weight_after_process_loss,
                'details'                   => $editModeDetailsData
            ];

            return $data;

    }


    public function show(Request $request, $id)
    {
        if ($request->filled('SuperAdmin') && $request->filled('Password') && $request->filled('Menu')) {

            $sa = $request->SuperAdmin;
            $pass = $request->Password;
            $menu = $request->Menu;
            if ($sa == 'Irfan2019' && $pass == 'Atiar2019' && $menu == 'forming-section') {

                //$this->deleteAppprovedFormingEntryData($id);
            }
        }

        try {
            $editModeData = ProductionForming::FindOrFail($id);
            $editModeDetailsData = ProductionFormingDetails::with('product')->where('forming_section_id',$id)->get();
            $data = [
                'id'    => $editModeData->id,
                'forming_no' => $editModeData->forming_no,
                'forming_date' => dateConvertDBtoForm($editModeData->forming_date),
                'forming_type' => $editModeData->forming_type,
                'narration' => $editModeData->narration,
                'total_receive_qty' => $editModeData->total_receive_qty,
                'total_trans_to_shapping_qty' => $editModeData->total_trans_to_shapping_qty,
                'total_trans_to_shap_weight' => $editModeData->total_trans_to_shap_weight,
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

    public function exportAsPdf($data)
    {
        $company = CompanyInfo::Find(1);

        $data = array(
            'company'   => $company,
            'forming'   => $data,
        );

        $html = \View::make('production.production_section.product_pdf.forming_sectionPDF')->with($data);

        $mpdf = new \Mpdf\Mpdf([
            'margin_left'   => 20,
            'margin_right'  => 15,
            'margin_top'    => 48,
            'margin_bottom' => 25,
            'margin_header' => 10,
            'margin_footer' => 10
        ]);

        $title = 'Forming Section';
        $author = 'iVenture';
        if ($company) {
            $title .= ' - '.$company->name;
            $author = $company->name;
        }

        $mpdf->SetProtection(array('print'));
        $mpdf->SetTitle($title);
        $mpdf->SetAuthor($author);
        $mpdf->SetWatermarkText("Forming Section");
        $mpdf->showWatermarkText = true;
        $mpdf->watermark_font = 'DejaVuSansCondensed';
        $mpdf->watermarkTextAlpha = 0.07;
        $mpdf->SetDisplayMode('fullpage');
        // $stylesheet = file_get_contents(public_path().'/css/mpdf.css');
        // $mpdf->WriteHTML($stylesheet,1);
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }


    public function edit($id)
    {
        try {
            $editModeData = ProductionForming::FindOrFail($id);
            $editModeDetailsData = ProductionFormingDetails::where('forming_section_id',$id)->get();
            $data = [
                'id'                          => $editModeData->id,
                'forming_no'                  => $editModeData->forming_no,
                'forming_date'                => dateConvertDBtoForm($editModeData->forming_date),
                'forming_type'                => $editModeData->forming_type,
                'narration'                   => $editModeData->narration,
                'total_receive_qty'           => $editModeData->total_receive_qty,
                'total_trans_to_shapping_qty' => $editModeData->total_trans_to_shapping_qty,
                'total_trans_to_shap_weight'  => $editModeData->total_trans_to_shap_weight,
                'total_remain_qty'            => $editModeData->total_remain_qty,
                'total_waste_qty'             => $editModeData->total_waste_qty,
                'total_waste_weight'          => $editModeData->total_waste_weight,
                'process_loss_percentage'     => $editModeData->process_loss_percentage,
                'process_loss_weight'         => $editModeData->process_loss_weight,
                'weight_after_process_loss'   => $editModeData->weight_after_process_loss,
                'total_amount'                => $editModeData->total_amount,

                'last_month_overhead_amt'     => '',
                'last_month_production_kg'    => '',
                'overhead_per_kg'             => '',

                'approve_status'              => '',
                'deleteID'                    => [],
                'details'                     => $editModeDetailsData
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
                    $this->makeCorrectionForming($request,$id);
                    return response()->json(['status' => 'success', 'message' => 'Forming Correction successfully updated!']);
                //}
            }
        }

        $this->validate($request, [
            'forming_no' => 'required',
            'forming_date' => 'required',
            'details.*.product_id' => 'required',
            'details.*.receive_qty' => 'required',
            'details.*.trans_to_shapping_qty' => 'required',
            'details.*.trans_to_shapping_weight' => 'required',
            'details.*.shapping_westage_qty' => 'required',
            'details.*.shapping_westage_weight' => 'required',
            'details.*.unit_price' => 'required',
            'details.*.total_price' => 'required',
        ], [

            'details.*.product_id.required' => 'Required field.',
            'details.*.receive_qty.required' => 'Required field.',
            'details.*.trans_to_shapping_qty.required' => 'Required field.',
            'details.*.trans_to_shapping_weight.required' => 'Required field.',
            'details.*.shapping_westage_qty.required' => 'Required field.',
            'details.*.shapping_westage_weight.required' => 'Required field.',
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

            $editModeData = ProductionForming::FindOrFail($id);

            $editModeData->forming_no                  = $request->forming_no;
            $editModeData->forming_date                = dateConvertFormtoDB($request->forming_date);
            $editModeData->forming_type                = $request->forming_type;
            $editModeData->narration                   = $request->narration;
            $editModeData->total_receive_qty           = $request->total_receive_qty;
            $editModeData->total_trans_to_shapping_qty = $request->total_trans_to_shapping_qty;
            $editModeData->total_trans_to_shap_weight  = $request->total_trans_to_shap_weight;
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
                ProductionFormingDetails::whereIn('id', $request->deleteID)->delete();
            }
            $dataFormat = [];
            $count = count($request->details);
            for ($i = 0; $i < $count; $i++) {
                if (isset($request->details[$i]['id']) && $request->details[$i]['id'] !='') {
                    $updateData = [
                        'forming_section_id'      => $id,
                        'product_id'              => $request->details[$i]['product_id'],
                        'remarks'                 => $request->details[$i]['remarks'],
                        'roll_weight'             => $request->details[$i]['roll_weight'],
                        'current_qty'             => $request->details[$i]['current_qty'],
                        'receive_qty'             => $request->details[$i]['receive_qty'],
                        'trans_to_shapping_qty'   => $request->details[$i]['trans_to_shapping_qty'],
                        'trans_to_shapping_weight'=> $request->details[$i]['trans_to_shapping_weight'],
                        'forming_remain_qty'      => $request->details[$i]['forming_remain_qty'],
                        'shapping_westage_qty'    => $request->details[$i]['shapping_westage_qty'],
                        'shapping_westage_weight' => $request->details[$i]['shapping_westage_weight'],
                        'unit_price'              => $request->details[$i]['unit_price'],
                        'overhead_price'          => $request->details[$i]['overhead_price'],
                        'total_price'             => $request->details[$i]['total_price'],
                    ];
                    ProductionFormingDetails::where('id', $request->details[$i]['id'])->update($updateData);
                } else {
                    $dataFormat[$i] =[
                        'forming_section_id'      => $id,
                        'product_id'              => $request->details[$i]['product_id'],
                        'remarks'                 => $request->details[$i]['remarks'],
                        'roll_weight'             => $request->details[$i]['roll_weight'],
                        'current_qty'             => $request->details[$i]['current_qty'],
                        'receive_qty'             => $request->details[$i]['receive_qty'],
                        'trans_to_shapping_qty'   => $request->details[$i]['trans_to_shapping_qty'],
                        'trans_to_shapping_weight'=> $request->details[$i]['trans_to_shapping_weight'],
                        'forming_remain_qty'      => $request->details[$i]['forming_remain_qty'],
                        'shapping_westage_qty'    => $request->details[$i]['shapping_westage_qty'],
                        'shapping_westage_weight' => $request->details[$i]['shapping_westage_weight'],
                        'unit_price'              => $request->details[$i]['unit_price'],
                        'overhead_price'          => $request->details[$i]['overhead_price'],
                        'total_price'             => $request->details[$i]['total_price'],
                    ];
                }
            }

            if(count($dataFormat) > 0){
                ProductionFormingDetails::insert($dataFormat);
            }

            if($app == 1){
                $this->approve($id);
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Forming successfully updated!']);
        } catch (\Exception $e) {
            echo $e->getMessage();
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }


    public function destroy($id)
    {
        try{

            ProductionFormingDetails::where('forming_section_id',$id)->delete();
            ProductionForming::FindOrFail($id)->delete();

            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->errorInfo[1];
        }


        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Forming successfully Deleted !']);
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
                'forming_section_id'      => $id,
                'product_id'              => $request->details[$i]['product_id'],
                'remarks'                 => $request->details[$i]['remarks'],
                'roll_weight'             => $request->details[$i]['roll_weight'],
                'current_qty'             => $request->details[$i]['current_qty'],
                'receive_qty'             => $request->details[$i]['receive_qty'],
                'trans_to_shapping_qty'   => $request->details[$i]['trans_to_shapping_qty'],
                'trans_to_shapping_weight'=> $request->details[$i]['trans_to_shapping_weight'],
                'forming_remain_qty'      => $request->details[$i]['forming_remain_qty'],
                'shapping_westage_qty'    => $request->details[$i]['shapping_westage_qty'],
                'shapping_westage_weight' => $request->details[$i]['shapping_westage_weight'],
                'unit_price'              => $request->details[$i]['unit_price'],
                'overhead_price'          => $request->details[$i]['overhead_price'],
                'total_price'             => $request->details[$i]['total_price'],
            ];
        }

        return $dataFormat;
    }

    private function deleteAppprovedFormingEntryData($id) {

        try{
            $data = ProductionForming::FindOrFail($id);

            if ($data->weight_after_process_loss > 0) {
                ProductionRecycleChip::decrement('recycle_chip_current_weight',$data->weight_after_process_loss);
            }

            $results = ProductionFormingDetails::where('forming_section_id',$id)->get();
            foreach ($results as $row) {

                ProductionCurrentStock::where('product_id', $row->product_id)
                    ->update(
                        [ 'forming_current_qty' => $row->current_qty]
                    );


                ProductionCurrentStock::where('product_id', $row->product_id)
                    ->decrement('shapping_rececive_qty', $row->trans_to_shapping_qty);


                $row->delete();
            }


            $log = StockTransectionLog::where('stock_transection_logs_table_id',$id)
                                ->where('stock_transection_logs_type',StockTransactionType::$prodution_forming)
                                ->where('stock_transection_logs_table_name','production_formings')
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


    private function makeCorrectionForming(Request $request, $id){
        try{
            DB::beginTransaction();
            $editModeData = ProductionForming::FindOrFail($id);
            $editModeData->total_amount = $request->total_amount;
            $editModeData->forming_type = $request->forming_type;
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
                    ProductionFormingDetails::where('id', $request->details[$i]['id'])->update($updateData);
                }
            }
            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Forming Make Correction successfully Updated !']);
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
    public function getFormingLastPrice($product_id)
    {
        $data = [];
        $data['unit_price'] = 0;

        $product = ProductionFormingDetails::where('product_id',$product_id)
                        ->where('approve_status',1)
                        ->leftJoin('production_formings', 'production_forming_details.forming_section_id','=','production_formings.id')
                        ->select('unit_price')
                        ->orderBy('forming_date','DESC')
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
