<?php

namespace App\Http\Controllers\Production;

use App\Model\CompanyInfo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use DB;

use App\Model\Production\ProductionIndirectCostsType;

use App\Model\Production\ProductionIndirectCosts;
use App\Model\Production\ProductionIndirectCostDetails;

class ProductionIndirectCostsController extends Controller
{
    public function index(Request $request)
    {
        if ($request->filled('production_token') && $request->production_token == 'serial_no_generate') {
            return $this->serialNoGenerate();
        }
        if ($request->ajax()) {
            $query = DB::table('production_indirect_costs')
                ->leftJoin('users as ura', 'production_indirect_costs.created_by','=','ura.id')
                ->leftJoin('users as ured', 'production_indirect_costs.updated_by','=','ured.id')
                ->leftJoin('users as urea', 'production_indirect_costs.approve_by','=','urea.id')
                ->whereNull('production_indirect_costs.deleted_at')
                ->select(['production_indirect_costs.id AS id',
                    'production_indirect_costs.indir_no',
                    'production_indirect_costs.date',
                    'production_indirect_costs.total_amount',
                    'production_indirect_costs.remarks',
                    'production_indirect_costs.created_by',
                    'production_indirect_costs.updated_by',
                    'production_indirect_costs.approve_by',
                    'production_indirect_costs.approve_status',
                    'ura.user_name as ad_name',
                    'ured.user_name as ed_name',
                    'urea.user_name as ap_name'
                ]);
            return datatables()->of($query)->toJson();            
        }

        $indir_costs_types   = ProductionIndirectCostsType::where('status', '1')
                                            ->select('id','indirect_cost_type')
                                            ->get();
        return view('production.production_section.indirect_costs',compact('indir_costs_types'));
    }

    public function serialNoGenerate(){
        $id = ProductionIndirectCosts::withTrashed()->count();
        $currentId = $id+1;
        return 'PIndCost-'.date('Ym').$currentId;
    }

    public function approve($id)
    {
        $appData = ProductionIndirectCosts::where('approve_status', 0)->FindOrFail($id);
        
        $appData->approve_status = 1;
        $appData->approve_by = Auth::user()->id;
        $appData->approve_at = Carbon::now();
        $appData->save();
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'indir_no' => 'required',
            'date' => 'required',
            'total_amount' => 'required',

            'details.*.prod_indir_costs_type_id' => 'required',
            'details.*.amount' => 'required',
        ], [
            'date.required' => 'Required field.',
            'total_amount.required' => 'Required field.',

            'details.*.prod_indir_costs_type_id.required' => 'Required field.',
            'details.*.amount.required' => 'Required field.',            
        ]);

        $data = [
            'indir_no' => $request->indir_no,
            'date' => dateConvertFormtoDB($request->date),
            'total_amount' => $request->total_amount,
            'remarks' => $request->remarks,
            'created_by' => Auth::user()->id,
        ];

        $app = $request->approve_status;

        try {
            DB::beginTransaction();
            
            $result = ProductionIndirectCosts::create($data);

            $detailsData = $this->dataFormat($request->details, $result->id);
            ProductionIndirectCostDetails::insert($detailsData);

            if($app == 1){

                $this->approve($result->id);
                if($request->updated_by == Null){

                    $result->updated_by = Auth::user()->id;
                    $result->save();
                }
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Costs Successfully saved!']);
        } catch (\Exception $e) {
            DB::rollback();
            print_r($e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    public function show($id)
    {       
        try {
            $editModeData = ProductionIndirectCosts::FindOrFail($id);
            $details = ProductionIndirectCostDetails::with('get_indirect_cost_type')
                                    ->where('indir_cost_id',$id)
                                    ->get();

            $data = [
                'id'    => $editModeData->id,
                'indir_no' => $editModeData->indir_no,
                'date' => dateConvertDBtoForm($editModeData->date),
                'total_amount' => $editModeData->total_amount ,
                'remarks' => $editModeData->remarks,                
                'approve_status' => $editModeData->approve_status,
                'details' => $details,
            ];
            return response()->json(['status'=>'success','data'=>$data]);
        } catch(\Exception $e){
            print_r($e->getMessage());
            return response()->json(['status'=>'error','data'=>[]]);
        }
    }

    public function edit($id)
    {
        try {
            $editModeData = ProductionIndirectCosts::FindOrFail($id);
            $details = ProductionIndirectCostDetails::where('indir_cost_id',$id)->get();

            $data = [
                'id'    => $editModeData->id,
                'indir_no' => $editModeData->indir_no,
                'date' => dateConvertDBtoForm($editModeData->date),
                'total_amount' => $editModeData->total_amount,
                'remarks' => $editModeData->remarks,
                'details' => $details,
            ];
            return response()->json(['status'=>'success','data'=>$data]);
        } catch(\Exception $e){
            $msg = $e->getMessage();
            return response()->json(['status'=>'error','data'=>$msg]);
        }
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'indir_no' => 'required',
            'date' => 'required',
            'total_amount' => 'required',

            'details.*.prod_indir_costs_type_id' => 'required',
            'details.*.amount' => 'required',
        ], [
            'date.required' => 'Required field.',
            'total_amount.required' => 'Required field.',

            'details.*.prod_indir_costs_type_id.required' => 'Required field.',
            'details.*.amount.required' => 'Required field.',            
        ]);

        $app = $request->approve_status;

        try {
            DB::beginTransaction();

            $editModeData = ProductionIndirectCosts::FindOrFail($id);

            $editModeData->indir_no           = $request->indir_no;
            $editModeData->date         = dateConvertFormtoDB($request->date);
            $editModeData->total_amount         = $request->total_amount;
            $editModeData->remarks = $request->remarks;

            if ($app!=1) {
                $editModeData->updated_by = Auth::user()->id;
            }

            $editModeData->save();

            if($app == 1){
                $this->approve($id);
            }

            ProductionIndirectCostDetails::where('indir_cost_id',$id)->delete();
            
            $detailsData = $this->dataFormat($request->details, $id);
            ProductionIndirectCostDetails::insert($detailsData);

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Successfully updated!']);
        } catch (\Exception $e) {
            DB::rollback();
            print_r($e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    public function destroy($id)
    {
        try{
            ProductionIndirectCostDetails::where('indir_cost_id',$id)->delete();
            ProductionIndirectCosts::FindOrFail($id)->delete();
            $bug = 0;
        }
        catch(\Exception $e){
            print_r($e->getMessage());
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Successfully Deleted !']);
        } elseif ($bug == 1451) {
            return response()->json(['status' => 'error', 'message' => 'This data is used another table,can not delete data']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    public function dataFormat($details, $id)
    {
        $dataFormat = [];
        $count = count($details);
        for ($i = 0; $i < $count; $i++) {
            $dataFormat[$i] = [
                'indir_cost_id' => $id,
                'prod_indir_costs_type_id' => $details[$i]['prod_indir_costs_type_id'],
                'remarks' => $details[$i]['remarks'],
                'amount' => $details[$i]['amount'],
            ];
        }

        return $dataFormat;
    }    
}
