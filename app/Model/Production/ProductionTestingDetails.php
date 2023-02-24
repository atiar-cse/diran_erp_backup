<?php

namespace App\Model\Production;

use Illuminate\Database\Eloquent\Model;

class ProductionTestingDetails extends Model
{
    public $timestamps = false;
    protected $fillable = ['testing_section_id','product_id','remarks','current_qty','receive_qty','adj_qty','trans_to_finishing','unit_price','overhead_price','total_price','remain_qty','reject_qty','kiln_weight'];

    public function product(){
        return $this->belongsTo(ProductionProducts::class,'product_id');
    }


}
