<?php

namespace App\Model\Purchase;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrderReceive extends Model
{
    use SoftDeletes;
    protected $softDelet = true;
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'po_order_no',

        'po_warehouse_id',

        'po_supplier_id',
        'po_requisition_id',

        'po_receive_date',
        'po_narration',
        'po_order_docs',
        'po_total_qty',
        'po_total_price',
        'approve_status',

        'account_status',

        /*'stock_enrty_status',*/
        'status',
        'created_by',
        'updated_by',
        'approve_by'];

    public function wh(){
        return $this->belongsTo(PurchaseWareHouse::class,'po_warehouse_id');
    }

    public function sup(){
        return $this->belongsTo(PurchaseSupplierEntry::class,'po_supplier_id');
    }
    public function get_supplier(){
        return $this->belongsTo(PurchaseSupplierEntry::class,'po_supplier_id');
    }

    public function req(){
        return $this->belongsTo(PurchaseRequisition::class,'po_requisition_id');
    }

}
