<?php

namespace App\Model\LC;

use App\Model\Purchase\PurchaseSupplierEntry;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LcCustomDutyChargeEntry extends Model
{
    use SoftDeletes;
    protected $softDelete = true;
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'lc_custom_duty_no',
        'lc_opening_info_id',
        'lc_commercial_invoice_id',
        'custom_duty_date',
        'custom_duty_cost_id',
        'total_cost',
        'cnf_agent_id',
        'narration',
        'status',

        'approve_status',
        'account_status',
        'created_by',
        'updated_by',
        'approve_by',
        'acc_approve_by',
        'acc_updated_by'
    ];

    public  function get_lc_no(){
        return $this->belongsTo(LcOpeningSection::class,'lc_opening_info_id');
    }
    public  function get_ci_no(){
        return $this->belongsTo(LcCommercialInvoiceEntry::class,'lc_commercial_invoice_id');
    }
    public  function get_custom_duty(){
        return $this->belongsTo(LcCustomDutyCostNameEntry::class,'custom_duty_cost_id');
    }

    public  function get_details(){
        return $this->hasMany(LcCustomDutyChargeDetails::class,'lc_custom_duty_entry_id');
    }

    public  function get_cnf_agent(){
        return $this->belongsTo(PurchaseSupplierEntry::class,'cnf_agent_id');
    }
}
