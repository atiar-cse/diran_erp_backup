<?php

namespace App\Model\Production;

use Illuminate\Database\Eloquent\Model;

class ProductionKilnDetails extends Model
{
    public $timestamps = false;
    protected $fillable = ['kiln_section_id','product_id','remarks','kiln_weight','current_qty','receive_qty','remain_qty','trans_to_hv_qty','trans_to_hv_weight','reject_qty','reject_weight','unit_price','overhead_price','total_price'];

    public function product(){
        return $this->belongsTo(ProductionProducts::class,'product_id');
    }
}
