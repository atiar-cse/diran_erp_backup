<?php

namespace App\Model\Payroll;

use Illuminate\Database\Eloquent\Model;

class PayGradeToDeduction extends Model
{
    protected $fillable = ['id', 'pay_grade_id','deduction_id'];
}
