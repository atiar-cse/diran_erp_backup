<?php

namespace App\Model\Sales;

use App\Model\Purchase\PurchaseReturnType;
use App\Model\Purchase\PurchaseWareHouse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesDeliveryReturn extends Model
{
    use SoftDeletes;
    protected $softDelet = true;
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'sales_delivery_return_no',
        'sales_delivery_return_date',
        'sales_delivery_return_warehouse_id',
        'sales_delivery_return_customer_id',
        'sales_delivery_return_return_types_id',
        'sales_delivery_return_narration',
        'sales_delivery_return_total_qty',
        'sales_delivery_return_total_price',
        'approve_status',
        'account_status',
        'status',
        'created_by',
        'updated_by','approve_by'];

    public function get_wh(){
        return $this->belongsTo(PurchaseWareHouse::class,'sales_delivery_return_warehouse_id');
    }

    public function get_customer(){
        return $this->belongsTo(SalesCustomer::class,'sales_delivery_return_customer_id');
    }

    public function get_issue_type(){
        return $this->belongsTo(PurchaseReturnType::class,'sales_delivery_return_return_types_id');
    }
}
