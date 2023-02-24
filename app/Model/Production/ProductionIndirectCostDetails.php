<?php

namespace App\Model\Production;

use Illuminate\Database\Eloquent\Model;

class ProductionIndirectCostDetails extends Model
{
    public $timestamps = false;
    protected $fillable = ['indir_cost_id','prod_indir_costs_type_id','remarks', 'amount'];

    public function get_indirect_cost_type(){
    	return $this->belongsTo(ProductionIndirectCostsType::class,'prod_indir_costs_type_id');
    }
}
