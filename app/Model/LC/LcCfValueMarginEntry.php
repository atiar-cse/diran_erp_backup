<?php

namespace App\Model\LC;

use App\Model\Accounts\AccountsBankName;
use App\Model\Purchase\PurchaseSupplierEntry;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LcCfValueMarginEntry extends Model
{
    use SoftDeletes;
    protected $softDelete = true;
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'lc_opening_info_id',
        'supplier_id',
        'margin_entry_date',
        'bank_id',
        'lc_value',
        'margin_percentage',
        'margin_value',
        'narration',
        'lc_margin_no',
        'status',
        'approve_status',
        'account_status',
        'created_by',
        'updated_by',
        'approve_by',
        'acc_approve_by',
        'acc_updated_by'];

    public  function get_lc_no(){
        return $this->belongsTo(LcOpeningSection::class,'lc_opening_info_id');
    }

    public  function get_supplier(){
        return $this->belongsTo(PurchaseSupplierEntry::class,'supplier_id');
    }

    public  function get_bank(){
        return $this->belongsTo(AccountsBankName::class,'bank_id');
    }
}
