<?php

namespace App\Model\Inventory;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User;

class InventoryStockTransfer extends Model
{
    use SoftDeletes;
    protected $softDelet = true;
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'transfers_no','transfers_date',
        'from_warehouse_id','to_warehouse_id','transfers_narration',
        'transfers_total_qty','transfers_total_price',
        'approve_status','status',
        'created_by','updated_by','approve_by',
        'approve_at',
    ];


}
