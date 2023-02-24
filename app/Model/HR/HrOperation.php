<?php

namespace App\Model\HR;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HrOperation extends Model
{
    use SoftDeletes;
    protected $softDelete = true;
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'unit_id',
        'operation_name',
        'status',
        'created_by',
        'updated_by'
    ];

    public function getUnit(){
        return $this->belongsTo(HrUnit::class,'unit_id');
    }
}
