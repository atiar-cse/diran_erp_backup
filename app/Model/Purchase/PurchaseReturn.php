<?php

namespace App\Model\Purchase;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseReturn extends Model
{
    use SoftDeletes;
    protected $softDelet = true;
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'po_return_no','po_return_date','po_rtn_supplier_id',
        'po_rtn_warehouse_id', 'po_rtn_issue_type_id',
        'po_rtn_narration', 'po_rtn_docs','po_rtn_total_qty',
        'po_rtn_total_price', 'approve_status',
        'account_status','status','created_by','updated_by','approve_by'];

    public function wh(){
        return $this->belongsTo(PurchaseWareHouse::class,'po_rtn_warehouse_id');
    }

    public function sup(){
        return $this->belongsTo(PurchaseSupplierEntry::class,'po_rtn_supplier_id');
    }
    public function get_issue_type(){
        return $this->belongsTo(PurchaseReturnType::class,'po_rtn_issue_type_id');
    }
}
