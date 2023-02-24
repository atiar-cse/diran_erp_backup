<?php

namespace App\Model\Production;

use App\Model\Purchase\PurchaseWareHouse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductionAssembling extends Model
{
    use SoftDeletes;
    protected $softDelete = true;
    protected $dates = ['deleted_at'];

    protected $fillable = ['assembling_no','assembling_date','warehouse_id','assembling_types',
        'finishing_product_id','trans_to_packing_qty','unit_price','overhead_info','total_price','narration','total_rm_qty',
        'total_rm_price', 'inv_total_qty', 'inv_total_amount',
        'approve_status','status','created_by','updated_by','approve_by'];


    public  function finish_product(){
        return $this->belongsTo(ProductionProducts::class,'finishing_product_id');
    }


    public  function warehouse(){
        return $this->belongsTo(PurchaseWareHouse::class,'warehouse_id');
    }

}
