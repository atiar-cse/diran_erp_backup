<?php

namespace App\Model\Accounts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountsBankPaymentVoucher extends Model
{
    use SoftDeletes;
    protected $softDelete = true;
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'payment_transaction_no',
        'payment_date',
        'type',
        'cost_center_id',
        'branch_id',
        'project_id',
        'total_debit_amount',
        'total_credit_amount',
        'narration',
        'approve_status',
        'created_by',
        'updated_by','approve_by'
    ];


    public function cost_center(){
        return $this->belongsTo(AccountsCostCenter::class,'cost_center_id');
    }

    public function project_info(){
        return $this->belongsTo(AccountsProjectInfo::class,'project_id');
    }
}
