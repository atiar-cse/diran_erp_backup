<?php

namespace App\Http\Controllers\HR\Employee;

use App\Model\HR\HrBranch;
use App\Model\HR\HrDepartment;
use App\Model\HR\HrDesignation;
use App\Model\HR\HrManageEmployee;
use App\Model\HR\HrShiftManage;
use App\Model\HR\HrUnit;
use App\Model\Payroll\PayrollHourlyPayGrade;
use App\Model\Payroll\PayrollMonthlyPayGrade;
use App\Model\UserAccessControl\AclRole;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use DB;
use Hash;
use Carbon\Carbon;
use Image;

class ManageEmployeeController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return HrManageEmployee::with('emp_designation','emp_department')->where('status','=',1)->orderBy('id','desc')->paginate($request->perPage);
        }

        $roleList             = AclRole::where('status', '1')->orderBy('role_name','ASC')->get();
        $departmentList       = HrDepartment::where('status', '1')->orderBy('department_name','ASC')->get();
        $designationList      = HrDesignation::where('status', '1')->orderBy('designation_name','ASC')->get();
        $branchList           = HrUnit::where('status', '1')->orderBy('unit_name','ASC')->get();
        $monthlyPayGradeList  = [];//PayrollMonthlyPayGrade::where('status', '1')->orderBy('role_name','ASC')->get();
        $hourlyPayGradeList   = [];//PayrollHourlyPayGrade::where('status', '1')->orderBy('role_name','ASC')->get();
        $ShiftList            = HrShiftManage::where('status', '1')->orderBy('shift_name','ASC')->get();//PayrollHourlyPayGrade::where('status', '1')->orderBy('role_name','ASC')->get();
        $typeList             = getEmpTypeList();
        $teamList             = getTeamList();
        $religionList         = getReligionList();
        $salaryGradeList      = getGradeList();
        $salaryConfigurationList = DB::table('hr_salary_configurations')->pluck('percentage','key_name');
        return view('hr.employee_section.manage_employee',compact('roleList','departmentList','designationList','branchList','monthlyPayGradeList','hourlyPayGradeList','ShiftList','typeList','teamList','religionList','salaryGradeList','salaryConfigurationList'));
    }





    public function store(Request $request)
    {
        $this->validate($request, [
            'user_name' =>'unique:hr_manage_employees,user_name',
            'role_id'   =>'required',
            'emp_email' =>'required',
            'first_name'=>'required',
            'emp_id'    =>'required',
            //'department_id'=>'required',
            'designation_id'=>'required',
            //'work_shift_id'=>'required',
            //'employee_type_id'=>'required',
            //'salary_grade_id'=>'required',
            //'phone'=>'required',
            //'gender'=>'required',
            //'date_of_birth'=>'required',
            //'date_of_joining'=>'required',
            //'marital_status'=>'required',

        ]);

        $imageData = $request->get('photo');

        if ($imageData) {
            $fileName = Carbon::now()->timestamp . '_' . uniqid() . '.' . explode('/', explode(':', substr($imageData, 0, strpos($imageData, ';')))[1])[1];
            Image::make($request->get('photo'))->save(public_path('/uploads/employee_photo/') . $fileName);
            $userData['user_photo'] = $fileName;
        }

        $userData =[
            'role_id'              => $request->role_id,
            'user_name'            => $request->user_name,
            'password'             => Hash::make($request->password),
            'email'                => $request->emp_email,
            'user_photo'           => isset($fileName) ? $fileName : null,
        ];

        $user_ID = User::create($userData);


        $data = [
            'role_id'              => $request->role_id,
            'user_id'              => $user_ID->id,
            'user_name'            => $request->user_name,
            'password'             => Hash::make($request->password),
            'first_name'           => $request->first_name,
            'last_name'            => $request->last_name,
            'emp_id'               => $request->emp_id,
            'fingerprint_no'       => $request->fingerprint_no,
            'supervisor'           => $request->supervisor,
            'department_id'        => $request->department_id,
            'designation_id'       => $request->designation_id,
            'branch_id'            => $request->branch_id,
            'work_shift_id'        => $request->work_shift_id,
            'employee_type_id'     => $request->employee_type_id,
            'team_id'              => $request->team_id,
            'salary_grade_id'      => $request->salary_grade_id,

            'gross_salary'         => $request->gross_salary,
            'basic_salary'         => $request->basic_salary,
            'house_rent'           => $request->house_rent,
            'medical'              => $request->medical,
            'transport'            => $request->transport,
            'lunch'                => $request->lunch,

            'compliance_salary'    => $request->compliance_salary,
            'comp_gross_salary'    => $request->comp_gross_salary,
            'comp_basic_salary'    => $request->comp_basic_salary,
            'comp_house_rent'      => $request->comp_house_rent,
            'comp_medical'         => $request->comp_medical,
            'comp_transport'       => $request->comp_transport,
            'comp_lunch'           => $request->comp_lunch,

            'monthly_pay_grade_id' => $request->monthly_pay_grade_id,
            'hourly_pay_grade_id'  => $request->hourly_pay_grade_id,
            'emp_email'            => $request->emp_email,
            'phone'                => $request->phone,
            'gender'               => $request->gender,
            'religion_id'          => $request->religion_id,
            'report_two_id'        => $request->report_two_id,
            'date_of_birth'        => dateConvertFormtoDB($request->date_of_birth),
            'date_of_joining'      => dateConvertFormtoDB($request->date_of_joining),
            'date_of_leaving'      => dateConvertFormtoDB($request->date_of_leaving),
            'marital_status'       => $request->marital_status,
            'emergency_contact'    => $request->emergency_contact,
            'em_address'           => $request->em_address,
            //'photo'                => $request->photo,
            'created_by'           => Auth::user()->id,
        ];


        try {
            DB::beginTransaction();

             HrManageEmployee::create($data);

            DB::commit();
            $bug = 0;
        } catch (\Exception $e) {
            DB::rollback();
            print_r($e->getMessage());
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Employee successfully saved !']);
        } elseif ($bug == 1062) {
            return response()->json(['status' => 'error', 'message' => 'Employee is Found Duplicate']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }


    public function show($id)
    {

    }


    public function edit($id)
    {
        try {
            $editModeData = HrManageEmployee::FindOrFail($id);
            $data = [
                'id'                   => $editModeData->id,
                'role_id'              => $editModeData->role_id,
                'user_id'              => $editModeData->user_id,
                'user_name'            => $editModeData->user_name,
                //'password'             => $editModeData->password,
                'first_name'           => $editModeData->first_name,
                'last_name'            => $editModeData->last_name,
                'emp_id'               => $editModeData->emp_id,
                'fingerprint_no'       => $editModeData->fingerprint_no,
                'supervisor'           => $editModeData->supervisor,
                'department_id'        => $editModeData->department_id,
                'designation_id'       => $editModeData->designation_id,
                'branch_id'            => $editModeData->branch_id,
                'work_shift_id'        => $editModeData->work_shift_id,
                'employee_type_id'     => $editModeData->employee_type_id,
                'team_id'              => $editModeData->team_id,
                'salary_grade_id'      => $editModeData->salary_grade_id,

                'gross_salary'         => $editModeData->gross_salary,
                'basic_salary'         => $editModeData->basic_salary,
                'house_rent'           => $editModeData->house_rent,
                'medical'              => $editModeData->medical,
                'transport'            => $editModeData->transport,
                'lunch'                => $editModeData->lunch,

                'compliance_salary'    => $editModeData->compliance_salary,
                'comp_gross_salary'    => $editModeData->comp_gross_salary,
                'comp_basic_salary'    => $editModeData->comp_basic_salary,
                'comp_house_rent'      => $editModeData->comp_house_rent,
                'comp_medical'         => $editModeData->comp_medical,
                'comp_transport'       => $editModeData->comp_transport,
                'comp_lunch'           => $editModeData->comp_lunch,

                'monthly_pay_grade_id' => $editModeData->monthly_pay_grade_id,
                'hourly_pay_grade_id'  => $editModeData->hourly_pay_grade_id,
                'emp_email'            => $editModeData->emp_email,
                'phone'                => $editModeData->phone,
                'gender'               => $editModeData->gender,
                'religion_id'          => $editModeData->religion_id,
                'date_of_birth'        => dateConvertDBtoForm($editModeData->date_of_birth),
                'date_of_joining'      => dateConvertDBtoForm($editModeData->date_of_joining),
                'date_of_leaving'      => dateConvertDBtoForm($editModeData->date_of_leaving),
                'marital_status'       => $editModeData->marital_status,
                'emergency_contact'    => $editModeData->emergency_contact,
                'em_address'           => $editModeData->em_address,
                'photo'                => $editModeData->photo,

            ];
            return response()->json(['status'=>'success','data'=>$data]);
        } catch(\Exception $e){
            return response()->json(['status'=>'error','data'=>[]]);
        }
    }


    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'user_name' =>'required',
            'role_id'   =>'required',
            'emp_email' =>'required',
            'first_name'=>'required',
            'emp_id'    =>'required',
            //'department_id'=>'required',
            'designation_id'=>'required',
            //'work_shift_id'=>'required',
            //'employee_type_id'=>'required',
            //'salary_grade_id'=>'required',
            //'phone'=>'required',
            //'gender'=>'required',
            //'date_of_birth'=>'required',
            //'date_of_joining'=>'required',
            //'marital_status'=>'required',

        ]);



        $data = [

            'role_id'              => $request->role_id,
            'user_id'              => $request->user_id,
            'user_name'            => $request->user_name,
            'password'             => Hash::make($request->password),
            'first_name'           => $request->first_name,
            'last_name'            => $request->last_name,
            'emp_id'               => $request->emp_id,
            'fingerprint_no'       => $request->fingerprint_no,
            'supervisor'           => $request->supervisor,
            'department_id'        => $request->department_id,
            'designation_id'       => $request->designation_id,
            'branch_id'            => $request->branch_id,
            'work_shift_id'        => $request->work_shift_id,
            'employee_type_id'     => $request->employee_type_id,
            'team_id'              => $request->team_id,
            'salary_grade_id'      => $request->salary_grade_id,

            'gross_salary'         => $request->gross_salary,
            'basic_salary'         => $request->basic_salary,
            'house_rent'           => $request->house_rent,
            'medical'              => $request->medical,
            'transport'            => $request->transport,
            'lunch'                => $request->lunch,

            'compliance_salary'    => $request->compliance_salary,
            'comp_gross_salary'    => $request->comp_gross_salary,
            'comp_basic_salary'    => $request->comp_basic_salary,
            'comp_house_rent'      => $request->comp_house_rent,
            'comp_medical'         => $request->comp_medical,
            'comp_transport'       => $request->comp_transport,
            'comp_lunch'           => $request->comp_lunch,

            'monthly_pay_grade_id' => $request->monthly_pay_grade_id,
            'hourly_pay_grade_id'  => $request->hourly_pay_grade_id,
            'emp_email'            => $request->emp_email,
            'phone'                => $request->phone,
            'gender'               => $request->gender,
            'religion_id'          => $request->religion_id,
            'report_two_id'        => $request->report_two_id,
            'date_of_birth'        => dateConvertFormtoDB($request->date_of_birth),
            'date_of_joining'      => dateConvertFormtoDB($request->date_of_joining),
            'date_of_leaving'      => dateConvertFormtoDB($request->date_of_leaving),
            'marital_status'       => $request->marital_status,
            'emergency_contact'    => $request->emergency_contact,
            'em_address'           => $request->em_address,
            //'photo'                => $request->photo,
            'updated_by'           => Auth::user()->id,
        ];

        $imageData = $request->get('photo');

        if ($imageData) {
            $fileName = Carbon::now()->timestamp . '_' . uniqid() . '.' . explode('/', explode(':', substr($imageData, 0, strpos($imageData, ';')))[1])[1];
            Image::make($request->get('photo'))->save(public_path('/uploads/employee_photo/') . $fileName);
            //$FilePath= url('/'). "/uploads/employee_photo/".$fileName;
            $data['photo'] = $fileName;
            /* if ($editModeData->photo != '' && file_exists('uploads/employee_photo/' . $editModeData->photo)) {
                 unlink('uploads/employee_photo/' . $editModeData->photo);
             }*/

        }

        try {
            DB::beginTransaction();

            $editModeData = HrManageEmployee::FindOrFail($id);

            $editModeData->update($data);

            $userData =[
                'role_id'              => $editModeData->role_id,
                'user_name'            => $editModeData->user_name,
                'password'             => Hash::make($editModeData->password),
                'email'                => $editModeData->emp_email,
                'user_photo'           => $editModeData->photo,
            ];

            User::where('id',$editModeData->user_id)->update($userData);

            DB::commit();
            $bug = 0;
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'LC Opening Section successfully updated!']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }


    public function destroy($id)
    {
        $data = HrManageEmployee::FindOrFail($id);
        try{
            $data->delete();
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Warning Agnet successfully Deleted !']);
        } elseif ($bug == 1451) {
            return response()->json(['status' => 'error', 'message' => 'This data is used another table,can not delete data']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something Error Found !, Please try again']);
        }
    }
}
