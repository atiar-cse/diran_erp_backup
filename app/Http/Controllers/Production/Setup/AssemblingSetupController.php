<?php

namespace App\Http\Controllers\Production\Setup;

use App\Model\Production\ProductionAssemblingSetupDetails;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Production\ProductionAssemblingSetup;
use App\Model\Production\ProductionProducts;

use Illuminate\Support\Facades\Auth;
use DB;


class AssemblingSetupController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {
            //return ProductionAssemblingSetup::with('product')->orderBy('id','desc')->paginate($request->perPage);

            $query = DB::table('production_assembling_setups')
                ->leftJoin('production_products', 'production_assembling_setups.finishing_product_id', '=', 'production_products.id')
                ->leftJoin('users', 'production_assembling_setups.created_by', '=', 'users.id')
                ->whereNull('production_assembling_setups.deleted_at')
                ->select(['production_assembling_setups.id AS id',
                        'production_assembling_setups.finishing_product_qty',
                        'production_assembling_setups.status',
                        'production_assembling_setups.created_by',
                        'production_assembling_setups.updated_by',
                        'production_products.product_name',
                        'users.user_name',
                    ]);

            return datatables()->of($query)->toJson();

        }

        $finishingProductList  = ProductionProducts::where('status', '1')->where('is_raw_material_status', '0')->with('category')->get();
        $fittingProductList    = ProductionProducts::where('status', '1')
            //->where('is_raw_material_status', '1')
            ->with('category')->get();
        return view('production.production_setup.assembling_setup',compact('finishingProductList','fittingProductList'));
    }




    public function store(Request $request)
    {
        $this->validate($request, [
            'finishing_product_id' => 'required',
            'finishing_product_qty' => 'required',

            'details.*.fitting_product_id' => 'required',
            'details.*.fitting_product_qty' => 'required',
        ], [

            'details.*.fitting_product_id.required' => 'Required field.',
            'details.*.fitting_product_qty.required' => 'Required field.',
        ]);

        $data = [
            'finishing_product_id' => $request->finishing_product_id,
            'finishing_product_qty' => $request->finishing_product_qty,
            'created_by' => Auth::user()->id,
        ];

        try {
            DB::beginTransaction();

            $result = ProductionAssemblingSetup::create($data);
            $details = $this->dataFormat($request, $result->id);

            ProductionAssemblingSetupDetails::insert($details);

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Assembling Setup Successfully saved!']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }


    public function show($id)
    {
        //
    }


    public function edit($id)
    {
        try {
            $editModeData = ProductionAssemblingSetup::FindOrFail($id);
            $editModeDetailsData = ProductionAssemblingSetupDetails::where('production_assembling_setup_id',$id)->get();
            $data = [
                'id'    => $editModeData->id,
                'finishing_product_id' => $editModeData->finishing_product_id,
                'finishing_product_qty' => $editModeData->finishing_product_qty,
                'deleteID' => [],
                'details'    => $editModeDetailsData
            ];
            return response()->json(['status'=>'success','data'=>$data]);
        } catch(\Exception $e){
            return response()->json(['status'=>'error','data'=>[]]);
        }


    }

    public function update(Request $request,$id)
    {
        $this->validate($request, [
            'finishing_product_id' => 'required',
            'finishing_product_qty' => 'required',

            'details.*.fitting_product_id' => 'required',
            'details.*.fitting_product_qty' => 'required',
        ], [

            'details.*.fitting_product_id.required' => 'Required field.',
            'details.*.fitting_product_qty.required' => 'Required field.',
        ]);


        $data = [
            'finishing_product_id' => $request->finishing_product_id,
            'finishing_product_qty' => $request->finishing_product_qty,
            'updated_by' => Auth::user()->id,
        ];


        try {
            DB::beginTransaction();

            $data = [
                'finishing_product_id' => $request->finishing_product_id,
                'finishing_product_qty' => $request->finishing_product_qty,
                'updated_by' => Auth::user()->id,
            ];

            $editModeData = ProductionAssemblingSetup::FindOrFail($id);
            $editModeData->update($data);

            /* Insert update and delete assembling setup details  */

            if (count($request->deleteID) > 0) {
                ProductionAssemblingSetupDetails::whereIn('id', $request->deleteID)->delete();
            }

            $dataFormat = [];
            $count = count($request->details);
            for ($i = 0; $i < $count; $i++) {
                if (isset($request->details[$i]['id']) && $request->details[$i]['id'] !='') {
                    $updateData = [
                        'production_assembling_setup_id'    => $id,
                        'fitting_product_id'                => $request->details[$i]['fitting_product_id'],
                        'fitting_product_qty'               => $request->details[$i]['fitting_product_qty'],
                    ];
                    ProductionAssemblingSetupDetails::where('id', $request->details[$i]['id'])->update($updateData);
                } else {
                    $dataFormat[$i] =[
                        'production_assembling_setup_id'    => $id,
                        'fitting_product_id'                => $request->details[$i]['fitting_product_id'],
                        'fitting_product_qty'               => $request->details[$i]['fitting_product_qty'],
                    ];
                }
            }

            if(count($dataFormat) > 0){
                ProductionAssemblingSetupDetails::insert($dataFormat);
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Assembling Setup successfully updated!']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }



    public function destroy($id)
    {

        try{

            ProductionAssemblingSetupDetails::where('production_assembling_setup_id',$id)->delete();
            ProductionAssemblingSetup ::FindOrFail($id)->delete();

            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->errorInfo[1];
        }


        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Assembling Setup successfully Deleted !']);
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
                'production_assembling_setup_id' => $id,
                'fitting_product_id' => $request->details[$i]['fitting_product_id'],
                'fitting_product_qty' => $request->details[$i]['fitting_product_qty'],
            ];
        }

        return $dataFormat;
    }
}
