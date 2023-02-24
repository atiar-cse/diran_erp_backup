<?php

namespace App\Model\Payroll;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PayrollMonthlyPayGrade extends Model
{
    use SoftDeletes;
    protected $softDelete = true;
    protected $dates = ['deleted_at'];

    protected $fillable = ['pay_grade_name','gross_salary','percentage_of_basic','basic_salary','over_time_rate','status','created_by','updated_by'];
}
