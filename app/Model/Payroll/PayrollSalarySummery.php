<?php

namespace App\Model\Payroll;

use App\Model\Accounts\AccountsChartofAccounts;
use Illuminate\Database\Eloquent\Model;

class PayrollSalarySummery extends Model
{
    protected  $fillable =[
        'id',
        'payroll_salary_month',
        'payable_total_salary',
        'debit_account_id',
        'credit_account_id',
        'acct_status',
    ];

    public function get_debit(){
        return $this->belongsTo(AccountsChartofAccounts::class,'debit_account_id');
    }

    public function get_credit(){
        return $this->belongsTo(AccountsChartofAccounts::class,'credit_account_id');
    }
}
