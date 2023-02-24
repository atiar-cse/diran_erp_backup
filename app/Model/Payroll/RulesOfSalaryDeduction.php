<?php

namespace App\Model\Payroll;

use Illuminate\Database\Eloquent\Model;

class RulesOfSalaryDeduction extends Model
{
    protected $table ='rules_of_salary_deductions';
    protected $fillable = ['id','for_days','day_of_salary_deduction','status'];
}
