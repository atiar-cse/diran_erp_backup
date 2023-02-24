<?php

namespace App\Model\Inventory;

use Illuminate\Database\Eloquent\Model;

class StockTransectionLogDetails extends Model
{
    protected $fillable = [
        'log_id',
        'is_in_out',
        'log_table_name',
        'log_table_id',

        'product_id',
        'warehouse_id',

        'log_open_qty',
        'log_entry_qty',
        'log_current_qty',
        'log_close_qty',

        'log_unit_price',
        'log_total_price',

        'add_sub',

        'entry_date',

        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];
}
