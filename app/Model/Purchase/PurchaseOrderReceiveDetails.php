<?php

namespace App\Model\Purchase;

use App\Model\Production\ProductionProducts;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderReceiveDetails extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'pod_order_id',
        'pod_product_id',
        'pod_remarks',
        'pod_unit',
        'pod_unit_price',
        'pod_qty',
        'pod_total_price'
    ];

    public function get_po(){
        return $this->belongsTo(PurchaseOrderReceive::class,'pod_order_id');
    }

    public function get_product(){
        return $this->belongsTo(ProductionProducts::class,'pod_product_id')->select('product_name','id');//
    }
}
