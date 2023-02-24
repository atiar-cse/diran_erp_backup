<?php

namespace App\Model\Payroll;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PayrollBonusSetting extends Model
{
    use SoftDeletes;
    protected $softDelete = true;
    protected $dates = ['deleted_at'];

    protected $fillable = ['festival_name','percentage_of_bonus','bonus_type','status','created_by','updated_by'];
}
