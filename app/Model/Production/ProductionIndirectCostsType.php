<?php

namespace App\Model\Production;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductionIndirectCostsType extends Model
{
    use SoftDeletes;
    protected $softDelete = true;
    protected $dates = ['deleted_at'];

    protected $fillable = ['id','indirect_cost_type','description','status','created_by','updated_by'];
}
