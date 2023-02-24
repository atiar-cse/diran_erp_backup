<?php

namespace App\Model\LC;

use Illuminate\Database\Eloquent\Model;
use App\Model\LC\LcCostNameEntry;

class LcOthersChargeDetails extends Model
{
    public $timestamps = false;
    protected $fillable = ['lc_other_charge_entry_id','lc_cost_name_id','cost_value'];

    public  function get_cost_name(){
        return $this->belongsTo(LcCostNameEntry::class,'lc_cost_name_id');
    }  

}
