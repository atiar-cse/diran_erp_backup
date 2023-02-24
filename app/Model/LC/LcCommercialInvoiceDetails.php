<?php

namespace App\Model\LC;

use Illuminate\Database\Eloquent\Model;
use App\Model\Production\ProductionProducts;
use App\Model\Production\ProductionMeasureUnit;

class LcCommercialInvoiceDetails extends Model
{
    public $timestamps = false;
    protected $fillable = ['lc_commercial_invoice_id','product_id','hs_code','unit_price','qty','measure_unit_id','gross_weight','net_weight','total_amount','landed_unit_price'];

    public  function get_product(){
        return $this->belongsTo(ProductionProducts::class,'product_id');
    }    
    public  function get_measure_unit(){
        return $this->belongsTo(ProductionMeasureUnit::class,'measure_unit_id');
    }    
}
