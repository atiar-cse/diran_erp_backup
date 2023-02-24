<?php

namespace App\Model\Payroll;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PayrollAllowance extends Model
{
    use SoftDeletes;
    protected $softDelete = true;
    protected $dates = ['deleted_at'];

    protected $fillable = ['allowance_name','allowance_type','percentage_of_basic','limit_per_month','status','created_by','updated_by'];
}
