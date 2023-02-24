<?php

namespace App\Model\LC;

use Illuminate\Database\Eloquent\Model;
use App\Model\Production\ProductionProducts;
use App\Model\Production\ProductionMeasureUnit;
use App\Model\Currency;

class LcWorkOrderImportDetails extends Model
{
    public $timestamps = false;
    protected $fillable = ['work_order_import_id','product_id','remarks','currency_id','unit_price','qty','total_price','bdt_convert_rate','total_amount_taka'];

    public  function get_product(){
        return $this->belongsTo(ProductionProducts::class,'product_id');
    } 
    public  function get_currency(){
        return $this->belongsTo(Currency::class,'currency_id');
    }       
}
