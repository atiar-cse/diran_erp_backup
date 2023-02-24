<?php

namespace App\Model\LC;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LcCostNameCategory extends Model
{
    use SoftDeletes;
    protected $softDelete = true;
    protected $dates = ['deleted_at'];
    protected $fillable = ['cost_category_name','status','created_by','updated_by'];

    public function get_cost_names() {
    	return $this->hasMany(LcCostNameEntry::class,'cost_category_id')->selectRaw('*, "0" as cost_value');
    }
}
