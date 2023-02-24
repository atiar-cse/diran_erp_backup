<?php

namespace App\Http\Controllers\LcImport;

use App\Model\LC\LcProformaInvoiceEntry;
use App\Model\LC\LcProformaInvoiceDetails;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Model\LC\LcWorkOrderImport;
use App\Model\Currency;
use App\Model\Country;
use App\Model\Production\ProductionProducts;
use DB;

class ProformaInvoiceEntryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $query = DB::table('lc_proforma_invoice_entries')
                ->leftJoin('purchase_supplier_entries', 'lc_proforma_invoice_entries.supplier_id', '=', 'purchase_supplier_entries.id')
                ->leftJoin('lc_work_order_imports', 'lc_proforma_invoice_entries.work_order_id', '=', 'lc_work_order_imports.id')
                ->leftJoin('users as ura', 'lc_proforma_invoice_entries.created_by','=','ura.id')
                ->leftJoin('users as ured', 'lc_proforma_invoice_entries.updated_by','=','ured.id')
                ->leftJoin('users as urea', 'lc_work_order_imports.approve_by','=','urea.id')
                ->whereNull('lc_proforma_invoice_entries.deleted_at')
                ->select(['lc_proforma_invoice_entries.id AS id',
                    'lc_proforma_invoice_entries.pi_no',
                    'lc_proforma_invoice_entries.pi_date',
                    'lc_proforma_invoice_entries.total_qty',
                    'lc_proforma_invoice_entries.narration',
                    'lc_proforma_invoice_entries.total_amount_taka',
                    'lc_proforma_invoice_entries.created_by',
                    'lc_proforma_invoice_entries.updated_by',
                    'lc_proforma_invoice_entries.approve_by',
                    'lc_proforma_invoice_entries.approve_status',
                    'purchase_supplier_entries.purchase_supplier_name',
                    'lc_work_order_imports.work_order_no',
                    'ura.user_name as ad_name',
                    'ured.user_name as ed_name',
                    'urea.user_name as ap_name'
                ]);
            return datatables()->of($query)->toJson();
        }

        $workOrderLists = LcWorkOrderImport::with('get_supplier')->where('status',1)
                            ->where('approve_status',1)->where('closing_status',0)
                            ->whereNotIn('id',
                                DB::table('lc_proforma_invoice_entries')->where('approve_status', '1')
                                    ->pluck('work_order_id')
                            )->selectRaw('id,work_order_no,supplier_id')->get();
        $productLists = ProductionProducts::where('status',1)->selectRaw('id,product_name,product_code')->get();
        $currecyLists = Currency::where('status',1)->selectRaw('id,name,symbol')->get();        
        $countryLists = Country::selectRaw('id,country_name')->get();      
        return view('lc.lc_section.proforma_invoice_import',compact(
            'workOrderLists','productLists','currecyLists','countryLists'));
    }

    public function piNoGenerate(){
        $id = LcProformaInvoiceEntry::withTrashed()->count();
        $currentId = $id+1;
        return 'PI-'.date('Ym').$currentId;
    }
    

    public function store(Request $request)
    {
        $this->validate($request, [
            'pi_no' => 'required|unique:lc_proforma_invoice_entries,pi_no',
            'work_order_id' => 'required',
            //'usd_account_no' => 'required|alpha_dash',
            'terms_of_loading' => 'required',
            'port_of_discharger' => 'required',
            'details.*.product_id' => 'required',
            'details.*.currency_id' => 'required',
            'details.*.unit_price' => 'required|numeric',
            'details.*.qty' => 'required|numeric',
            'details.*.bdt_convert_rate' => 'required|numeric',
        ], [

            'pi_no.required' => 'The PI No. field is required.',
            //'pi_no.alpha_dash' => 'The PI No. field may only contain letters, numbers, dashes and underscores.',
            'pi_no.unique' => 'The PI No. has already been taken.',
            'work_order_id.required' => 'The Work Order No field is required.',
            'usd_account_no.required' => 'The USD A/C No field is required.',
            'usd_account_no.alpha_dash' => 'The USD A/C No field may only contain letters, numbers, dashes and underscores.',
            'details.*.product_id.required' => 'Required field.',
            'details.*.currency_id.required' => 'Required field.',
            'details.*.unit_price.required' => 'Required field.',
            'details.*.qty.required' => 'Required field.',
            'details.*.bdt_convert_rate.required' => 'Required field.',
        ]);

        $app = $request->approve;
        $data = [
            'pi_no' => $request->pi_no,
            'work_order_id' => $request->work_order_id,
            'supplier_id' => $request->supplier_id,
            'usd_account_no' => $request->usd_account_no,
            'pi_date' => dateConvertFormtoDB($request->pi_date),
            'consignee' => $request->consignee,
            'swift_code' => $request->swift_code,
            'terms_of_loading' => $request->terms_of_loading,
            'port_of_discharger' => $request->port_of_discharger,
            'delivery_time' => dateConvertFormtoDB($request->delivery_time),
            'terms_of_delivery' => $request->terms_of_delivery,
            'terms_of_payment' => $request->terms_of_payment,
            'origin_of_goods' => $request->origin_of_goods,
            'final_destination' => $request->final_destination,
            'narration' => $request->narration,
            'total_qty' => $request->total_qty,
            'total_amount' => $request->total_amount,
            'total_amount_taka' => $request->total_amount_taka,
            'created_by' => Auth::user()->id,
        ];

        try {
            DB::beginTransaction();

            $result = LcProformaInvoiceEntry::create($data);
            $details = $this->dataFormat($request, $result->id);
            LcProformaInvoiceDetails::insert($details);

            if($app == '1'){
                $this->approve($result->id);

                if($request->updated_by == Null){
                    DB::table('lc_proforma_invoice_entries')->where('id', $result->id)->update(array('updated_by' => Auth::user()->id));
                }
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Proforma Invoice Successfully saved!']);
        } catch (\Exception $e) {
            print_r($e->getMessage());
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }


    public function show($id)
    {
        try {
            $editModeData = LcProformaInvoiceEntry::with('get_supplier','get_work_order')->FindOrFail($id);
            $editModeDetailsData = LcProformaInvoiceDetails::with('get_product','get_currency')->where('lc_proforma_invoice_id',$id)->get();

            $data = [
                'id'    => $editModeData->id,
                'pi_no' => $editModeData->pi_no,
                'get_work_order' => $editModeData->get_work_order,
                'supplier_id' => $editModeData->supplier_id,
                'supplier_info_display'=>$editModeData->get_supplier? $editModeData->get_supplier->purchase_supplier_name: '',
                'usd_account_no' => $editModeData->usd_account_no,
                'pi_date' => dateConvertDBtoForm($editModeData->pi_date),
                'consignee' => $editModeData->consignee,
                'swift_code' => $editModeData->swift_code,
                'terms_of_loading' => $editModeData->terms_of_loading,
                'port_of_discharger' => $editModeData->port_of_discharger,
                'delivery_time' => dateConvertDBtoForm($editModeData->delivery_time),
                'terms_of_delivery' => $editModeData->terms_of_delivery,
                'terms_of_payment' => $editModeData->terms_of_payment,
                'get_country' => $editModeData->get_country,
                'final_destination' => $editModeData->final_destination,
                'narration' => $editModeData->narration,
                'total_qty' => $editModeData->total_qty,
                'total_amount' => $editModeData->total_amount,
                'total_amount_taka' => $editModeData->total_amount_taka,
                'approve_status'  => $editModeData->approve_status,
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
            $editModeData = LcProformaInvoiceEntry::with('get_supplier','get_work_order')->FindOrFail($id);
            $editModeDetailsData = LcProformaInvoiceDetails::where('lc_proforma_invoice_id',$id)->get();

            $data = [
                'id'    => $editModeData->id,
                'pi_no' => $editModeData->pi_no,
                'work_order_id' => $editModeData->work_order_id,
                'supplier_id' => $editModeData->supplier_id,
                'supplier_info_display'=>$editModeData->get_supplier? $editModeData->get_supplier->purchase_supplier_name: '',
                'usd_account_no' => $editModeData->usd_account_no,
                'pi_date' => dateConvertDBtoForm($editModeData->pi_date),
                'consignee' => $editModeData->consignee,
                'swift_code' => $editModeData->swift_code,
                'terms_of_loading' => $editModeData->terms_of_loading,
                'port_of_discharger' => $editModeData->port_of_discharger,
                'delivery_time' => dateConvertDBtoForm($editModeData->delivery_time),
                'terms_of_delivery' => $editModeData->terms_of_delivery,
                'terms_of_payment' => $editModeData->terms_of_payment,
                'origin_of_goods' => $editModeData->origin_of_goods,
                'final_destination' => $editModeData->final_destination,
                'narration' => $editModeData->narration,
                'total_qty' => $editModeData->total_qty,
                'total_amount' => $editModeData->total_amount,
                'total_amount_taka' => $editModeData->total_amount_taka,
                'approve'  => $editModeData->approve_status,
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
            'pi_no' => 'required|unique:lc_proforma_invoice_entries,pi_no,'.$id,
            'work_order_id' => 'required',
            //'usd_account_no' => 'required|alpha_dash',
            'terms_of_loading' => 'required',
            'port_of_discharger' => 'required',
            'details.*.product_id' => 'required',
            'details.*.currency_id' => 'required',
            'details.*.unit_price' => 'required|numeric',
            'details.*.qty' => 'required|numeric',
            'details.*.bdt_convert_rate' => 'required|numeric',
        ], [

            'pi_no.required' => 'The PI No. field is required.',
            //'pi_no.alpha_dash' => 'The PI No. field may only contain letters, numbers, dashes and underscores.',
            'pi_no.unique' => 'The PI No. has already been taken.',
            'work_order_id.required' => 'The Work Order No field is required.',
            'usd_account_no.required' => 'The USD A/C No field is required.',
            'usd_account_no.alpha_dash' => 'The USD A/C No field may only contain letters, numbers, dashes and underscores.',
            'details.*.product_id.required' => 'Required field.',
            'details.*.currency_id.required' => 'Required field.',
            'details.*.unit_price.required' => 'Required field.',
            'details.*.qty.required' => 'Required field.',
            'details.*.bdt_convert_rate.required' => 'Required field.',
        ]);


        try {
            DB::beginTransaction();

            $app = $request->approve;

            $editModeData = LcProformaInvoiceEntry::FindOrFail($id);

            $editModeData->pi_no              = $request->pi_no;
            $editModeData->work_order_id      = $request->work_order_id;
            $editModeData->supplier_id        = $request->supplier_id;
            $editModeData->usd_account_no     = $request->usd_account_no;
            $editModeData->pi_date            = dateConvertFormtoDB($request->pi_date);
            $editModeData->consignee          = $request->consignee;
            $editModeData->swift_code         = $request->swift_code;
            $editModeData->terms_of_loading   = $request->terms_of_loading;
            $editModeData->port_of_discharger = $request->port_of_discharger;
            $editModeData->delivery_time      = dateConvertFormtoDB($request->delivery_time);
            $editModeData->terms_of_delivery  = $request->terms_of_delivery;
            $editModeData->terms_of_payment   = $request->terms_of_payment;
            $editModeData->origin_of_goods    = $request->origin_of_goods;
            $editModeData->final_destination  = $request->final_destination;
            $editModeData->narration          = $request->narration;
            $editModeData->total_qty          = $request->total_qty;
            $editModeData->total_amount       = $request->total_amount;
            $editModeData->total_amount_taka  = $request->total_amount_taka;

            if($app !=1){
                $editModeData->updated_by = Auth::user()->id;
            }

            $editModeData->save();

            /* Insert update and delete proforma-invoice details  */

            if (count($request->deleteID) > 0) {
                LcProformaInvoiceDetails::whereIn('id', $request->deleteID)->delete();
            }
            $dataFormat = [];
            $count = count($request->details);
            for ($i = 0; $i < $count; $i++) {
                if (isset($request->details[$i]['id']) && $request->details[$i]['id'] !='') {
                    $updateData = [
                        'lc_proforma_invoice_id' => $id,
                        'product_id' => $request->details[$i]['product_id'],
                        'remarks' => $request->details[$i]['remarks'],
                        'currency_id' => $request->details[$i]['currency_id'],
                        'unit_price' => $request->details[$i]['unit_price'],
                        'qty' => $request->details[$i]['qty'],
                        'total_price' => $request->details[$i]['total_price'],
                        'bdt_convert_rate' => $request->details[$i]['bdt_convert_rate'],
                        'total_amount_taka' => $request->details[$i]['total_amount_taka'],
                    ];
                    LcProformaInvoiceDetails::where('id', $request->details[$i]['id'])->update($updateData);
                } else {
                    $dataFormat[$i] =[
                        'lc_proforma_invoice_id' => $id,
                        'product_id' => $request->details[$i]['product_id'],
                        'remarks' => $request->details[$i]['remarks'],
                        'currency_id' => $request->details[$i]['currency_id'],
                        'unit_price' => $request->details[$i]['unit_price'],
                        'qty' => $request->details[$i]['qty'],
                        'total_price' => $request->details[$i]['total_price'],
                        'bdt_convert_rate' => $request->details[$i]['bdt_convert_rate'],
                        'total_amount_taka' => $request->details[$i]['total_amount_taka'],
                    ];
                }
            }

            if(count($dataFormat) > 0){
                LcProformaInvoiceDetails::insert($dataFormat);
            }

            if($app == 1){
                $this->approve($id);
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Proforma Invoice successfully updated!']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    public function destroy($id)
    {
        try{

            LcProformaInvoiceDetails::where('lc_proforma_invoice_id',$id)->delete();
            LcProformaInvoiceEntry::FindOrFail($id)->delete();

            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->errorInfo[1];
        }


        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Proforma Invoice successfully Deleted !']);
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
                'lc_proforma_invoice_id' => $id,
                'product_id' => $request->details[$i]['product_id'],
                'remarks' => $request->details[$i]['remarks'],
                'currency_id' => $request->details[$i]['currency_id'],
                'unit_price' => $request->details[$i]['unit_price'],
                'qty' => $request->details[$i]['qty'],
                'total_price' => $request->details[$i]['total_price'],
                'bdt_convert_rate' => $request->details[$i]['bdt_convert_rate'],
                'total_amount_taka' => $request->details[$i]['total_amount_taka'],
            ];
        }

        return $dataFormat;
    }

    public function approve($id){
        $approval = DB::table('lc_proforma_invoice_entries')->where('id',$id)->first()->approve_status;
        if($approval == 0)
        {
            DB::table('lc_proforma_invoice_entries')->where('id', $id)->update(array(
                'approve_status'    => 1,
                'approve_by'        => Auth::user()->id,
                'approve_at'        => Carbon::now(),
            ));
        }
    }
}
