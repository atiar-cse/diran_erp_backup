<?php

namespace App\Http\Controllers\Production;

use App\Model\CompanyInfo;
use App\Model\Production\ProductionCurrentStock;
use App\Model\Production\ProductionKilnDetails;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Model\Production\ProductionKiln;
use App\Model\Production\ProductionProducts;
use DB;

use App\Model\Inventory\StockTransectionLog;
use App\Model\Inventory\StockTransectionLogDetails;
use App\Lib\Enumerations\StockTransactionType;

class KilnSectionController extends Controller
{

    public function index(Request $request)
    {
        if ($request->filled('token_last_price') && $request->token_last_price == 'yes') {
            return $this->getKilnLastPrice($request->product_id);
        }

        if ($request->ajax()) {

            $query = DB::table('production_kilns')
                ->leftJoin('users as ura', 'production_kilns.created_by','=','ura.id')
                ->leftJoin('users as ured', 'production_kilns.updated_by','=','ured.id')
                ->leftJoin('users as urea', 'production_kilns.approve_by','=','urea.id')
                ->whereNull('production_kilns.deleted_at')
                ->select(['production_kilns.id AS id',
                    'production_kilns.kiln_no',
                    'production_kilns.kiln_date',
                    'production_kilns.narration',
                    'production_kilns.created_by',
                    'production_kilns.updated_by',
                    'production_kilns.approve_by',
                    'production_kilns.approve_status',
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

        $productList  = ProductionProducts::select('production_products.id','production_products.product_name','production_products.forming_weight','production_products.kiln_weight','production_current_stocks.kiln_current_qty','production_current_stocks.kiln_receive_qty')
            ->leftJoin('production_current_stocks', 'production_current_stocks.product_id', '=', 'production_products.id')
            ->where('production_products.status', '1')->where('production_products.is_raw_material_status', '0')->get();
        return view('production.production_section.kiln_section',compact('productList'));
    }


    public function kilnNoGenerate(){
        $id = ProductionKiln::withTrashed()->count();
        $currentId = $id+1;
        return 'KILN-'.date('Ym').$currentId;
    }

    public function approve($id)
    {
        $appData = ProductionKiln::where('id', $id)->first();
        if ($appData->approve_status == 0) {
            $appDetailsData = ProductionKilnDetails::where('kiln_section_id', $id)->get();

            $this->incrementDecrementKilnQTY($appDetailsData);

            $date = $appData ? $appData->kiln_date : date('Y-m-d');
            $log_type = StockTransactionType::$prodution_kiln;
            $sl = $appData->kiln_no;

            $ref_tbl = '';
            $ref_tbl_id = 0;
            $log_tbl = 'production_kilns';

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
            $details_tbl = 'production_kiln_details';

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
        $inv_no_like = 'KILN-';
        $rtn_val =InvStore($request->kiln_no, $inv_no_like,
            'production_kilns','kiln_no');

        $request->merge(['kiln_no' => $rtn_val]) ;


        $this->validate($request, [
            'kiln_no' => 'required',
            'kiln_date' => 'required',
            'total_trans_to_hv_qty' => 'required',
            'total_trans_weight' => 'required',

            'details.*.product_id' => 'required',
            'details.*.kiln_weight' => 'required',
            'details.*.current_qty' => 'required',
            'details.*.receive_qty' => 'required',
            'details.*.trans_to_hv_qty' => 'required',
            'details.*.trans_to_hv_weight' => 'required',
            'details.*.reject_qty' => 'required',
            'details.*.reject_weight' => 'required',
            'details.*.unit_price' => 'required',
        ], [

            'details.*.product_id.required' => 'Required field.',
            'details.*.kiln_weight.required' => 'Required field.',
            'details.*.current_qty.required' => 'Required field.',
            'details.*.receive_qty.required' => 'Required field.',
            'details.*.trans_to_hv_qty.required' => 'Required field.',
            'details.*.trans_to_hv_weight.required' => 'Required field.',
            'details.*.reject_qty.required' => 'Required field.',
            'details.*.reject_weight.required' => 'Required field.',
            'details.*.unit_price.required' => 'Required field.',
        ]);

         $overhead_info = [
            'last_month_overhead_amt'  => $request->last_month_overhead_amt,
            'last_month_production_kg' => $request->last_month_production_kg,
            'overhead_per_kg'          => $request->overhead_per_kg,
        ];

        $data = [
            'kiln_no'             => $request->kiln_no,
            'kiln_date'           => dateConvertFormtoDB($request->kiln_date),
            'narration'           => $request->narration,
            'total_trans_to_hv_qty' => $request->total_trans_to_hv_qty,
            'total_trans_weight'  => $request->total_trans_weight,
            'total_receive_qty'   => $request->total_receive_qty,
            'total_remain_qty'    => $request->total_remain_qty,
            'total_reject_qty'    => $request->total_reject_qty,
            'total_reject_weight' => $request->total_reject_weight,
            'total_amount'        => $request->total_amount,

            'overhead_info'       => json_encode($overhead_info),
            'reject_overhead_amt' => $request->total_reject_weight * $request->overhead_per_kg,
            'reject_amt'          => $this->getTotalRejectAmount($request->details),

            'created_by'          => Auth::user()->id,
        ];

        try {
            DB::beginTransaction();

            $app = $request->approve_status;
            $result = ProductionKiln::create($data);
            $details = $this->dataFormat($request, $result->id);

            ProductionKilnDetails::insert($details);
            if($app == 1){
                $this->approve($result->id);

                if($request->updated_by == Null){
                    DB::table('production_kilns')->where('id', $result->id)->update(array('updated_by' => Auth::user()->id));
                }
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => "Kiln # " . $request->kiln_no .' Successfully Saved!']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    private function incrementDecrementKilnQTY($productArray){
        foreach($productArray as $val) {

            $checkExists = ProductionCurrentStock::where('product_id',$val['product_id'])->first();

            if($checkExists){

                ProductionCurrentStock::where('product_id', $val['product_id'])
                    ->update(
                        [ 'kiln_current_qty' => $val['remain_qty']]
                    );
                ProductionCurrentStock::where('product_id', $val['product_id'])
                    ->decrement('kiln_receive_qty', $val['receive_qty']);
                ProductionCurrentStock::where('product_id', $val['product_id'])
                    ->increment('testing_receive_qty', $val['trans_to_hv_qty']);

            }else{
                $kilnQty= [
                    'product_id' => $val['product_id'],
                    'kiln_current_qty' => $val['remain_qty'],
                    'kiln_receive_qty' => $val['receive_qty'],
                    'testing_receive_qty' => $val['trans_to_hv_qty'],
                ];
                ProductionCurrentStock::insert($kilnQty);
            }
        }
    }

    public function show($id)
    {
        try {
            $editModeData = ProductionKiln::FindOrFail($id);
            $editModeDetailsData = ProductionKilnDetails::with('product')->where('kiln_section_id',$id)->get();
            $data = [
                'id'    => $editModeData->id,
                'kiln_no' => $editModeData->kiln_no,
                'kiln_date' => dateConvertDBtoForm($editModeData->kiln_date),
                'narration' => $editModeData->narration,
                'total_trans_to_hv_qty' => $editModeData->total_trans_to_hv_qty,
                'total_trans_weight' => $editModeData->total_trans_weight,
                'total_receive_qty' => $editModeData->total_receive_qty,
                'total_remain_qty' => $editModeData->total_remain_qty,
                'total_reject_qty' => $editModeData->total_reject_qty,
                'total_reject_weight' => $editModeData->total_reject_weight,
                'total_amount'        => $editModeData->total_amount,
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
            $editModeData = ProductionKiln::FindOrFail($id);
            $editModeDetailsData = ProductionKilnDetails::where('kiln_section_id',$id)->get();
            $data = [
                'id'                    => $editModeData->id,
                'kiln_no'               => $editModeData->kiln_no,
                'kiln_date'             => dateConvertDBtoForm($editModeData->kiln_date),
                'narration'             => $editModeData->narration,
                'total_trans_to_hv_qty' => $editModeData->total_trans_to_hv_qty,
                'total_trans_weight'    => $editModeData->total_trans_weight,
                'total_receive_qty'     => $editModeData->total_receive_qty,
                'total_remain_qty'      => $editModeData->total_remain_qty,
                'total_reject_qty'      => $editModeData->total_reject_qty,
                'total_reject_weight'   => $editModeData->total_reject_weight,
                'total_amount'          => $editModeData->total_amount,

                'last_month_overhead_amt'       => '',
                'last_month_production_kg'      => '',
                'overhead_per_kg'                => '',

                'approve_status'        => '',
                'deleteID'              => [],
                'details'               => $editModeDetailsData
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
                    $this->makeCorrectionKiln($request,$id);
                    return response()->json(['status' => 'success', 'message' => 'Kiln Correction successfully updated!']);
                //}
            }
        }

        $this->validate($request, [
            'kiln_no' => 'required',
            'kiln_date' => 'required',
            'total_trans_to_hv_qty' => 'required',
            'total_trans_weight' => 'required',

            'details.*.product_id' => 'required',
            'details.*.kiln_weight' => 'required',
            'details.*.current_qty' => 'required',
            'details.*.receive_qty' => 'required',
            'details.*.trans_to_hv_qty' => 'required',
            'details.*.trans_to_hv_weight' => 'required',
            'details.*.reject_qty' => 'required',
            'details.*.reject_weight' => 'required',
            'details.*.unit_price' => 'required',
        ], [

            'details.*.fitting_product_id.required' => 'Required field.',
            'details.*.kiln_weight.required' => 'Required field.',
            'details.*.current_qty.required' => 'Required field.',
            'details.*.receive_qty.required' => 'Required field.',
            'details.*.trans_to_hv_qty.required' => 'Required field.',
            'details.*.trans_to_hv_weight.required' => 'Required field.',
            'details.*.reject_qty.required' => 'Required field.',
            'details.*.reject_weight.required' => 'Required field.',
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

            $editModeData = ProductionKiln::FindOrFail($id);

            $editModeData->kiln_no               = $request->kiln_no;
            $editModeData->kiln_date             = dateConvertFormtoDB($request->kiln_date);
            $editModeData->narration             = $request->narration;
            $editModeData->total_trans_to_hv_qty = $request->total_trans_to_hv_qty;
            $editModeData->total_trans_weight    = $request->total_trans_weight;
            $editModeData->total_receive_qty     = $request->total_receive_qty;
            $editModeData->total_remain_qty      = $request->total_remain_qty;
            $editModeData->total_reject_qty      = $request->total_reject_qty;
            $editModeData->total_reject_weight   = $request->total_reject_weight;
            $editModeData->total_amount          = $request->total_amount;

            $editModeData->overhead_info         = json_encode($overhead_info);
            $editModeData->reject_overhead_amt         = $request->total_reject_weight * $request->overhead_per_kg;
            $editModeData->reject_amt         = $this->getTotalRejectAmount($request->details);


            if ($app!=1) {
                $editModeData->updated_by = Auth::user()->id;
            }

            $editModeData->save();

            /* Insert update and delete rm requisition details  */

            if (count($request->deleteID) > 0) {
                ProductionKilnDetails::whereIn('id', $request->deleteID)->delete();
            }
            $dataFormat = [];
            $count = count($request->details);
            for ($i = 0; $i < $count; $i++) {
                if (isset($request->details[$i]['id']) && $request->details[$i]['id'] !='') {
                    $updateData = [
                        'kiln_section_id'    => $id,
                        'product_id'         => $request->details[$i]['product_id'],
                        'remarks'            => $request->details[$i]['remarks'],
                        'kiln_weight'        => $request->details[$i]['kiln_weight'],
                        'current_qty'        => $request->details[$i]['current_qty'],
                        'receive_qty'        => $request->details[$i]['receive_qty'],
                        'remain_qty'         => $request->details[$i]['remain_qty'],
                        'trans_to_hv_qty'    => $request->details[$i]['trans_to_hv_qty'],
                        'trans_to_hv_weight' => $request->details[$i]['trans_to_hv_weight'],
                        'reject_qty'         => $request->details[$i]['reject_qty'],
                        'reject_weight'      => $request->details[$i]['reject_weight'],
                        'unit_price'         => $request->details[$i]['unit_price'],
                        'overhead_price'     => $request->details[$i]['overhead_price'],
                        'total_price'        => $request->details[$i]['total_price'],
                    ];
                    ProductionKilnDetails::where('id', $request->details[$i]['id'])->update($updateData);
                } else {
                    $dataFormat[$i] =[
                        'kiln_section_id'    => $id,
                        'product_id'         => $request->details[$i]['product_id'],
                        'remarks'            => $request->details[$i]['remarks'],
                        'kiln_weight'        => $request->details[$i]['kiln_weight'],
                        'current_qty'        => $request->details[$i]['current_qty'],
                        'receive_qty'        => $request->details[$i]['receive_qty'],
                        'remain_qty'         => $request->details[$i]['remain_qty'],
                        'trans_to_hv_qty'    => $request->details[$i]['trans_to_hv_qty'],
                        'trans_to_hv_weight' => $request->details[$i]['trans_to_hv_weight'],
                        'reject_qty'         => $request->details[$i]['reject_qty'],
                        'reject_weight'      => $request->details[$i]['reject_weight'],
                        'unit_price'         => $request->details[$i]['unit_price'],
                        'overhead_price'     => $request->details[$i]['overhead_price'],
                        'total_price'        => $request->details[$i]['total_price'],
                    ];
                }
            }

            if(count($dataFormat) > 0){
                ProductionKilnDetails::insert($dataFormat);
            }

            if($app == 1){
                $this->approve($id);
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Kiln successfully updated!']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }


    public function destroy($id)
    {
        try{

            ProductionKilnDetails::where('kiln_section_id',$id)->delete();
            ProductionKiln ::FindOrFail($id)->delete();

            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->errorInfo[1];
        }


        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Kiln successfully Deleted !']);
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
                'kiln_section_id'    => $id,
                'product_id'         => $request->details[$i]['product_id'],
                'remarks'            => $request->details[$i]['remarks'],
                'kiln_weight'        => $request->details[$i]['kiln_weight'],
                'current_qty'        => $request->details[$i]['current_qty'],
                'receive_qty'        => $request->details[$i]['receive_qty'],
                'remain_qty'         => $request->details[$i]['remain_qty'],
                'trans_to_hv_qty'    => $request->details[$i]['trans_to_hv_qty'],
                'trans_to_hv_weight' => $request->details[$i]['trans_to_hv_weight'],
                'reject_qty'         => $request->details[$i]['reject_qty'],
                'reject_weight'      => $request->details[$i]['reject_weight'],
                'unit_price'         => $request->details[$i]['unit_price'],
                'overhead_price'     => $request->details[$i]['overhead_price'],
                'total_price'        => $request->details[$i]['total_price'],
            ];
        }

        return $dataFormat;
    }

    public function getTotalRejectAmount($details)
    {
        $reject_amt = 0;
        $count = count($details);
        for ($i = 0; $i < $count; $i++) {

            $reject_amt += $details[$i]['reject_qty'] * $details[$i]['unit_price'];
        }

        return $reject_amt;
    }

    public function getPdfData($id)
    {
        $editModeData = ProductionKiln::FindOrFail($id);
        $editModeDetailsData = ProductionKilnDetails::with('product')->where('kiln_section_id',$id)->get();
        $data = [
            'id'                    => $editModeData->id,
            'kiln_no'               => $editModeData->kiln_no,
            'kiln_date'             => dateConvertDBtoForm($editModeData->kiln_date),
            'narration'             => $editModeData->narration,
            'total_trans_to_hv_qty' => $editModeData->total_trans_to_hv_qty,
            'total_trans_weight'    => $editModeData->total_trans_weight,
            'total_receive_qty'     => $editModeData->total_receive_qty,
            'total_remain_qty'      => $editModeData->total_remain_qty,
            'total_reject_qty'      => $editModeData->total_reject_qty,
            'total_reject_weight'   => $editModeData->total_reject_weight,
            'details'               => $editModeDetailsData
        ];

        return $data;

    }

    public function exportAsPdf($data)
    {

        $company = CompanyInfo::Find(1);

        $data = array(
            'company'=> $company,
            'kiln'   => $data,

        );

        $html = \View::make('production.production_section.product_pdf.kiln_sectionPDF')->with($data);

        $mpdf = new \Mpdf\Mpdf([
            'margin_left'   => 20,
            'margin_right'  => 15,
            'margin_top'    => 48,
            'margin_bottom' => 25,
            'margin_header' => 10,
            'margin_footer' => 10
        ]);

        $title = 'Kiln Section';
        $author = 'iVenture';
        if ($company) {
            $title .= ' - '.$company->name;
            $author = $company->name;
        }

        $mpdf->SetProtection(array('print'));
        $mpdf->SetTitle($title);
        $mpdf->SetAuthor($author);
        $mpdf->SetWatermarkText("Kiln Section");
        $mpdf->showWatermarkText = true;
        $mpdf->watermark_font = 'DejaVuSansCondensed';
        $mpdf->watermarkTextAlpha = 0.07;
        $mpdf->SetDisplayMode('fullpage');
        // $stylesheet = file_get_contents(public_path().'/css/mpdf.css');
        // $mpdf->WriteHTML($stylesheet,1);
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }

    private function makeCorrectionKiln(Request $request, $id){
        try{
            DB::beginTransaction();
            $editModeData = ProductionKiln::FindOrFail($id);
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
                    ProductionKilnDetails::where('id', $request->details[$i]['id'])->update($updateData);
                }
            }
            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Kiln Correction successfully Updated !']);
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
    public function getKilnLastPrice($product_id)
    {
        $data = [];
        $data['unit_price'] = 0;

        $product = ProductionKilnDetails::where('product_id',$product_id)
                        ->where('approve_status',1)
                        ->leftJoin('production_kilns', 'production_kiln_details.kiln_section_id','=','production_kilns.id')
                        ->select('unit_price')
                        ->orderBy('kiln_date','DESC')
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
