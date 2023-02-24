<?php

namespace App\Http\Controllers\Purchase\Setup;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Purchase\PurchaseWareHouse;
use Illuminate\Support\Facades\Auth;
use DB;

class PurchaseWareHouseController extends Controller
{
    public function index(Request $request)
    {
        if ($request->filled('token_wh') && $request->token_wh == 'token_wh') {
            return  PurchaseWareHouse::where('status',1)->get();
        }
        return datatables()->of(PurchaseWareHouse::query())->toJson();
    }

    public function create()
    {
        return view('purchase.purchase_setup.purchase_ware_house');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'purchase_ware_houses_name'=>'required|unique:purchase_ware_houses,purchase_ware_houses_name',
            'purchase_ware_houses_mobile'=>'required',
            'purchase_ware_houses_address'=>'required',
            'status'=>'required',
        ]);

        $input = $request->all();
        $input['created_by'] = Auth::user()->id;

        try {
            DB::beginTransaction();
            PurchaseWareHouse::create($input);

            DB::commit();
            $bug = 0;
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Purchase Warhouse successfully saved !']);
        } elseif ($bug == 1062) {
            return response()->json(['status' => 'error', 'message' => 'Purchase Warhouse is Found Duplicate']);
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
        return PurchaseWareHouse::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'purchase_ware_houses_name'=>'required',
            'purchase_ware_houses_mobile'=>'required',
            'purchase_ware_houses_address'=>'required',
            'status'=>'required',
        ]);

        $data = PurchaseWareHouse::findOrFail($id);
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
            return response()->json(['status' => 'success', 'message' => 'Purchase Warhouse successfully Updated !']);
        } elseif ($bug == 1062) {
            return response()->json(['status' => 'error', 'message' => 'Purchase Warhouse is Found Duplicate']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    public function destroy($id)
    {
        $data = PurchaseWareHouse ::FindOrFail($id);
        try{
            $data->delete();
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->errorInfo[1];
        }


        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Purchase Warhouse successfully Deleted !']);
        } elseif ($bug == 1451) {
            return response()->json(['status' => 'error', 'message' => 'This data is used another table,can not delete data']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }
}
