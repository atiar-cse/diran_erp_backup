<?php

namespace App\Model\Production;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User;

class ProductionDryer extends Model
{
    use SoftDeletes;
    protected $softDelete = true;
    protected $dates = ['deleted_at'];

    protected $fillable = ['dryer_no','dryer_date','narration',
        'total_receive_qty','total_remain_qty','total_waste_qty','total_waste_weight',
        'total_trans_to_glaze_qty','total_trans_to_glaze_weight','total_amount','overhead_info','wastage_overhead_amt',
        'approve_status','account_status','status','created_by','updated_by','approve_by','acc_approve_by','acc_approve_at'];

}
