<?php

namespace App\Model\LC;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LcOthersCharge extends Model
{
    use SoftDeletes;
    protected $softDelete = true;
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'lc_others_charge_no',
        'lc_opening_info_id',
        'lc_commercial_invoice_id',

        'others_charge_date',
        'total_amount',
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
}
