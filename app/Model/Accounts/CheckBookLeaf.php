<?php

namespace App\Model\Accounts;

use Illuminate\Database\Eloquent\Model;

class CheckBookLeaf extends Model
{
    protected $fillable = [
        'chart_ac_id',
        'account_no',
        'book_no',
        'prefix',
        'suffix',
        'cheque_no_length',
        'check_start',
        'check_range',
        'check_gen_date',
        'details',
        'approve_status',
        'status',
        'created_by',
        'updated_by',
        'approve_by'
    ];

    public function get_coa(){
        return $this->belongsTo(AccountsChartofAccounts::class,'chart_ac_id');
    }
}
