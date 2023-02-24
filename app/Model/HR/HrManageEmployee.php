<?php

namespace App\Model\HR;

use App\Model\Payroll\PayrollMonthlyPayGrade;
use App\Model\Payroll\PayrollSalaryProcess;
use App\Model\UserAccessControl\AclRole;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HrManageEmployee extends Model
{
    use SoftDeletes;
    protected $softDelete = true;
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'user_id',
        'role_id',
        'user_name',
        'password',
        'first_name',
        'last_name',
        'emp_id',
        'fingerprint_no',
        'supervisor',
        'department_id',
        'designation_id',
        'branch_id',
        'work_shift_id',
        'employee_type_id',
        'team_id',
        'salary_grade_id',
        'gross_salary',
        'basic_salary',
        'house_rent',
        'medical',
        'transport',
        'lunch',
        'compliance_salary',
        'comp_basic_salary',
        'comp_house_rent',
        'comp_medical',
        'comp_transport',
        'comp_lunch',
        'monthly_pay_grade_id',
        'hourly_pay_grade_id',
        'religion_id',
        'emp_email',
        'phone',
        'gender',
        'date_of_birth',
        'date_of_joining',
        'date_of_leaving',
        'marital_status',
        'emergency_contact',
        'photo',
        'em_address',
        'report_two_id',
        'status',
        'created_by',
        'updated_by'
    ];

    public function emp_designation(){

        return $this->belongsTo(HrDesignation::class,'designation_id');
    }

    public function emp_department(){

        return $this->belongsTo(HrDepartment::class,'department_id');
    }

    public function emp_roll_id(){

        return $this->belongsTo(AclRole::class,'role_id');
    }

    public function emp_branch(){

        return $this->belongsTo(HrBranch::class,'branch_id');
    }

    public function get_pay_grade()
    {

        return $this->belongsTo(PayrollMonthlyPayGrade::class, 'monthly_pay_grade_id');
    }

    public function get_salary(){

    return $this->belongsTo(PayrollMonthlyPayGrade::class,'monthly_pay_grade_id');
    }



}
