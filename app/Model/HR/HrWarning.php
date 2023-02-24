<?php

namespace App\Model\HR;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HrWarning extends Model
{
    use SoftDeletes;
    protected $softDelete = true;
    protected $dates = ['deleted_at'];

    protected $fillable = ['warning_to_employee_id','warning_types','subject','warning_by_employee_id','warning_date','narration','status','created_by','updated_by'];

    public  function warning_to_employee(){
        return $this->belongsTo(HrManageEmployee::class,'warning_to_employee_id');
    }
    public  function warning_by_employee(){
        return $this->belongsTo(HrManageEmployee::class,'warning_by_employee_id');
    }

}
