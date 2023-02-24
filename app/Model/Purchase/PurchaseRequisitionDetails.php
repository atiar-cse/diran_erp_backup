<?php

namespace App\Model\Purchase;

use App\Model\Production\ProductionProducts;
use Illuminate\Database\Eloquent\Model;

class PurchaseRequisitionDetails extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'pr_requisition_id',
        'pr_product_id',
        'pr_qty',
        'pr_unit',
        'pr_unit_price',
        'pr_total_price',
        'pr_remarks',
        'product_type'
    ];

    public function get_req(){
        return $this->belongsTo(PurchaseRequisition::class,'pr_requisition_id');
    }

    public function get_product(){
        return $this->belongsTo(ProductionProducts::class,'pr_product_id')->select('product_name','id');//->select('product_name')
    }
}
