<?php

namespace App\Model\Payroll;

use App\Model\Accounts\AccountsChartofAccounts;
use Illuminate\Database\Eloquent\Model;


class PayrollBonusSummery extends Model
{
    protected  $fillable =[
        'id',
        'payroll_bonus_month',
        'payable_total_bonus',
        'debit_account_id',
        'credit_account_id',
        'approve_status',

    ];

    public function get_debit(){
        return $this->belongsTo(AccountsChartofAccounts::class,'debit_account_id');
    }

    public function get_credit(){
        return $this->belongsTo(AccountsChartofAccounts::class,'credit_account_id');
    }
}
