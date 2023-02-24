<?php

namespace App\Model\Inventory;

use Illuminate\Database\Eloquent\Model;

class InventoryProductDamageDetails extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'inventory_product_damage_id',
        'inventory_product_damage_details_product_id',
        'inventory_product_damage_details_remarks',
        'inventory_product_damage_details_unit',
        'inventory_product_damage_details_qty',

        /*'inventory_product_damage_details_unit_price',
        'inventory_product_damage_details_total_price'*/
    ];


}
