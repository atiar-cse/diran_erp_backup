<?php

namespace App\Model\Sales;

use App\Model\Production\ProductionProducts;
use Illuminate\Database\Eloquent\Model;

class SalesDeliveryChallanDetails extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'sales_delivery_id',
        'sales_delivery_details_product_id',
        'sales_delivery_details_description',
        'sales_delivery_details_unit',
        'sales_delivery_details_unit_price',
        'sales_delivery_details_discount_type',
        'sales_delivery_details_discount',
        'sales_delivery_details_vat_sub',
        'sales_delivery_details_order_qty',
        'sales_delivery_details_qty',
        'sales_delivery_details_total_price'
    ];

    public function get_chln(){
        return $this->belongsTo(SalesDeliveryChallan::class,'sales_delivery_id');
    }

    public function get_prod(){
        return $this->belongsTo(ProductionProducts::class,'sales_delivery_details_product_id');
    }
}
