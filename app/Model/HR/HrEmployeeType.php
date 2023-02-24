<?php

namespace App\Model\HR;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HrEmployeeType extends Model
{
    protected $table = 'hr_employee_types';
    use SoftDeletes;
    protected $softDelete = true;
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'title',
        'status',
        'created_by',
        'updated_by'
    ];
}
