<?php

namespace App\Model\Purchase;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseWareHouse extends Model
{
    use SoftDeletes;
    protected $softDelet = true;
    protected $dates = ['deleted_at'];

    protected $fillable = ['purchase_ware_houses_name','purchase_ware_houses_contact_person_name',
        'purchase_ware_houses_mobile', 'purchase_ware_houses_address','status','created_by','updated_by'];
}
