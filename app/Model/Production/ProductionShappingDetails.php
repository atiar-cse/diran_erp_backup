<?php

namespace App\Model\Production;

use Illuminate\Database\Eloquent\Model;

class ProductionShappingDetails extends Model
{
    public $timestamps = false;
    protected $fillable = ['shapping_section_id','product_id','remarks','shapping_product_weight',
        'current_qty','receive_qty','remain_qty','trans_to_dry_qty','trans_to_dry_weight',
        'dry_westage_qty','dry_westage_weight','unit_price','overhead_price','total_price'];

    public function product(){
        return $this->belongsTo(ProductionProducts::class,'product_id');
    }
}
