<?php

namespace App\Model\Inventory;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryStockAdjust extends Model
{
    use SoftDeletes;
    protected $softDelet = true;
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'inventory_stock_adjusts_no',
        'inventory_stock_adjusts_date',
        'inventory_stock_adjusts_warehouse_id',
        'inventory_stock_adjusts_narration',

        //'inventory_stock_adjusts_total_qty',
        //'inventory_stock_adjusts_total_price',

        'approve_status', 'status',
        'created_by', 'updated_by','approve_by','approve_at'

        ];
}
