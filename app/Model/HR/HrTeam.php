<?php

namespace App\Model\HR;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HrTeam extends Model
{
    use SoftDeletes;
    protected $softDelete = true;
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'dept_id',
        'title',
        'head_person',
        'status',
        'created_by',
        'updated_by'
    ];

    public function getPerson()
    {
        return $this->belongsTo(HrManageEmployee::class, 'head_person');
    }
    public function getDept(){
        return $this->belongsTo(HrDepartment::class, 'dept_id');
    }
}
