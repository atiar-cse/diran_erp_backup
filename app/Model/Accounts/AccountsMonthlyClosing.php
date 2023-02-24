<?php

namespace App\Model\Accounts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountsMonthlyClosing extends Model
{
    use SoftDeletes;
    protected $softDelete = true;
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'date',
        'chart_of_account_id',
        'chart_of_account_code',
        'opening_balance',
        'current_balance',
        'closing_balance',
        'status',
        'created_by',
        'updated_by'
    ];

}
