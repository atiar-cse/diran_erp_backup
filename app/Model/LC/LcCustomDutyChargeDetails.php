<?php

namespace App\Model\LC;

use Illuminate\Database\Eloquent\Model;
use App\Model\Production\ProductionProducts;

class LcCustomDutyChargeDetails extends Model
{
    public $timestamps = false;
    protected $fillable = ['lc_custom_duty_entry_id','product_id','cost_value'];

    public  function get_product(){
        return $this->belongsTo(ProductionProducts::class,'product_id');
    }      
}
