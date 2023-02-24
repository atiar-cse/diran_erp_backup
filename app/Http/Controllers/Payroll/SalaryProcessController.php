<?php

namespace App\Http\Controllers\Payroll;

use App\Model\Payroll\PayrollSalaryProcess;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\HR\HrManageEmployee;
use App\Model\HR\HrWeeklyHoliday;
use Auth;
use DB;

class SalaryProcessController extends Controller
{

    public function index()
    {
       return view('payroll.payroll_section.salary_process');
    }


    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        $queryEmpResult = HrManageEmployee::select(

            'hr_manage_employees.emp_id',
            'hr_manage_employees.department_id',
            'hr_manage_employees.designation_id',
            'hr_manage_employees.gross_salary',
            'hr_manage_employees.basic_salary',
            'hr_manage_employees.monthly_pay_grade_id',
            'hr_manage_employees.hourly_pay_grade_id',
            'hr_manage_employees.date_of_joining',
            'hr_manage_employees.created_at',
            'hr_manage_employees.updated_at',
            'hr_manage_employees.permanent_status'
        )

            ->where('hr_manage_employees.permanent_status','=',1)
            ->get();



        try {
            DB::beginTransaction();

            $workdays = array();
            $type = CAL_GREGORIAN;
            $month = date('n'); // Month ID, 1 through to 12.
            $year = date('Y'); // Year in 4 digit 2009 format.
            $day_count = cal_days_in_month($type, $month, $year); // Get the amount of days
            //loop through all days
            for ($i = 1; $i <= $day_count; $i++) {
                $date = $year.'/'.$month.'/'.$i; //format date
                $get_name = date('l', strtotime($date)); //get week day
                $day_name = substr($get_name, 0, 3); // Trim day name to 3 chars

                //if not a weekend add day to array
                if($day_name != 'Fri'){
                    $workdays[] = $i;
                }
            }
            $totalWorkingDays = count($workdays);
            $totalWeekEnd     = $day_count -$totalWorkingDays;
            $payDays          = $day_count;
            //$days = count($workdays)-$leaveday;
            $data=[];

            if($queryEmpResult){
                foreach ($queryEmpResult as $key=>$value) {

                    $data[] =[

                        'emp_id'                    => $value['emp_id'],
                        'department_id'             => $value['department_id'],
                        'designation_id'            => $value['designation_id'],
                        'gross_salary'              => $value['gross_salary'],
                        'basic_salary'              => $value['basic_salary'],
                        'hourly_pay_grade_id'       => $value['hourly_pay_grade_id'],
                        'permanent_status'          => $value['permanent_status'],
                        'date_of_joining'           => $request->date_of_joining,
                        'created_at'                => $request->created_at,
                        'updated_at'                => $request->updated_at,

                        'total_working_days'        => $totalWorkingDays,
                        //'total_holidays'=> $value['total_holidays'],
                        'total_weekends'       => $totalWeekEnd,
                        //'total_number_of_leave'=> $value['total_number_of_leave'],
                        'pay_days'              => $payDays,

                    ];

                    $input['employee_id']            = $value['emp_id'];
                    $input['department_id']          = $value['department_id'];
                    $input['designation_id']         = $value['designation_id'];
                    $input['gross_salary']           = $value['gross_salary'];
                    $input['basic_salary']           = $value['basic_salary'];
                    $input['hourly_pay_rate']        = $value['hourly_pay_grade_id'];
                    $input['emp_status']             = $value['permanent_status'];

                    $input['joining_date']           = $value['date_of_joining'];

                    $input['created_at']             = $value['created_at'];
                    $input['updated_at']             = $value['updated_at'];

                    $input['salary_month']           = $request->salary_month;

                    $input['total_working_days']     = $totalWorkingDays;
                    //$input['total_holidays']   = $value['total_holidays'];
                    $input['total_weekends']         = $totalWeekEnd;
                    // $input['total_number_of_leave']   = $value['total_number_of_leave'];
                    $input['pay_days']               = $payDays;


                    $resultData = PayrollSalaryProcess::create($input);

                }
            }

            DB::commit();
            $bug = 0;
       }
            catch (\Exception $e) {
            DB::rollback();
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return response()->json(['status' => 'success', 'message' => 'Salary successfully saved !']);
        } elseif ($bug == 1062) {
            return response()->json(['status' => 'error', 'message' => 'Salary is Found Duplicate']);
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
