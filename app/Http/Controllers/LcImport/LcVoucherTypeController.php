<?php

namespace App\Http\Controllers\LcImport;

use App\Http\Controllers\Controller;
use App\Model\LC\LcVoucherType;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LcVoucherTypeController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return datatables()->of(LcVoucherType::query())->toJson();
        }

        return view('lc.lc_setup.lc_voucher_type');
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'voucher_type_name' => 'required|unique:lc_voucher_type,voucher_type_name',
            'transaction_column' => 'required',
            'status' => 'required',
        ]);

        $input = $request->all();
        $input['created_by'] = Auth::user()->id;
        $input['created_at'] = Carbon::now();

        try {
            DB::beginTransaction();

            LcVoucherType::create($input);

            DB::commit();
            $bug = 0;
        } catch (Exception $e) {
            DB::rollback();
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'LC Voucher Type successfully saved !']);
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
        return LcVoucherType::FindOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'voucher_type_name' => 'required|unique:lc_voucher_type,voucher_type_name,' . $id,
            'transaction_column' => 'required',
            'status' => 'required',
        ]);

        $data = LcVoucherType::findOrFail($id);
        $input = $request->all();
        $input['updated_by'] = Auth::user()->id;
        $input['updated_at'] = Carbon::now();

        try {
            DB::beginTransaction();

            $data->update($input);

            DB::commit();
            $bug = 0;
        } catch (Exception $e) {
            DB::rollback();
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'LC Voucher Type successfully Updated !']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }

    public function destroy($id)
    {
        $data = LcVoucherType::FindOrFail($id);
        try {
            $data->delete();
            $bug = 0;
        } catch (Exception $e) {
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'LC Voucher Type successfully Deleted !']);
        } elseif ($bug == 1451) {
            return response()->json(['status' => 'error', 'message' => 'This data is used another table,can not delete data']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }
}
