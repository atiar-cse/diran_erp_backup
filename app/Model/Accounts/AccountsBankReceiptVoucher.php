<?php

namespace App\Model\Accounts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountsBankReceiptVoucher extends Model
{
    use SoftDeletes;
    protected $softDelete = true;
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'receipt_transaction_no',
        'reciept_date',
        'cost_center_id',
        'type',
        'branch_id',
        'total_debit_amount',
        'total_credit_amount',
        'narration',
        'approve_status',
        'created_by',
        'updated_by',
        'approve_by'
    ];


    public function cost_center(){
        return $this->belongsTo(AccountsCostCenter::class,'cost_center_id');
    }
}
