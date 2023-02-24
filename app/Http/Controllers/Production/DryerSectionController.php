<?php

namespace App\Http\Controllers\Production;

use App\Model\CompanyInfo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


use App\Model\Production\ProductionCurrentStock;
use App\Model\Production\ProductionDryerDetails;
use App\Model\Production\ProductionDryer;
use App\Model\Production\ProductionProducts;

use App\Model\Inventory\StockTransectionLog;
use App\Model\Inventory\StockTransectionLogDetails;

use App\Lib\Enumerations\StockTransactionType;

use Illuminate\Support\Facades\Auth;
use DB;

class DryerSectionController extends Controller
{

    public function index(Request $request)
    {
        if ($request->filled('token_last_price') && $request->token_last_price == 'yes') {
            return $this->getDryerLastPrice($request->product_id);
        }

        if ($request->ajax()) {

            $query = DB::table('production_dryers')
                ->leftJoin('users as ura', 'production_dryers.created_by','=','ura.id')
                ->leftJoin('users as ured', 'production_dryers.updated_by','=','ured.id')
                ->leftJoin('users as urea', 'production_dryers.approve_by','=','urea.id')
                ->whereNull('production_dryers.deleted_at')
                ->select(['production_dryers.id AS id',
                    'production_dryers.dryer_no',
                    'production_dryers.dryer_date',
                    'production_dryers.narration',
                    'production_dryers.created_by',
                    'production_dryers.updated_by',
                    'production_dryers.approve_by',
                    'production_dryers.approve_status',
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
            'production_products.dryer_weight','production_current_stocks.dryer_current_qty','production_current_stocks.dryer_receive_qty',
            'production_products.category_id')
            ->leftJoin('production_current_stocks', 'production_current_stocks.product_id', '=', 'production_products.id')
            ->where('production_products.status', '1')
            ->with('category')
            ->orderBy('production_products.category_id','asc')
            //->where('production_products.is_raw_material_status', '0')
            ->get();
        $a=0;
        foreach ($product as $row){
            $qty= '';
            if($row->dryer_current_qty == '' ||  $row->dryer_current_qty == 0){
                $qty= 0;
            }
            else{
                $qty= $row->dryer_current_qty;
            }
            $productList[$a] = [
                'id'                    => $row->id,
                'product_name'          => $row->product_name,
                'dryer_weight'          => $row->dryer_weight,
                'dryer_current_qty'     => $qty,
                'dryer_receive_qty'     => $row->dryer_receive_qty ? $row->dryer_receive_qty : 0,
                'category_id'           => $row->category_id,
                'product_category_code' => $row->category ? $row->category->product_category_code : '',
            ];
            $a++;
        }
        return view('production.production_section.dryer_section',compact('productList'));
    }

    public function dryerNoGenerate(){
        $id = ProductionDryer::withTrashed()->count();
        $currentId = $id+1;
        return 'DRY-'.date('Ym').$currentId;
    }

    public function approve($id)
    {
        $appData = ProductionDryer::where('id', $id)->first();
        if ($appData->approve_status == 0) {
            $appDetailsData = ProductionDryerDetails::where('dryer_section_id', $id)->get();
            $this->incrementDecrementDryerQTY($appDetailsData);


            $date = $appData ? $appData->dryer_date : date('Y-m-d');
            $log_type = StockTransactionType::$prodution_dryer;
            $sl = $appData->dryer_no;

            $ref_tbl = '';
            $ref_tbl_id = 0;
            $log_tbl = 'production_dryers';

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
            $details_tbl = 'production_dryer_details';

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


    public function store(Request $request)
    {
        $inv_no_like = 'DRY-';
        $rtn_val =InvStore($request->dryer_no, $inv_no_like,
            'production_dryers','dryer_no');

        $request->merge(['dryer_no' => $rtn_val]) ;


        $this->validate($request, [
            'dryer_no' => 'required',
            'dryer_date' => 'required',
            'total_trans_to_glaze_qty' => 'required',
            'total_trans_to_glaze_weight' => 'required',

            'details.*.product_id' => 'required',
            'details.*.dryer_product_weight' => 'required',
            'details.*.current_qty' => 'required',
            'details.*.receive_qty' => 'required',
            'details.*.trans_to_glaze_qty' => 'required',
            'details.*.trans_to_glaze_weight' => 'required',
            'details.*.unit_price' => 'required',
            'details.*.total_price' => 'required',
        ], [

            'details.*.product_id.required' => 'Required field.',
            'details.*.dryer_product_weight.required' => 'Required field.',
            'details.*.current_qty.required' => 'Required field.',
            'details.*.receive_qty.required' => 'Required field.',
            'details.*.trans_to_glaze_qty.required' => 'Required field.',
            'details.*.trans_to_glaze_weight.required' => 'Required field.',
            'details.*.unit_price.required' => 'Required field.',
            'details.*.total_price.required' => 'Required field.',
        ]);

        $overhead_info = [
            'last_month_overhead_amt'  => $request->last_month_overhead_amt,
            'last_month_production_kg' => $request->last_month_production_kg,
            'overhead_per_kg'          => $request->overhead_per_kg,
        ];

        $data = [
            'dryer_no' => $request->dryer_no,
            'dryer_date' => dateConvertFormtoDB($request->dryer_date),
            'narration' => $request->narration,

            'total_receive_qty' => $request->total_receive_qty,
            'total_remain_qty' => $request->total_remain_qty,
            'total_waste_qty' => $request->total_waste_qty,
            'total_waste_weight' => $request->total_waste_weight,

            'total_trans_to_glaze_qty' => $request->total_trans_to_glaze_qty,
            'total_trans_to_glaze_weight' => $request->total_trans_to_glaze_weight,
            'total_amount' => $request->total_amount,
            'overhead_info' => json_encode($overhead_info),
            'wastage_overhead_amt' => $request->weight_after_process_loss * $request->overhead_per_kg,
            'created_by' => Auth::user()->id,
        ];

        try {
            DB::beginTransaction();

            $app = $request->approve_status;

            $result = ProductionDryer::create($data);
            $details = $this->dataFormat($request, $result->id);

            ProductionDryerDetails::insert($details);

            if($app == 1){
                $this->approve($result->id);

                if($request->updated_by == Null){
                    DB::table('production_dryers')->where('id', $result->id)->update(array('updated_by' => Auth::user()->id));
                }
            }
            //$this->incrementDecrementDryerQTY($details);

            DB::commit();
            return response()->json(['status' => 'success', 'message' => "Dryer # " . $request->dryer_no .' Successfully Saved!']);
        } catch (\Exception $e) {
            print_r($e->getMessage());
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    private function incrementDecrementDryerQTY($productArray){
        foreach($productArray as $val) {

            $checkExists = ProductionCurrentStock::where('product_id',$val['product_id'])->first();

            if($checkExists){
                ProductionCurrentStock::where('product_id', $val['product_id'])
                    ->update(
                        [ 'dryer_current_qty' => $val['remain_qty']]
                    );

                ProductionCurrentStock::where('product_id', $val['product_id'])
                    ->decrement('dryer_receive_qty', $val['receive_qty']);
                ProductionCurrentStock::where('product_id', $val['product_id'])
                    ->increment('glaze_receive_qty', $val['trans_to_glaze_qty']);

            }else{
                $dryerQty= [
                    'product_id' => $val['product_id'],
                    'dryer_current_qty' => $val['remain_qty'],
                    'dryer_receive_qty' => $val['receive_qty'],
                    'glaze_receive_qty' => $val['trans_to_glaze_qty'],
                ];
                ProductionCurrentStock::insert($dryerQty);

            }
        }
    }

    public function show($id)
    {
        try {
            $editModeData = ProductionDryer::FindOrFail($id);
            $editModeDetailsData = ProductionDryerDetails::with('product')->where('dryer_section_id',$id)->get();
            $data = [
                'id'    => $editModeData->id,
                'dryer_no' => $editModeData->dryer_no,
                'dryer_date' => dateConvertDBtoForm($editModeData->dryer_date),
                'narration' => $editModeData->narration,

                'total_receive_qty' => $editModeData->total_receive_qty,
                'total_remain_qty' => $editModeData->total_remain_qty,
                'total_waste_qty' => $editModeData->total_waste_qty,
                'total_waste_weight' => $editModeData->total_waste_weight,

                'total_trans_to_glaze_qty' => $editModeData->total_trans_to_glaze_qty,
                'total_trans_to_glaze_weight' => $editModeData->total_trans_to_glaze_weight,
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
            $editModeData = ProductionDryer::FindOrFail($id);
            $editModeDetailsData = ProductionDryerDetails::where('dryer_section_id',$id)->get();
            $data = [
                'id'    => $editModeData->id,
                'dryer_no' => $editModeData->dryer_no,
                'dryer_date' => dateConvertDBtoForm($editModeData->dryer_date),
                'narration' => $editModeData->narration,

                'total_receive_qty' => $editModeData->total_receive_qty,
                'total_remain_qty' => $editModeData->total_remain_qty,
                'total_waste_qty' => $editModeData->total_waste_qty,
                'total_waste_weight' => $editModeData->total_waste_weight,

                'total_trans_to_glaze_qty' => $editModeData->total_trans_to_glaze_qty,
                'total_trans_to_glaze_weight' => $editModeData->total_trans_to_glaze_weight,
                'total_amount' => $editModeData->total_amount,

                'last_month_overhead_amt'       => '',
                'last_month_production_kg'      => '',
                'overhead_per_kg'                => '',

                'approve_status' => '',
                'deleteID' => [],
                'details'    => $editModeDetailsData
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
                    $this->makeCorrectionDrying($request,$id);
                    return response()->json(['status' => 'success', 'message' => 'Drying Section Correction successfully updated!']);
                //}
            }
        }

        $this->validate($request, [
            'dryer_no' => 'required',
            'dryer_date' => 'required',
            'total_trans_to_glaze_qty' => 'required',
            'total_trans_to_glaze_weight' => 'required',

            'details.*.product_id' => 'required',
            'details.*.dryer_product_weight' => 'required',
            'details.*.current_qty' => 'required',
            'details.*.receive_qty' => 'required',
            'details.*.trans_to_glaze_qty' => 'required',
            'details.*.trans_to_glaze_weight' => 'required',
            'details.*.unit_price' => 'required',
            'details.*.total_price' => 'required',
        ], [

            'details.*.product_id.required' => 'Required field.',
            'details.*.dryer_product_weight.required' => 'Required field.',
            'details.*.current_qty.required' => 'Required field.',
            'details.*.receive_qty.required' => 'Required field.',
            'details.*.trans_to_glaze_qty.required' => 'Required field.',
            'details.*.trans_to_glaze_weight.required' => 'Required field.',
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

            $editModeData = ProductionDryer::FindOrFail($id);

            $editModeData->dryer_no                    = $request->dryer_no;
            $editModeData->dryer_date                  = dateConvertFormtoDB($request->dryer_date);
            $editModeData->narration                   = $request->narration;
            $editModeData->total_trans_to_glaze_qty    = $request->total_trans_to_glaze_qty;
            $editModeData->total_trans_to_glaze_weight = $request->total_trans_to_glaze_weight;
            $editModeData->total_receive_qty           = $request->total_receive_qty;
            $editModeData->total_remain_qty            = $request->total_remain_qty;
            $editModeData->total_waste_qty             = $request->total_waste_qty;
            $editModeData->total_waste_weight          = $request->total_waste_weight;
            $editModeData->total_amount                = $request->total_amount;
            $editModeData->overhead_info               = json_encode($overhead_info);
            $editModeData->wastage_overhead_amt        = $request->weight_after_process_loss * $request->overhead_per_kg;

            if ($app!=1) {
                $editModeData->updated_by = Auth::user()->id;
            }

            $editModeData->save();


            /* Insert update and delete rm requisition details  */
            if (count($request->deleteID) > 0) {
                ProductionDryerDetails::whereIn('id', $request->deleteID)->delete();
            }
            $dataFormat = [];
            $count = count($request->details);
            for ($i = 0; $i < $count; $i++) {
                if (isset($request->details[$i]['id']) && $request->details[$i]['id'] !='') {
                    $updateData = [
                        'dryer_section_id'     => $id,
                        'product_id'           => $request->details[$i]['product_id'],
                        'remarks'              => $request->details[$i]['remarks'],
                        'dryer_product_weight' => $request->details[$i]['dryer_product_weight'],
                        'current_qty'          => $request->details[$i]['current_qty'],
                        'receive_qty'          => $request->details[$i]['receive_qty'],
                        'remain_qty'           => $request->details[$i]['remain_qty'],
                        'trans_to_glaze_qty'   => $request->details[$i]['trans_to_glaze_qty'],
                        'trans_to_glaze_weight'=> $request->details[$i]['trans_to_glaze_weight'],
                        'glaze_westage_qty'    => $request->details[$i]['glaze_westage_qty'],
                        'glaze_westage_weight' => $request->details[$i]['glaze_westage_weight'],
                        'unit_price'           => $request->details[$i]['unit_price'],
                        'overhead_price'       => $request->details[$i]['overhead_price'],
                        'total_price'          => $request->details[$i]['total_price'],
                    ];
                    ProductionDryerDetails::where('id', $request->details[$i]['id'])->update($updateData);
                } else {
                    $dataFormat[$i] =[
                        'dryer_section_id'     => $id,
                        'product_id'           => $request->details[$i]['product_id'],
                        'remarks'              => $request->details[$i]['remarks'],
                        'dryer_product_weight' => $request->details[$i]['dryer_product_weight'],
                        'current_qty'          => $request->details[$i]['current_qty'],
                        'receive_qty'          => $request->details[$i]['receive_qty'],
                        'remain_qty'           => $request->details[$i]['remain_qty'],
                        'trans_to_glaze_qty'   => $request->details[$i]['trans_to_glaze_qty'],
                        'trans_to_glaze_weight'=> $request->details[$i]['trans_to_glaze_weight'],
                        'glaze_westage_qty'    => $request->details[$i]['glaze_westage_qty'],
                        'glaze_westage_weight' => $request->details[$i]['glaze_westage_weight'],
                        'unit_price'           => $request->details[$i]['unit_price'],
                        'overhead_price'       => $request->details[$i]['overhead_price'],
                        'total_price'          => $request->details[$i]['total_price'],
                    ];
                }
            }

            if(count($dataFormat) > 0){
                ProductionDryerDetails::insert($dataFormat);
            }

            if($app == 1){
                $this->approve($id);
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Dryer successfully updated!']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }


    public function destroy($id)
    {
        try{

            ProductionDryerDetails::where('dryer_section_id',$id)->delete();
            ProductionDryer::FindOrFail($id)->delete();

            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->errorInfo[1];
        }


        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Dryer successfully Deleted !']);
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
                'dryer_section_id'     => $id,
                'product_id'           => $request->details[$i]['product_id'],
                'remarks'              => $request->details[$i]['remarks'],
                'dryer_product_weight' => $request->details[$i]['dryer_product_weight'],
                'current_qty'          => $request->details[$i]['current_qty'],
                'receive_qty'          => $request->details[$i]['receive_qty'],
                'remain_qty'           => $request->details[$i]['remain_qty'],
                'trans_to_glaze_qty'   => $request->details[$i]['trans_to_glaze_qty'],
                'trans_to_glaze_weight'=> $request->details[$i]['trans_to_glaze_weight'],
                'glaze_westage_qty'    => $request->details[$i]['glaze_westage_qty'],
                'glaze_westage_weight' => $request->details[$i]['glaze_westage_weight'],
                'unit_price'           => $request->details[$i]['unit_price'],
                'overhead_price'       => $request->details[$i]['overhead_price'],
                'total_price'          => $request->details[$i]['total_price'],
            ];
        }

        return $dataFormat;
    }


    public function getPdfData($id)
    {
        $editModeData = ProductionDryer::FindOrFail($id);
        $editModeDetailsData = ProductionDryerDetails::with('product')->where('dryer_section_id',$id)->get();
        $data = [
            'id'                         => $editModeData->id,
            'dryer_no'                   => $editModeData->dryer_no,
            'dryer_date'                 => dateConvertDBtoForm($editModeData->dryer_date),
            'narration'                  => $editModeData->narration,
            'total_receive_qty'          => $editModeData->total_receive_qty,
            'total_remain_qty'           => $editModeData->total_remain_qty,
            'total_waste_qty'            => $editModeData->total_waste_qty,
            'total_waste_weight'         => $editModeData->total_waste_weight,
            'total_trans_to_glaze_qty'   => $editModeData->total_trans_to_glaze_qty,
            'total_trans_to_glaze_weight'=> $editModeData->total_trans_to_glaze_weight,
            'details'                    => $editModeDetailsData
        ];

        return $data;

    }

    public function exportAsPdf($data)
    {

        $company = CompanyInfo::Find(1);

        $data = array(
            'company'=> $company,
            'dryer'  => $data,

        );

        $html = \View::make('production.production_section.product_pdf.dryer_sectionPDF')->with($data);

        $mpdf = new \Mpdf\Mpdf([
            'margin_left'   => 20,
            'margin_right'  => 15,
            'margin_top'    => 48,
            'margin_bottom' => 25,
            'margin_header' => 10,
            'margin_footer' => 10
        ]);

        $title = 'Dryer Section';
        $author = 'iVenture';
        if ($company) {
            $title .= ' - '.$company->name;
            $author = $company->name;
        }

        $mpdf->SetProtection(array('print'));
        $mpdf->SetTitle($title);
        $mpdf->SetAuthor($author);
        $mpdf->SetWatermarkText("Dryer Section");
        $mpdf->showWatermarkText = true;
        $mpdf->watermark_font = 'DejaVuSansCondensed';
        $mpdf->watermarkTextAlpha = 0.07;
        $mpdf->SetDisplayMode('fullpage');
        // $stylesheet = file_get_contents(public_path().'/css/mpdf.css');
        // $mpdf->WriteHTML($stylesheet,1);
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }

    private function makeCorrectionDrying(Request $request, $id){
        try{
            DB::beginTransaction();
            $editModeData = ProductionDryer::FindOrFail($id);
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
                    ProductionDryerDetails::where('id', $request->details[$i]['id'])->update($updateData);
                }
            }
            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Drying Section Correction successfully Updated !']);
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
    public function getDryerLastPrice($product_id)
    {
        $data = [];
        $data['unit_price'] = 0;

        $product = ProductionDryerDetails::where('product_id',$product_id)
                        ->where('approve_status',1)
                        ->leftJoin('production_dryers', 'production_dryer_details.dryer_section_id','=','production_dryers.id')
                        ->select('unit_price')
                        ->orderBy('dryer_date','DESC')
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
