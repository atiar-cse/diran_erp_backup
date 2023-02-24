<?php

namespace App\Model\Production;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User;

class ProductionForming extends Model
{
    use SoftDeletes;
    protected $softDelete = true;
    protected $dates = ['deleted_at'];

    protected $fillable = ['forming_no','forming_date','forming_type','narration',
        'total_receive_qty','total_trans_to_shapping_qty','total_trans_to_shap_weight','total_remain_qty','total_waste_qty',
        'total_waste_weight','process_loss_percentage','process_loss_weight','weight_after_process_loss', 'total_amount','overhead_info','wastage_overhead_amt',
        'approve_status','status','created_by','updated_by','approve_by'];

}
