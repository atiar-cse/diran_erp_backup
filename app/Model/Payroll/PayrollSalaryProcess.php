<?php

namespace App\Model\Payroll;

use App\Model\HR\HrManageEmployee;
use Illuminate\Database\Eloquent\Model;

class PayrollSalaryProcess extends Model
{
    protected  $fillable =[
        'id',
        'employee_id',
        'department_id',
        'designation_id',
        'gross_salary',
        'basic_salary',
        'hourly_pay_rate',
        'monthly_pay_grade_name',
        'total_working_days',
        'total_holidays',
        'total_weekends',
        'total_number_of_leave',
        'pay_days',
        'joining_date',
        'emp_status',
        'salary_month'
    ];

    function get_emp_name()
    {
        return $this->belongsTo(HrManageEmployee::class,'employee_id')->with('emp_department');
    }
}
