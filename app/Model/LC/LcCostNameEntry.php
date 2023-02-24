<?php

namespace App\Model\LC;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Model\LC\LcCostNameCategory;
use App\Model\Accounts\AccountsChartofAccounts;

class LcCostNameEntry extends Model
{
    use SoftDeletes;
    protected $softDelete = true;
    protected $dates = ['deleted_at'];
    protected $fillable = ['cost_name','cost_category_id','chart_ac_id','status','created_by','updated_by']; 

    public  function get_category(){
        return $this->belongsTo(LcCostNameCategory::class,'cost_category_id');
    } 
    public  function get_chart_acc(){
        return $this->belongsTo(AccountsChartofAccounts::class,'chart_ac_id');
    }        
}