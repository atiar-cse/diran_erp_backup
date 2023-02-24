<?php

namespace App\Model\Production;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User;

class ProductionGlaze extends Model
{
    use SoftDeletes;
    protected $softDelete = true;
    protected $dates = ['deleted_at'];

    protected $fillable = ['glaze_no','glaze_date','narration','total_trans_to_kiln_qty','total_trans_to_kiln_weight',
        'total_waste_qty','total_waste_weight','total_receive_qty','total_remain_qty','overhead_info','glaze_material_price_per_kg','wastage_overhead_amt','total_amount','process_loss_percentage','process_loss_weight','weight_after_process_loss',
        'approve_status','account_status','status','created_by','updated_by','approve_by','acc_approve_by','acc_approve_at'];

}
