<?php

namespace App\Model\Accounts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountsBillPaymentVoucher extends Model
{
    use SoftDeletes;
    protected $softDelete = true;
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'payment_transaction_no',
        'payment_date',
        'cost_center_id',
        'branch_id',
        'total_debit_amount',
        'total_credit_amount',
        'narration',
        'status',
        'created_by',
        'updated_by'
    ];


    public function cost_center(){
        return $this->belongsTo(AccountsCostCenter::class,'cost_center_id');
    }
}
