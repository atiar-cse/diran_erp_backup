<?php

namespace App\Model\Production;

use Illuminate\Database\Eloquent\Model;

class ProductionDryerDetails extends Model
{
    public $timestamps = false;
    protected $fillable = ['dryer_section_id','product_id','remarks',

        'dryer_product_weight','current_qty','receive_qty',
        'remain_qty','trans_to_glaze_qty','trans_to_glaze_weight',
        'glaze_westage_qty','glaze_westage_weight','unit_price','overhead_price','total_price'];

    public function product(){
        return $this->belongsTo(ProductionProducts::class,'product_id');
    }
}
