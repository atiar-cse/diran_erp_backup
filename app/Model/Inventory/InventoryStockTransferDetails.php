<?php

namespace App\Model\Inventory;

use Illuminate\Database\Eloquent\Model;

class InventoryStockTransferDetails extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'transfer_id','product_id',
        'remarks',
        'unit','unit_price',
        'qty','total_price'
    ];



}
