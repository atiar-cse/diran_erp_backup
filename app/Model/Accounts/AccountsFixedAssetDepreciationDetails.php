<?php

namespace App\Model\Accounts;

use Illuminate\Database\Eloquent\Model;

class AccountsFixedAssetDepreciationDetails extends Model
{
    protected $fillable = ['depreciation_id','sub_code2_id','chart_acc_id',
        'amount','depreciations','current_amount','percentage'];
}
