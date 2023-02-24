<?php

namespace App\Model\Purchase;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseCostType extends Model
{
    use SoftDeletes;
    protected $softDelet = true;
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'purchase_cost_types_name',
        'cost_types_code',
        'chart_ac_id',
        'status',
        'created_by',
        'updated_by'];

}
