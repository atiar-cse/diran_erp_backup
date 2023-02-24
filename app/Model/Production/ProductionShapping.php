<?php

namespace App\Model\Production;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User;

class ProductionShapping extends Model
{

    use SoftDeletes;
    protected $softDelete = true;
    protected $dates = ['deleted_at'];

    protected $fillable = ['shapping_no','shapping_date','narration','total_trans_to_dryer_qty',
        'total_trans_to_dryer_weight','total_receive_qty','total_remain_qty','total_waste_qty',
        'total_waste_weight','process_loss_percentage', 'process_loss_weight','weight_after_process_loss','total_amount','overhead_info','wastage_overhead_amt',
        'approve_status','status','created_by','updated_by','approve_by'];

}
