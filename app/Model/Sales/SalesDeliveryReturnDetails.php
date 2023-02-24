<?php

namespace App\Model\Sales;

use App\Model\Production\ProductionProducts;
use Illuminate\Database\Eloquent\Model;

class SalesDeliveryReturnDetails extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'sales_delivery_return_id',
        'sales_delivery_return_details_product_id',
        'sales_delivery_return_details_remarks',
        'sales_delivery_return_details_unit',
        'sales_delivery_return_details_unit_price',
        'sales_delivery_return_details_qty',
        'sales_delivery_return_details_total_price'
    ];

    public function get_rtn(){
        return $this->belongsTo(SalesDeliveryReturn::class,'sales_delivery_return_id');
    }

    public function get_prod(){
        return $this->belongsTo(ProductionProducts::class,'sales_delivery_return_details_product_id');
    }
}
