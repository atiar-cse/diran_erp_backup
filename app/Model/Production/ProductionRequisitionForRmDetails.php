<?php

namespace App\Model\Production;

use Illuminate\Database\Eloquent\Model;

class ProductionRequisitionForRmDetails extends Model
{

    public $timestamps = false;
    protected $fillable = ['requisition_for_rm_id','product_id','current_stock_qty','qty','unit_price','total_price','remarks',
        'created_at',
        'updated_at'
		];

    public function product(){
        return $this->belongsTo(ProductionProducts::class,'product_id');
    }
}
