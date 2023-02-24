<?php

namespace App\Model\Production;

use App\Model\Purchase\PurchaseWareHouse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;



class ProductionRequisitionForRm extends Model
{
    use SoftDeletes;
    protected $softDelete = true;
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'rm_requisition_no',
        'requisition_date',
        'requisition_types',
        'warehouse_id',
        'narration',
        'estimate_qty_for_production',
        'total_qty',
        'total_amount',
        'status',
        'created_by',
        'updated_by',
        'approve_by',
        'acc_approve_by'
        ];

    protected $guarded = [
        'approve_status',
        'account_status'];

    public  function warehouse(){
        return $this->belongsTo(PurchaseWareHouse::class,'warehouse_id');
    }

}
