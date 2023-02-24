<?php

namespace App\Model\Inventory;

use Illuminate\Database\Eloquent\Model;

class InventoryStockAdjustDetails extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'inventory_stock_adjust_id',
        'inventory_stock_adjust_details_product_id',
        'inventory_stock_adjust_details_remarks',
        'inventory_stock_adjust_adjust_rule',
        'inventory_stock_adjust_details_unit',

        //'inventory_stock_adjust_details_unit_price',
        'inventory_stock_adjust_details_qty',
        //'inventory_stock_adjust_details_total_price'
    ];
}
