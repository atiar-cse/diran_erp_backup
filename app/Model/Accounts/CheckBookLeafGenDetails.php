<?php

namespace App\Model\Accounts;

use Illuminate\Database\Eloquent\Model;

class CheckBookLeafGenDetails extends Model
{
    protected $fillable = [
        'check_book_leaf_id','chart_ac_id','leaf_number','use_status'
    ];

    public function get_coa(){

        return $this->belongsTo(AccountsChartofAccounts::class,'chart_ac_id');
    }


}
