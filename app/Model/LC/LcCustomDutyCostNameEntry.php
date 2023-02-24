<?php

namespace App\Model\LC;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Model\Accounts\AccountsChartofAccounts;

class LcCustomDutyCostNameEntry extends Model
{
    use SoftDeletes;
    protected $softDelete = true;
    protected $dates = ['deleted_at'];
    protected $fillable = ['duty_cost_name','chart_ac_id','status','created_by','updated_by'];

    public  function get_chart_acc(){
        return $this->belongsTo(AccountsChartofAccounts::class,'chart_ac_id');
    }     
}
