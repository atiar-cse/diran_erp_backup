<?php

namespace App\Model\Accounts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountsYearEnd extends Model
{
    use SoftDeletes;
    protected $softDelete = true;
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'chart_account_id',
        'year_end_date',
        'closing_year',
        'closing_balance',
        'status',
        'created_by',
        'updated_by'
    ];

    public  function chart_of_acc_no(){
        return $this->belongsTo(AccountsChartofAccounts::class,'chart_account_id');
    }
}
