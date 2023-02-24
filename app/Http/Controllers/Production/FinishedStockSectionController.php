<?php

namespace App\Http\Controllers\Production;

use App\Model\CompanyInfo;
use App\Model\Inventory\InventoryCurrentStock;
use App\Model\Production\ProductionCurrentStock;

use App\Model\Production\ProductionFinishedStock;
use App\Model\Production\ProductionFinishedStockDetails;
use App\Model\Purchase\PurchaseWareHouse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Model\Production\ProductionProducts;
use DB;

class FinishedStockSectionController extends Controller
{
    public function index(Request $request)
    {
        if ($request->filled('token_last_price') && $request->token_last_price == 'yes') {
            return $this->getInventoryOrSemiFinishedLastPrice($request->product_id);
        }

        /* Ajax finishedStockNoGenerate() */
        if ($request->filled('token_no_geneate') && $request->token_no_geneate == 'finished_stock_no_generate') {

            return  $this->finishedStockNoGenerate();
        }

        if ($request->ajax()) {
            $query = DB::table('production_finished_stocks')
                ->leftJoin('users as ura', 'production_finished_stocks.created_by','=','ura.id')
                ->leftJoin('users as ured', 'production_finished_stocks.updated_by','=','ured.id')
                ->leftJoin('users as urea', 'production_finished_stocks.approve_by','=','urea.id')
                ->whereNull('production_finished_stocks.deleted_at')
                ->select(['production_finished_stocks.id AS id',
                    'production_finished_stocks.finished_stock_no',
                    'production_finished_stocks.date',
                    'production_finished_stocks.narration',
                    'production_finished_stocks.created_by',
                    'production_finished_stocks.updated_by',
                    'production_finished_stocks.approve_by',
                    'production_finished_stocks.approve_status',
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

        $productList  = ProductionProducts::select('production_products.id','production_products.product_name','production_products.kiln_weight','production_current_stocks.finished_stk_current_qty','production_current_stocks.finished_stk_receive_qty')
                        ->leftJoin('production_current_stocks', 'production_current_stocks.product_id', '=', 'production_products.id')
                        ->where('production_products.status', '1')
                        ->get();
        $warehouse   = PurchaseWareHouse::where('status', '1')->get();

        return view('production.production_section.finished_stock_section',compact('productList','warehouse'));
    }

    public function finishedStockNoGenerate(){
        $id = ProductionFinishedStock::withTrashed()->count();
        $currentId = $id+1;
        return 'FStkN-'.date('Ym').$currentId;
    }

    public function approve($id)
    {
        $appData = ProductionFinishedStock::where('id', $id)->first();
        if ($appData->approve_status == 0) {
            $appDetailsData = ProductionFinishedStockDetails::where('finished_stock_section_id', $id)->get();

            $this->incrementDecrementFinishedStockQTY($appDetailsData);

            $appData->approve_status = 1;
            $appData->approve_by = Auth::user()->id;
            $appData->approve_at = Carbon::now();
            $appData->save();
        }
    }

    public function store(Request $request)
    {
        $inv_no_like = 'FStkN-';
        $rtn_val =InvStore($request->finished_stock_no, $inv_no_like,
            'production_finished_stocks','finished_stock_no');

        $request->merge(['finished_stock_no' => $rtn_val]) ;


        $this->validate($request, [
            'finished_stock_no' => 'required',
            'date' => 'required',
            'total_trans_to_store_qty' => 'required',
            'total_amount' => 'required',
            'total_reject_qty' => 'required',

            'details.*.product_id' => 'required',
            'details.*.current_qty' => 'required',
            'details.*.receive_qty' => 'required',
            'details.*.trans_to_store_qty' => 'required',
            'details.*.unit_price' => 'required',
            'details.*.total_price' => 'required',
        ], [
            'details.*.product_id.required' => 'Required field.',
            'details.*.current_qty.required' => 'Required field.',
            'details.*.receive_qty.required' => 'Required field.',
            'details.*.trans_to_store_qty.required' => 'Required field.',
            'details.*.unit_price.required' => 'Required field.',
            'details.*.total_price.required' => 'Required field.',
        ]);

        $overhead_info = [
            'last_month_overhead_amt'  => $request->last_month_overhead_amt,
            'last_month_production_kg' => $request->last_month_production_kg,
            'overhead_per_kg'          => $request->overhead_per_kg,
        ];

        $input = $request->except('details','approve_status');
        $input['date'] = dateConvertFormtoDB($request->date);
        $input['overhead_info'] = json_encode($overhead_info);
        $input['reject_overhead_amt'] = $this->getTotalRejectAmount($request->details, 'overhead', $request->overhead_per_kg);
        $input['reject_amt'] = $this->getTotalRejectAmount($request->details, 'overhead_no', $request->overhead_per_kg);
        $input['created_by'] = Auth::user()->id;

        try {
            DB::beginTransaction();

            $app = $request->approve_status;

            $result = ProductionFinishedStock::create($input);
            $details = $this->dataFormat($request, $result->id);

            ProductionFinishedStockDetails::insert($details);
            if($app == 1){
                $this->approve($result->id);

                if($request->updated_by == Null){
                    DB::table('production_finished_stocks')->where('id', $result->id)->update(array('updated_by' => Auth::user()->id));
                }
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => "Finished Stock # " . $request->finished_stock_no .' Successfully Saved!']);
        } catch (\Exception $e) {
            DB::rollback();
            $msg = $e->getMessage();

            return response()->json(['status' => 'error', 'message' => $msg]);
        }
    }

    private function incrementDecrementFinishedStockQTY($productArray){

        foreach($productArray as $val) {

            /* @ Production Section */
            $checkProductionExists = ProductionCurrentStock::where('product_id',$val['product_id'])->first();
                if($checkProductionExists){

                    ProductionCurrentStock::where('product_id', $val['product_id'])
                        ->update(
                            [ 'finished_stk_current_qty' => $val['remain_qty']]
                        );
                    ProductionCurrentStock::where('product_id', $val['product_id'])
                        ->decrement('finished_stk_receive_qty', $val['receive_qty']);
                    ProductionCurrentStock::where('product_id', $val['product_id'])
                        ->increment('assembling_receive_qty', $val['trans_to_store_qty']); //trans to Assembling

                }else{
                    $testingQty= [
                        'product_id' => $val['product_id'],
                        'finished_stk_current_qty' => $val['remain_qty'],
                        'finished_stk_receive_qty' => $val['receive_qty'],
                        'assembling_receive_qty' => $val['trans_to_store_qty'], //trans to Assembling
                    ];
                    ProductionCurrentStock::insert($testingQty);
                }

        }
    }

    public function show($id)
    {
        try {
            $editModeData = ProductionFinishedStock::FindOrFail($id);
            $editModeDetailsData = ProductionFinishedStockDetails::with('product')->where('finished_stock_section_id',$id)->get();
            $data = [
                'id'    => $editModeData->id,
                'finished_stock_no' => $editModeData->finished_stock_no,
                'date' => dateConvertDBtoForm($editModeData->date),
                'narration' => $editModeData->narration,
                'total_receive_qty' => $editModeData->total_receive_qty,
                'total_adj_qty' => $editModeData->total_adj_qty,
                'total_trans_to_store_qty' => $editModeData->total_trans_to_store_qty,
                'total_amount' => $editModeData->total_amount,
                'total_remain_qty' => $editModeData->total_remain_qty,
                'total_reject_qty' => $editModeData->total_reject_qty,
                'approve_status' => $editModeData->approve_status,
                'deleteID' => [],
                'details'    => $editModeDetailsData
            ];
            return response()->json(['status'=>'success','data'=>$data]);
        } catch(\Exception $e){
            $msg = $e->getMessage();
            return response()->json(['status'=>'error','data'=>$msg]);
        }
    }

    public function edit($id)
    {
        try {
            $editModeData = ProductionFinishedStock::FindOrFail($id);
            $editModeDetailsData = ProductionFinishedStockDetails::where('finished_stock_section_id',$id)->get();
            $data = [
                'id'    => $editModeData->id,
                'finished_stock_no' => $editModeData->finished_stock_no,
                'date' => dateConvertDBtoForm($editModeData->date),
                'narration' => $editModeData->narration,
                'total_receive_qty' => $editModeData->total_receive_qty,
                'total_adj_qty' => $editModeData->total_adj_qty,
                'total_trans_to_store_qty' => $editModeData->total_trans_to_store_qty,
                'total_amount' => $editModeData->total_amount,
                'total_remain_qty' => $editModeData->total_remain_qty,
                'total_reject_qty' => $editModeData->total_reject_qty,
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
        $this->validate($request, [
            'finished_stock_no' => 'required',
            'date' => 'required',
            'total_trans_to_store_qty' => 'required',
            'total_amount' => 'required',
            'total_reject_qty' => 'required',


            'details.*.product_id' => 'required',
            'details.*.current_qty' => 'required',
            'details.*.receive_qty' => 'required',
            'details.*.trans_to_store_qty' => 'required',
            'details.*.unit_price' => 'required',
            'details.*.total_price' => 'required',
            'details.*.reject_qty' => 'required',
        ], [

            'details.*.product_id.required' => 'Required field.',
            'details.*.current_qty.required' => 'Required field.',
            'details.*.receive_qty.required' => 'Required field.',
            'details.*.trans_to_store_qty.required' => 'Required field.',
            'details.*.unit_price.required' => 'Required field.',
            'details.*.total_price.required' => 'Required field.',
            'details.*.reject_qty.required' => 'Required field.',
        ]);

        $overhead_info = [
            'last_month_overhead_amt'  => $request->last_month_overhead_amt,
            'last_month_production_kg' => $request->last_month_production_kg,
            'overhead_per_kg'          => $request->overhead_per_kg,
        ];

        try {
            DB::beginTransaction();

            $app = $request->approve_status;

            $input = $request->except('details','approve_status');
            $input['date'] = dateConvertFormtoDB($request->date);
            $input['overhead_info'] = json_encode($overhead_info);
            $input['reject_overhead_amt'] = $this->getTotalRejectAmount($request->details, 'overhead', $request->overhead_per_kg);
            $input['reject_amt'] = $this->getTotalRejectAmount($request->details, 'overhead_no', $request->overhead_per_kg);

            if ($app!=1) {
                $input['updated_by'] = Auth::user()->id;
            }


            $editModeData = ProductionFinishedStock::FindOrFail($id);
            $editModeData->update($input);

            /* Insert update and delete rm requisition details  */

            if (count($request->deleteID) > 0) {
                ProductionFinishedStockDetails::whereIn('id', $request->deleteID)->delete();
            }
            $dataFormat = [];
            $count = count($request->details);
            for ($i = 0; $i < $count; $i++) {
                if (isset($request->details[$i]['id']) && $request->details[$i]['id'] !='') {
                    $updateData = [
                        'finished_stock_section_id' => $id,
                        'product_id'         => $request->details[$i]['product_id'],
                        'remarks'            => $request->details[$i]['remarks'],
                        'current_qty'        => $request->details[$i]['current_qty'],
                        'receive_qty'        => $request->details[$i]['receive_qty'],
                        'adj_qty'            => $request->details[$i]['adj_qty'],
                        'trans_to_store_qty' => $request->details[$i]['trans_to_store_qty'],
                        'unit_price'         => $request->details[$i]['unit_price'],
                        'overhead_price'     => $request->details[$i]['overhead_price'],
                        'total_price'        => $request->details[$i]['total_price'],
                        'remain_qty'         => $request->details[$i]['remain_qty'],
                        'reject_qty'         => $request->details[$i]['reject_qty'],
                        'kiln_weight'        => $request->details[$i]['kiln_weight'],
                    ];
                    ProductionFinishedStockDetails::where('id', $request->details[$i]['id'])->update($updateData);
                } else {
                    $dataFormat[$i] =[
                        'finished_stock_section_id' => $id,
                        'product_id'         => $request->details[$i]['product_id'],
                        'remarks'            => $request->details[$i]['remarks'],
                        'current_qty'        => $request->details[$i]['current_qty'],
                        'receive_qty'        => $request->details[$i]['receive_qty'],
                        'adj_qty'            => $request->details[$i]['adj_qty'],
                        'trans_to_store_qty' => $request->details[$i]['trans_to_store_qty'],
                        'unit_price'         => $request->details[$i]['unit_price'],
                        'overhead_price'     => $request->details[$i]['overhead_price'],
                        'total_price'        => $request->details[$i]['total_price'],
                        'remain_qty'         => $request->details[$i]['remain_qty'],
                        'reject_qty'         => $request->details[$i]['reject_qty'],
                        'kiln_weight'        => $request->details[$i]['kiln_weight'],
                    ];
                }
            }

            if(count($dataFormat) > 0){
                ProductionFinishedStockDetails::insert($dataFormat);
            }

            if($app == 1){
                $this->approve($id);
            }
            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Finished Stock successfully updated!']);
        } catch (\Exception $e) {
            DB::rollback();
            print_r($e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }


    public function destroy($id)
    {
        try{

            ProductionFinishedStockDetails::where('finished_stock_section_id',$id)->delete();
            ProductionFinishedStock::FindOrFail($id)->delete();

            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->errorInfo[1];
        }


        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Testing successfully Deleted !']);
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
                'finished_stock_section_id' => $id,
                'product_id'         => $request->details[$i]['product_id'],
                'remarks'            => $request->details[$i]['remarks'],
                'current_qty'        => $request->details[$i]['current_qty'],
                'receive_qty'        => $request->details[$i]['receive_qty'],
                'adj_qty'            => $request->details[$i]['adj_qty'],
                'trans_to_store_qty' => $request->details[$i]['trans_to_store_qty'],
                'unit_price'         => $request->details[$i]['unit_price'],
                'overhead_price'     => $request->details[$i]['overhead_price'],
                'total_price'        => $request->details[$i]['total_price'],
                'remain_qty'         => $request->details[$i]['remain_qty'],
                'reject_qty'         => $request->details[$i]['reject_qty'],
                'kiln_weight'         => $request->details[$i]['kiln_weight'],
            ];
        }

        return $dataFormat;
    }

    public function getTotalRejectAmount($details, $is_overhead, $overhead_per_kg)
    {
        $reject_overhead_amt = 0;
        $reject_amt = 0;

        $count = count($details);
        for ($i = 0; $i < $count; $i++) {

            $reject_overhead_amt += $details[$i]['reject_qty'] * $details[$i]['kiln_weight'] * $overhead_per_kg;
            $reject_amt += $details[$i]['reject_qty'] * $details[$i]['unit_price'];
        }

        if ($is_overhead == 'overhead') {
            return $reject_overhead_amt;
        }
        return $reject_amt;
    }

    public function getPdfData($id)
    {
        $editModeData = ProductionFinishedStock::FindOrFail($id);
        $editModeDetailsData = ProductionFinishedStockDetails::with('product')->where('finished_stock_section_id',$id)->get();
        $data = [
            'id'                      => $editModeData->id,
            'finished_stock_no'       => $editModeData->finished_stock_no,
            'date'                    => dateConvertDBtoForm($editModeData->date),
            'narration'               => $editModeData->narration,
            'total_receive_qty'       => $editModeData->total_receive_qty,
            'total_adj_qty'           => $editModeData->total_adj_qty,
            'total_trans_to_store_qty'=> $editModeData->total_trans_to_store_qty,
            'total_amount'            => $editModeData->total_amount,
            'total_remain_qty'        => $editModeData->total_remain_qty,
            'total_reject_qty'        => $editModeData->total_reject_qty,
            'approve_status'          => $editModeData->approve_status,
            'details'                 => $editModeDetailsData
        ];

        return $data;

    }

    public function exportAsPdf($data)
    {

        $company = CompanyInfo::Find(1);

        $data = array(
            'company'=> $company,
            'finish'  => $data,

        );

        $html = \View::make('production.production_section.product_pdf.finish_sectionPDF')->with($data);

        $mpdf = new \Mpdf\Mpdf([
            'margin_left'   => 20,
            'margin_right'  => 15,
            'margin_top'    => 48,
            'margin_bottom' => 25,
            'margin_header' => 10,
            'margin_footer' => 10
        ]);

        $title = 'Finish Stock Section';
        $author = 'iVenture';
        if ($company) {
            $title .= ' - '.$company->name;
            $author = $company->name;
        }

        $mpdf->SetProtection(array('print'));
        $mpdf->SetTitle($title);
        $mpdf->SetAuthor($author);
        $mpdf->SetWatermarkText("Finish Stock Section");
        $mpdf->showWatermarkText = true;
        $mpdf->watermark_font = 'DejaVuSansCondensed';
        $mpdf->watermarkTextAlpha = 0.07;
        $mpdf->SetDisplayMode('fullpage');
        // $stylesheet = file_get_contents(public_path().'/css/mpdf.css');
        // $mpdf->WriteHTML($stylesheet,1);
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }

    /**
     * @ Get Ajax Last Production Info - Unit Price with Qty Check
     *
     * @param $product_id
     * @return Product row with last price
     */
    public function getInventoryOrSemiFinishedLastPrice($product_id) //Incomplete
    {
        $data = [];
        $data['unit_price'] = 0;

        $product = ProductionProducts::where('id',$product_id)->first();
                        /*ProductionFinishedStockDetails::select('unit_price')
                        ->where('product_id',$product_id)
                        ->where('approve_status',1)
                        ->leftJoin('production_finished_stocks', 'production_finished_stock_details.finished_stock_section_id','=','production_finished_stocks.id')
                        ->orderBy('date','DESC')
                        ->First();*/

        if ($product) {
            $data['unit_price'] = $product->buying_price;
        }

        return $data;
    }
}
