<?php

namespace App\Model\HR;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HrShiftManage extends Model
{
    protected $table = 'hr_shift_manage';
    use SoftDeletes;
    protected $softDelete = true;
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'shift_name',
        'shift_schedule_id',
        'status',
        'created_by',
        'updated_by'
    ];

    public function get_shift_schedule()
    {
        return $this->belongsTo(HrShiftSchedule::class, 'shift_schedule_id');
    }
}
