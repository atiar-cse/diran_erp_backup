<?php

namespace App\Model\Purchase;

use App\Model\Production\ProductionProducts;
use Illuminate\Database\Eloquent\Model;

class PurchaseReturnDetails extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'po_rtnd_return_id',
        'po_rtnd_product_id',
        'po_rtnd_remarks',
        'po_rtnd_messure_unit',
        'po_rtnd_unit_price',
        'po_rtnd_qty',
        'po_rtnd_total_price'];


    public function get_rtn(){
        return $this->belongsTo(PurchaseReturn::class,'po_rtnd_return_id');
    }

    public function get_product(){
        return $this->belongsTo(ProductionProducts::class,'po_rtnd_product_id');
    }

}
