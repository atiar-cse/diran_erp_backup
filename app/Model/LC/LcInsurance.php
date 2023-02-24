<?php

namespace App\Model\LC;

use Illuminate\Database\Eloquent\Model;

class LcInsurance extends Model
{
    protected $fillable = [
        'lc_insurance_no',
        'lc_opening_info_id',
        'insurance_company',
        'insurance_date',
        'insurance_no',
        'insurance_amount',
        'narration',
        'approve_status',
        'account_status',
        'created_by',
        'updated_by',
        'approve_by',
        'acc_approve_by'
    ];

    public  function get_lc_no(){
        return $this->belongsTo(LcOpeningSection::class,'lc_opening_info_id')
            ->with('get_supplier','get_pi_no');
    }
}
