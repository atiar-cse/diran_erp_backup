<?php

namespace App\Model\Inventory;

use Illuminate\Database\Eloquent\Model;

class InventoryCurrentStock extends Model
{
    protected $fillable = [
        'inventory_current_stocks_warehouse_id',
        'inventory_current_stocks_product_id',
        'inventory_stocks_open_qty',
        'inventory_stocks_current_qty',

        'unit_price',
        'total_price',

        'status',
        'created_by',
        'updated_by'];
}
