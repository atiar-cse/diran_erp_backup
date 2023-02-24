<?php

namespace App\Model\Production;

use Illuminate\Database\Eloquent\Model;

class ProductionGlazeDetails extends Model
{
    public $timestamps = false;
    protected $fillable = ['glaze_section_id','product_id','remarks','glaze_weight','glaze_mat_weight','current_qty','receive_qty','remain_qty','trans_kiln_qty','trans_kiln_weight','waste_qty','waste_weight','unit_price','glaze_mat_price','overhead_price','total_price'];

    public function product(){
        return $this->belongsTo(ProductionProducts::class,'product_id');
    }
}
