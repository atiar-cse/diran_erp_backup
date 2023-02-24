<?php

namespace App\Model\Accounts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountsBankAccount extends Model
{
    use SoftDeletes;
    protected $softDelet = true;
    protected $dates = ['deleted_at'];

    protected $fillable = ['accounts_bank_id','accounts_branch_id','accounts_bank_accounts_name',
        'accounts_bank_accounts_number','accounts_bank_accounts_date','accounts_bank_accounts_contact_person',
        'accounts_bank_accounts_contact_number','chart_ac_id', 'status','created_by','updated_by'];

    public function coa(){
        return $this->belongsTo(AccountsChartofAccounts::class,'chart_ac_id');
    }

    public function get_accounts_bank_id(){
        return $this->belongsTo(AccountsBankName::class,'accounts_bank_id');
    }

    public function get_accounts_branch_id(){
        return $this->belongsTo(AccountsBankBranch::class,'accounts_branch_id');
    }
}
