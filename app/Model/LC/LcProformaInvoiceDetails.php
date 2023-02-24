<?php

namespace App\Model\LC;

use Illuminate\Database\Eloquent\Model;
use App\Model\Production\ProductionProducts;
use App\Model\Currency;

class LcProformaInvoiceDetails extends Model
{
    public $timestamps = false;
    protected $fillable = ['lc_proforma_invoice_id','product_id','remarks','currency_id','unit_price','qty','total_price','bdt_convert_rate','total_amount_taka'];

    public  function get_product(){
        return $this->belongsTo(ProductionProducts::class,'product_id');
    }
    public  function get_currency(){
        return $this->belongsTo(Currency::class,'currency_id');
    }

    public  function get_lc(){
        return $this->belongsTo(LcOpeningSection::class,'lc_proforma_invoice_id','proforma_invoice_id');
    }

}
