<?php

namespace App\Model\Production;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductionIndirectCosts extends Model
{
    use SoftDeletes;
    protected $softDelete = true;
    protected $dates = ['deleted_at'];
    protected $guarded = ['approve_status','account_status'];

    protected $fillable = ['id','indir_no','date','total_amount','remarks','created_by','updated_by','approve_by','approve_at'];

}
