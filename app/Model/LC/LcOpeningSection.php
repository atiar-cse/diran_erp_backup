<?php

namespace App\Model\LC;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Model\Purchase\PurchaseSupplierEntry;
use App\Model\LC\LcProformaInvoiceEntry;
use App\Model\Accounts\AccountsBankName;
use App\Model\LC\LcCfValueMarginEntry;

class LcOpeningSection extends Model
{
    use SoftDeletes;
    protected $softDelete = true;
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'lc_no',
        'supplier_id',
        'proforma_invoice_id',
        'lc_category',
        'lc_type',
        'lc_opening_date',
        'lc_opening_charges',
        'lc_opening_bank_id',
        'lc_bank_expenses',
        'baneficiary_bank',
        'lc_latest_shipment_date',
        'lc_expire_date',
        'lc_total_value',
        'insurance_amount',

        'status',
        'created_by',
        'updated_by',
        'approve_by'
    ];

    protected $guarded = [
        'lc_status',
        'approve_status',
        'account_status'
    ];


    public  function get_supplier(){
        return $this->belongsTo(PurchaseSupplierEntry::class,'supplier_id');
    }
    public  function get_pi_no(){
        return $this->belongsTo(LcProformaInvoiceEntry::class,'proforma_invoice_id')->with('get_work_order');
    } 
    public  function get_opening_bank(){
        return $this->belongsTo(AccountsBankName::class,'lc_opening_bank_id');
    }   
    public  function get_baneficiary_bank(){
        return $this->belongsTo(AccountsBankName::class,'baneficiary_bank');
    }  
    public  function get_cnf_margin(){
        return $this->hasOne(LcCfValueMarginEntry::class,'lc_opening_info_id');
    } 
}
