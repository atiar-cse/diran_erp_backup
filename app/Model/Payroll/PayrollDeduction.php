<?php

namespace App\Model\Payroll;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PayrollDeduction extends Model
{
    use SoftDeletes;
    protected $softDelete = true;
    protected $dates = ['deleted_at'];

    protected $fillable = ['deduction_name','deduction_type','percentage_of_basic','limit_per_month','status','created_by','updated_by'];
}
