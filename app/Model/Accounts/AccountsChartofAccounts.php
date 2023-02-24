<?php

namespace App\Model\Accounts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountsChartofAccounts extends Model
{
    use SoftDeletes;
    protected $softDelete = true;
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'main_code_id',
        'sub_code_id',
        'sub_code2_id',
        'branch_id',
        'chart_of_accounts_title',
        'chart_of_account_code',
        'accounts_status',
        'current_balance',
        'opening_date',
        'opening_balance',
        'closing_date',
        'closing_balance',

        'open_bl_add_status',
        'status',
        'narration',
        'created_by',
        'updated_by',

        'voucher_type_id',
        'voucher_ref_id',
    ];

    public function sub_code2()
    {
        return $this->belongsTo(AccountsSubCode2::class, 'sub_code2_id');
    }


    /*   public function sub_code2_title(){
           return $this->belongsTo(AccountsSubCode2::class,'sub_code2_id','id');
       }*/
}
