<?php

namespace App\Model\HR;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HrDepartment extends Model
{
    use SoftDeletes;
    protected $softDelete = true;
    protected $dates = ['deleted_at'];

    protected $fillable = ['div_id','department_name','head_person','status','created_by','updated_by'];

    public function getPerson(){
        return $this->belongsTo(HrManageEmployee::class,'head_person');
    }
    public function getDiv(){
        return $this->belongsTo(HrDivision::class,'div_id')->with('getUnit');
    }
}
