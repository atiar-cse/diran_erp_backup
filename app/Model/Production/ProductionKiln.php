<?php

namespace App\Model\Production;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User;

class ProductionKiln extends Model
{
    use SoftDeletes;
    protected $softDelete = true;
    protected $dates = ['deleted_at'];

    protected $fillable = ['kiln_no','kiln_date','narration','total_receive_qty','total_trans_to_hv_qty','total_trans_weight','total_remain_qty','total_reject_qty',
        'total_reject_weight','overhead_info','reject_overhead_amt','reject_amt','total_amount','approve_status','account_status','status','created_by','updated_by','approve_by','acc_approve_by','acc_approve_at'];

    public function user_created_by(){
        return $this->belongsTo(User::class,'created_by');
    }

    public function user_updated_by(){
        return $this->belongsTo(User::class,'updated_by');
    }

}
