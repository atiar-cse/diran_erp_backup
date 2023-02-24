<?php

namespace App\Model\HR;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HrDesignation extends Model
{
    use SoftDeletes;
    protected $softDelete = true;
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'designation_name',
        'employee_level_id',
        'status',
        'created_by',
        'updated_by'
    ];

    public function getEmployeeLevel()
    {
        return $this->belongsTo(HrEmployeeLevel::class, 'employee_level_id');
    }
}
