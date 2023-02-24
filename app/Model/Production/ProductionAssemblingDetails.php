<?php

namespace App\Model\Production;

use Illuminate\Database\Eloquent\Model;

class ProductionAssemblingDetails extends Model
{
    public $timestamps = false;
    protected $fillable = ['assembling_section_id','product_id','remarks','current_qty','receive_qty','trans_to_packing_qty','unit_price','overhead_price','total_price','remain_qty'];

    public function product(){
        return $this->belongsTo(ProductionProducts::class,'product_id')->select('id','product_name');
    }

    public function assem_setup(){
        return $this->belongsTo(ProductionAssemblingSetupDetails::class,'product_id','fitting_product_id');
    }
}
