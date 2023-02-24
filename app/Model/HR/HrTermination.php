<?php

namespace App\Model\HR;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HrTermination extends Model
{
    use SoftDeletes;
    protected $softDelete = true;
    protected $dates = ['deleted_at'];

    protected $fillable = ['employee_terminated_employee_id','termination_types','subject','terminated_by_employee_id','notice_date','termination_date','narration','status','created_by','updated_by'];

    public  function employee_terminated(){
        return $this->belongsTo(HrManageEmployee::class,'employee_terminated_employee_id');
    }
    public  function terminated_by_employee(){
        return $this->belongsTo(HrManageEmployee::class,'terminated_by_employee_id');
    }
}
