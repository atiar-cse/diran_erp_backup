<?php

namespace App\Model\Production;

use App\Model\Purchase\PurchaseWareHouse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User;

class ProductionTesting extends Model
{
    use SoftDeletes;
    protected $softDelete = true;
    protected $dates = ['deleted_at'];

    protected $fillable = ['testing_no','testing_date','warehouse_id','narration','total_receive_qty','total_adj_qty',
        'total_trans_to_store_qty','overhead_info','reject_overhead_amt','reject_amt','total_amount','total_remain_qty','total_reject_qty','approve_status','account_status','status','created_by','updated_by','approve_by','acc_approve_by', 'acc_approve_at'];

    public  function warehouse(){
        return $this->belongsTo(PurchaseWareHouse::class,'warehouse_id');
    }


}
