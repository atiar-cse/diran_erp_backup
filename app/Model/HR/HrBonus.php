<?php

namespace App\Model\HR;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HrBonus extends Model
{
    protected $table = 'hr_bonus';
    use SoftDeletes;
    protected $softDelete = true;
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'title',
        'unit_id',
        'employee_type',
        'start_month',
        'end_month',
        'salary_type',
        'amount_percent',
        'bonus_percent',
        'effective_date',
        'status',
        'created_by',
        'updated_by'
    ];

    public function get_unit()
    {
        return $this->belongsTo(HrUnit::class, 'unit_id');
    }

    public function get_employee_type()
    {
        return $this->belongsTo(HrEmployeeType::class, 'employee_type');
    }
}
