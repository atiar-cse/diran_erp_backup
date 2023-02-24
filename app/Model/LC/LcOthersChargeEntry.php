<?php

namespace App\Model\LC;

use App\Model\LC\LcCostNameCategory;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class LcOthersChargeEntry extends Model
{
    use SoftDeletes;
    protected $softDelete = true;
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'lc_others_charge_no',
        'lc_other_charge_id',
        'lc_opening_info_id',
        'lc_commercial_invoice_id',
        'cost_category_id',
        'others_charge_date',
        'total_cost',
        'narration',

        'status',

        'approve_status',
        'account_status',
        'created_by','updated_by','approve_by'];

    public  function get_lc_no(){
        return $this->belongsTo(LcOpeningSection::class,'lc_opening_info_id');
    }
    public  function get_ci_no(){
        return $this->belongsTo(LcCommercialInvoiceEntry::class,'lc_commercial_invoice_id');
    }
    public  function get_cost_cat(){
        return $this->belongsTo(LcCostNameCategory::class,'cost_category_id');
    }

    public  function charge_details(){
        return $this->belongsTo(LcOthersChargeDetails::class,'lc_other_charge_entry_id')->with('get_cost_name');
    }
    public  function get_details(){
        return $this->hasMany(LcOthersChargeDetails::class,'lc_other_charge_entry_id')->with('get_cost_name');
    }


}
