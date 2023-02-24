<?php

namespace App\Model\LC;

use Illuminate\Database\Eloquent\Model;
use App\Model\LC\LcOpeningSection;
use App\Model\LC\LcCommercialInvoiceEntry;
use Illuminate\Database\Eloquent\SoftDeletes;

class LcLatrEntry extends Model
{
    use SoftDeletes;
    protected $softDelete = true;
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'lc_opening_info_id',
        'lc_commercial_invoice_id',
        'latr_date',
        'lc_margin_percentage',
        'bank_latr_no',
        'narration',
        'total_qty',
        'total_amount',
        'latr_percentage',

        'bank_percentage',
        'bank_percentage_amount',

        'latr_total_amount',

        'status',
        'approve_status',
        'account_status',

        'created_by',
        'updated_by','approve_by'];

    public  function get_lc_no(){
        return $this->belongsTo(LcOpeningSection::class,'lc_opening_info_id');
    } 
    public  function get_ci_no(){
        return $this->belongsTo(LcCommercialInvoiceEntry::class,'lc_commercial_invoice_id');
    }  
}
