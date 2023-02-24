<?php

namespace App\Model\HR;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HrShiftSchedule extends Model
{
    protected $table = 'hr_shift_schedule';
    use SoftDeletes;
    protected $softDelete = true;
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'title',
        'in_start',
        'in_time',
        'late_start',
        'in_end',
        'out_start',
        'out_end',
        'status',
        'created_by',
        'updated_by'
    ];
}
