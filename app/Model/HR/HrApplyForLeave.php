<?php

namespace App\Model\HR;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HrApplyForLeave extends Model
{
    use SoftDeletes;
    protected $softDelete = true;
    protected $dates = ['deleted_at'];

    protected $fillable = ['employee_id','leave_type_id','application_from_date','application_to_date','number_of_day','purpose','check_status','status','created_by','updated_by'];

    public function employee(){
        return $this->belongsTo(HrManageEmployee::class,'employee_id');
    }
    public function leave_type(){
        return $this->belongsTo(HrLeaveType::class,'leave_type_id')->with('getLeaveTitle');
    }

}
