<?php

namespace App\Model\HR;

use App\Model\Payroll\PayrollMonthlyPayGrade;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HrPromotion extends Model
{
    use SoftDeletes;
    protected $softDelete = true;
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'employee_id',
        'current_department_id',
        'current_designation_id',
        'current_pay_grade_id',
        'current_salary',
        'promoted_pay_grade',
        'new_salary',
        'promoted_department',
        'promoted_designation',
        'promotion_date',
        'narration',
        'status',
        'created_by',
        'updated_by'
    ];

    public function get_employee(){

        return $this->belongsTo(HrManageEmployee::class,'employee_id');
    }

    public function get_department(){

        return $this->belongsTo(HrDepartment::class,'current_department_id');
    }

    public function get_designation(){

        return $this->belongsTo(HrDesignation::class,'current_designation_id');
    }

    public function get_current_paygrade(){

        return $this->belongsTo(PayrollMonthlyPayGrade::class,'current_pay_grade_id');
    }

    public function get_promoted_paygrade(){

        return $this->belongsTo(PayrollMonthlyPayGrade::class,'promoted_pay_grade');
    }

    public function get_promoted_department(){

        return $this->belongsTo(HrDepartment::class,'promoted_department');
    }

    public function get_promoted_designation(){

        return $this->belongsTo(HrDesignation::class,'promoted_designation');
    }
}
