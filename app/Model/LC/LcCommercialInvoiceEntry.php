<?php

namespace App\Model\LC;

use Illuminate\Database\Eloquent\Model;
use App\Model\LC\LcOpeningSection;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Model\LC\LcLatrEntry;
use App\Model\LC\LcCnfAgent;
use App\Model\Country;
use App\Model\LC\LcCfValueMarginEntry;
use App\Model\LC\LcCustomDutyChargeEntry;
use App\Model\LC\LcOthersChargeEntry;

class LcCommercialInvoiceEntry extends Model
{
    use SoftDeletes;
    protected $softDelete = true;
    protected $dates = ['deleted_at'];
    protected $fillable = ['lc_opening_info_id','ci_no','ci_date','ci_bill_of_entry_no','ci_bill_entry_date','invoice_no','invoice_date','invoice_amount','eta_date','cnf_agent','shipping_mode','receive_date','exp_no','vassel_name','shipping_line','depart_from','arrived_port','present_destination','origin','narration','goods_name','total_qty','total_net_weight','total_gross_weight','total_amount','ci_landed_status','status','ci_delivery_type','ci_margin','ci_opening_charge','ci_bank_expenses','ci_insurance','created_by','updated_by','approve_by'];

    public  function get_lc_no(){
        return $this->belongsTo(LcOpeningSection::class,'lc_opening_info_id')->with('get_supplier','get_pi_no');
    }
    public  function get_cnf_agent(){
        return $this->belongsTo(LcCnfAgent::class,'cnf_agent');
    }
    public  function get_origin(){
        return $this->belongsTo(Country::class,'origin');
    }

    public  function get_latr_rows(){
        return $this->hasMany(LcLatrEntry::class,'lc_commercial_invoice_id');
    }
    // public  function get_margins(){
    //     return $this->hasMany(LcCfValueMarginEntry::class,'lc_opening_info_id','lc_opening_info_id');
    // }
    public  function get_custom_duty_charges(){
        return $this->hasMany(LcCustomDutyChargeEntry::class,'lc_commercial_invoice_id')->with('get_custom_duty');
    }
    public  function get_other_charges(){
        return $this->hasMany(LcOthersChargeEntry::class,'lc_commercial_invoice_id')->with('get_cost_cat');
    }    
}
