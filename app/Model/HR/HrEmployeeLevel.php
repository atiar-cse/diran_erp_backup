<?php

namespace App\Model\HR;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HrEmployeeLevel extends Model
{
    protected $table = 'hr_employee_level';

    use SoftDeletes;
    protected $softDelete = true;
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'employee_level_name',
        'order',
        'status',
        'created_by',
        'updated_by'
    ];
}
