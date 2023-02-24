<?php

namespace App\Model\Production;

use Illuminate\Database\Eloquent\Model;

class ProductionPackingDetails extends Model
{
    public $timestamps = false;
    protected $fillable = ['packing_section_id','product_id','remarks',
        'unit_price','current_qty', 'receive_qty', 'used_qty', 'total_used_qty','overhead_price','total_price', 'remain_qty'];

    public function product(){
        return $this->belongsTo(ProductionProducts::class,'product_id');
    }
}
