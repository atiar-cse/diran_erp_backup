<?php

namespace App\Http\Controllers\HR\Employee;

use App\Model\HR\HrManageEmployee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class EmployeePermanentController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return HrManageEmployee::with('emp_roll_id','emp_designation','emp_department','emp_branch')->where('permanent_status','=',0)->orderBy('id','desc')->paginate($request->perPage);
        }
        return view('hr.employee_section.employee_permanent');
    }


    public function updatePermanent($id)
    {
        $approval = HrManageEmployee::where('id',$id)->first()->permanent_status;
        //dd($approval);

        if($approval ==0){
                HrManageEmployee::where('id', $id)->update(['permanent_status' => 1]);
                return response()->json(['status' => 'success', 'message' => 'Successfully permanented!']);
           }
       }



    public function store(Request $request)
    {
        //
    }


    public function show($id)
    {
        //
    }


    public function edit($id)
    {
        //
    }


    public function update(Request $request, $id)
    {
        //
    }


    public function destroy($id)
    {
        //
    }
}
