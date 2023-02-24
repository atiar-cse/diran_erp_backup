<?php

namespace App\Model\HR;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HrDivision extends Model
{
    use SoftDeletes;
    protected $softDelete = true;
    protected $dates = ['deleted_at'];

    protected $fillable = ['unit_id','title','head_person','status','created_by','updated_by'];

    public function getPerson(){
        return $this->belongsTo(HrManageEmployee::class,'head_person');
    }
    public function getUnit(){
        return $this->belongsTo(HrUnit::class,'unit_id');
    }
}
