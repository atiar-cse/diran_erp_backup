<?php

namespace App\Model\Payroll;

use Illuminate\Database\Eloquent\Model;

class PayrollBonusProcess extends Model
{
    protected  $fillable =[
        'id',
        'bonus_setting_id',
        'employee_id',
        'bonus_month',
        'gross_salary',
        'basic_salary',
        'festival_name',
        'bonus_type',
        'percentage_of_bonus',
        'bonus_amount',

    ];
}
