<?php

namespace App\Model\HR;

use Illuminate\Database\Eloquent\Model;

class HrManualAttendanceEntry extends Model
{
    protected $fillable = [
        'employee_attendance_employee_id',
        'employee_attendance_department_id',
        'employee_shift_id',
        'emp_attendance_date',
        'start_time',
        'end_time',
        'status',
        'created_by',
        'updated_by'
    ];
}
