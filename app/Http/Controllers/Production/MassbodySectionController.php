<?php

namespace App\Http\Controllers\Production;

use App\Model\Production\ProductionRequisitionForRm;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Model\Production\ProductionMassbody;
use DB;
use App\Model\Production\ProductionRecycleChip;

class MassbodySectionController extends Controller
{

    public function index(Request $request)
    {
        if ($request->filled('get_latest_massbody')) {
            return $this->getLatestMmassbody();
        }

        if($request->ajax()) {

            $query = DB::table('production_massbodies')
                ->leftJoin('users as ura', 'production_massbodies.created_by','=','ura.id')
                ->leftJoin('users as ured', 'production_massbodies.updated_by','=','ured.id')
                ->leftJoin('users as urea', 'production_massbodies.approve_by','=','urea.id')
                ->whereNull('production_massbodies.deleted_at')
                ->select(['production_massbodies.id AS id',
                    'production_massbodies.massbody_no',
                    'production_massbodies.massbody_date',
                    'production_massbodies.created_by',
                    'production_massbodies.updated_by',
                    'production_massbodies.approve_by',
                    'production_massbodies.approve_status',
                    'ura.user_name as ad_name',
                    'ured.user_name as ed_name',
                    'urea.user_name as ap_name'
                ]);

            return datatables()->of($query)->toJson();
        }
       
        $alreadyReqIDs = ProductionMassbody::where('approve_status', '1')
                                ->pluck('requisition_for_raw_material_id');
        $rmRequisitionList = ProductionRequisitionForRm::where('approve_status', '1')
                                    ->Select('id','total_qty','total_amount','rm_requisition_no')
                                    ->whereNotIn('id',$alreadyReqIDs)    
                                    ->where('requisition_types','Massbody Requisition')
                                    ->get();

        $recycleChip = ProductionRecycleChip::First();

        return view('production.production_section.massbody_section',compact('rmRequisitionList','recycleChip'));
    }

    public function massbodyNoGenerate(){
        $id = ProductionMassbody::withTrashed()->count();
        $currentId = $id+1;
        return 'MSB-'.date('Ym').$currentId;
    }

    public function approve($id)
    {
        $approval = ProductionMassbody::where('id', $id)->first()->approve_status;
        if ($approval == 0) {
            $appData = ProductionMassbody::FindOrFail($id);

            $recycleChip = ProductionRecycleChip::First(); 
            if ($recycleChip) {
                if ($appData->recycle_chip_weight > $recycleChip->recycle_chip_current_weight) {
                    $current_amount = $recycle_chip_total_amt = 0;
                } else {
                    $current_amount = $recycleChip->recycle_chip_current_weight - $appData->recycle_chip_weight;
                    $recycle_chip_total_amt = $current_amount * $recycleChip->recycle_chip_unit_price;
                }
                $recycleChip->recycle_chip_current_weight =  $current_amount;

                $recycleChip->recycle_chip_total_amt =  $recycle_chip_total_amt;

                $recycleChip->updated_by = Auth::user()->id;
                $recycleChip->save();                
            }  

            $appData->approve_status = 1;         
            $appData->approve_by = Auth::user()->id;
            $appData->approve_at = Carbon::now();
            $appData->save();
        }
    }

    public function store(Request $request)
    {
        $inv_no_like = 'MSB-';
        $rtn_val =InvStore($request->massbody_no, $inv_no_like,
            'production_massbodies','massbody_no');

        $request->merge(['massbody_no' => $rtn_val]) ;

        $this->validate($request, [
            'massbody_no'=>'required|unique:production_massbodies,massbody_no',
            'massbody_date'=>'required',
            'requisition_for_raw_material_id'=>'required',
            'green_chip_name'=>'required',
            'green_chip_percentage'=>'required|numeric',
            'green_chip_weight'=>'required|numeric',
            'recycle_chip_name'=>'required',
            'recycle_chip_percentage'=>'required|numeric',
            'recycle_chip_weight'=>'required|numeric'
        ]);

        $input = $request->all();
        $input['created_by'] = Auth::user()->id;
        $input['massbody_date'] = dateConvertFormtoDB($request->massbody_date);

        try {

            //Calculate unit price [green chip]
            $input['green_chip_unit_price'] = round( ($request->green_chip_amount / $request->green_chip_weight),2);

            DB::beginTransaction();

            $app = $request->approve_status;
            $result =ProductionMassbody::create($input);

            if($app == 1){
                $this->approve($result->id);

                if($request->updated_by == Null){
                    DB::table('production_massbodies')->where('id', $result->id)->update(array('updated_by' => Auth::user()->id));
                }
            }

            DB::commit();
            
            return response()->json(['status' => 'success', 'message' => "Massbody # " . $request->massbody_no .' Successfully Saved!']);
        } catch (\Exception $e) {
            DB::rollback();
            print_r($e->getMessage());
            
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);           
        }
    }


    public function show($id)
    {
        $editData = ProductionMassbody::with('requisition')->FindOrFail($id);
        $editModeData = $editData;
        $editModeData['massbody_date'] = dateConvertDBtoForm($editData->massbody_date);
        return $editModeData;
    }

    public function edit($id)
    {
        $editData = ProductionMassbody::FindOrFail($id);
        $editModeData = $editData;
        $editModeData['massbody_date'] = dateConvertDBtoForm($editData->massbody_date);

        return $editModeData;
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'massbody_date'=>'required',
            'requisition_for_raw_material_id'=>'required',
            'green_chip_name'=>'required',
            'green_chip_percentage'=>'required|numeric',
            'green_chip_weight'=>'required|numeric',
            'recycle_chip_name'=>'required',
            'recycle_chip_percentage'=>'required|numeric',
            'recycle_chip_weight'=>'required|numeric'
        ]);

        $data = ProductionMassbody::findOrFail($id);
        $input = $request->all();
        $input['massbody_date'] = dateConvertFormtoDB($request->massbody_date);

        try {
            //Calculate unit price [green chip]
            /* $input['green_chip_unit_price'] = round($request->green_chip_amount / $request->green_chip_weight); */
                                                        
            DB::beginTransaction();
            $app = $request->approve_status;

            if ($app!=1) {
                $input['updated_by'] = Auth::user()->id;
            }

            $data->update($input);


            if($app == 1){
                $this->approve($id);
            }

            DB::commit();

            return response()->json(['status' => 'success', 'message' => 'Massbody successfully Updated !']);
        } catch (\Exception $e) {
            DB::rollback();
            print_r($e->getMessage());
            
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }


    public function destroy($id)
    {
        $data = ProductionMassbody ::FindOrFail($id);
        try{
            $data->delete();
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->errorInfo[1];
        }


        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Massbody entry successfully Deleted !']);
        } elseif ($bug == 1451) {
            return response()->json(['status' => 'error', 'message' => 'This data is used another table,can not delete data']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    public function getRequisitionWeight(Request $request)
    {
        $data = ProductionRequisitionForRm::where('id',$request->requisition_id)->first();
        return $data->total_qty;
    }

    private function getLatestMmassbody()
    {
        $editData = ProductionMassbody::with('requisition')
                            ->where('approve_status',1)
                            ->orderBy('id','DESC')
                            ->First();
        $editModeData = $editData;
        $editModeData['massbody_date'] = dateConvertDBtoForm($editData->massbody_date);
        return $editModeData;
    }
}
