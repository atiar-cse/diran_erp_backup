<?php

namespace App\Model\Purchase;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User;

class PurchaseRequisition extends Model
{
    use SoftDeletes;
    protected $softDelet = true;
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'purchase_requisition_no',
        'purchase_requisition_date',
        'purchase_requisition_narration',
        'purchase_requisition_supervisor_narration',
        'purchase_requisition_total_qty',
        'purchase_requisition_total_price',
        'stock_enrty_status',
        'approve_status',
        'status',
        'created_by',
        'updated_by',
        'approve_by'];


}
