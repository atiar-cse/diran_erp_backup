<?php

namespace App\Model\Inventory;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryProductDamage extends Model
{
    use SoftDeletes;
    protected $softDelet = true;
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'inventory_product_damages_no',
        'inventory_product_damages_date',
        'inventory_product_damages_warehouse_id',
        'inventory_product_damages_narration',
        //'inventory_product_damages_total_qty',

        //'inventory_product_damages_total_price',

        'approve_status', 'status',
        'created_by', 'updated_by','approve_by','approve_at'
    ];




}
