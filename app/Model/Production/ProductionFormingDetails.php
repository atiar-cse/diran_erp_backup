<?php

namespace App\Model\Production;

use Illuminate\Database\Eloquent\Model;

class ProductionFormingDetails extends Model
{
    public $timestamps = false;
    protected $fillable = ['forming_section_id','product_id','remarks','roll_weight',
        'current_qty','receive_qty','trans_to_shapping_qty','trans_to_shapping_weight',
        'forming_remain_qty','shapping_westage_qty','shapping_westage_weight','unit_price','overhead_price','total_price'];

    public function product(){
        return $this->belongsTo(ProductionProducts::class,'product_id');
    }
}
