<?php

namespace App\Model\HR;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HrLeaveType extends Model
{
    use SoftDeletes;
    protected $softDelet = true;
    protected $dates = ['deleted_at'];
    protected $fillable = ['employee_type_id','leave_type_name','number_of_day','status','created_by','updated_by'];

    public function getEmployeeType(){
        return $this->belongsTo(HrEmployeeType::class,'employee_type_id');
    }

    public function getLeaveTitle(){
        return $this->belongsTo(LeaveTitle::class,'leave_type_name');
    }


}
