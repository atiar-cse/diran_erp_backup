<?php

namespace App\Http\Controllers\Purchase\Setup;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Purchase\PurchaseReturnType;
use Illuminate\Support\Facades\Auth;
use DB;
class PurchaseReturnTypeController extends Controller
{

    public function index(Request $request)
    {
        return PurchaseReturnType::orderBy('id', 'desc')->paginate($request->perPage);
    }

    public function create()
    {
        return view('purchase.purchase_setup.purchase_return_type');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'purchase_return_types_name'=>'required|unique:purchase_return_types,purchase_return_types_name',
            'status'=>'required',
        ]);

        $input = $request->all();
        $input['created_by'] = Auth::user()->id;

        try {
            DB::beginTransaction();

            PurchaseReturnType::create($input);

            DB::commit();
            $bug = 0;
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Purchase Return Type successfully saved !']);
        } elseif ($bug == 1062) {
            return response()->json(['status' => 'error', 'message' => 'Purchase Return Type is Found Duplicate']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }


    public function show($id)
    {
        //
    }


    public function edit($id)
    {
        return PurchaseReturnType::findOrFail($id);
    }


    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'purchase_return_types_name'=>'required',
            'status'=>'required',
        ]);

        $data = PurchaseReturnType::findOrFail($id);
        $input = $request->all();
        $input['updated_by'] = Auth::user()->id;

        try {
            DB::beginTransaction();

            $data->update($input);

            DB::commit();
            $bug = 0;
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Purchase Return Type successfully Updated !']);
        } elseif ($bug == 1062) {
            return response()->json(['status' => 'error', 'message' => 'Purchase Return Type is Found Duplicate']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }


    public function destroy($id)
    {
        $data = PurchaseReturnType ::FindOrFail($id);
        try{
            $data->delete();
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->errorInfo[1];
        }


        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Purchase Return Type successfully Deleted !']);
        } elseif ($bug == 1451) {
            return response()->json(['status' => 'error', 'message' => 'This data is used another table,can not delete data']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }
}
