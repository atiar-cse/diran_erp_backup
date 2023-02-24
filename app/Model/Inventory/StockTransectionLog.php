<?php

namespace App\Model\Inventory;

use Illuminate\Database\Eloquent\Model;

class StockTransectionLog extends Model
{
    protected $fillable = [
        'stock_transection_logs_date',
        'is_in_out',
        'stock_transection_logs_type',
        'stock_transection_logs_number',
        'stock_transection_logs_ref_table_name',
        'stock_transection_logs_ref_table_id',

        'stock_transection_logs_table_name',
        'stock_transection_logs_table_id',

        'stock_trans_supp_cus_table_name',
        'stock_trans_supp_cus_table_id',
        'stock_trans_tender',

        'stock_trans_warehouse_id',
        'stock_trans_qty',
        'stock_trans_total_price',

        'created_at',
        'updated_at'
    ];

    /**
    '0-pr-entry,
     1-pr-return,
     2-delivery-challan,
     3 -delicery return
     4 - production rm
     5 - production testing
     6 - production assmble
     7 - production packing
     8 - Lc Stock entry
     9 - Inventory Stockentry
     10 - Inventory adjust
     11 - Inventory damage'

     **/

}
