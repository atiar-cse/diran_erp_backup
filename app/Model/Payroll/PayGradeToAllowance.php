<?php

namespace App\Model\Payroll;

use Illuminate\Database\Eloquent\Model;

class PayGradeToAllowance extends Model
{
    protected $fillable = ['id', 'pay_grade_id','allowance_id'];
}
