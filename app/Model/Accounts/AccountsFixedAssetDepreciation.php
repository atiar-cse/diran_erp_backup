<?php

namespace App\Model\Accounts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountsFixedAssetDepreciation extends Model
{
    use SoftDeletes;
    protected $softDelet = true;
    protected $dates = ['deleted_at'];

    protected $fillable = ['depreciations_no','depreciations_date','sub_code_id',
        'depreciations_narration','total_amount',
        'total_depreciations', 'total_current_amount','approve_status','account_status',

        'status','created_by','updated_by','approve_by'];

    public  function sub_code(){
        return $this->belongsTo(AccountsSubCode::class,'sub_code_id');
    }
}
