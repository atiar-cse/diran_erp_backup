<?php

namespace App\Model\Production;

use App\Model\Purchase\PurchaseWareHouse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductionPacking extends Model
{
    use SoftDeletes;
    protected $softDelete = true;
    protected $dates = ['deleted_at'];

    protected $fillable = ['packing_no','packing_date','warehouse_id','packing_types','finishing_product_id',
        'trans_to_store_qty','unit_price','overhead_info','reject_overhead_amt','reject_amt','total_price','narration','total_rm_qty','total_rm_price',
        'approve_status','status','created_by','updated_by','approve_by'];


    public  function finish_product(){
        return $this->belongsTo(ProductionProducts::class,'finishing_product_id');
    }

    public  function warehouse(){
        return $this->belongsTo(PurchaseWareHouse::class,'warehouse_id');
    }
}
