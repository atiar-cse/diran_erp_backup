<?php

namespace App\Model\Sales;

use App\Model\Purchase\PurchaseWareHouse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesDeliveryChallan extends Model
{
    use SoftDeletes;
    protected $softDelet = true;
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'sales_delivery_no',
        'sales_delivery_date',
        'sales_invoice_id',
        'sales_customer_id',
        'sales_delivery_tender',
        'sales_warehouse_id',

        'sales_delivery_location',

        'sales_delivery_narration',
        'sales_delivery_total_qty',
        'sales_delivery_sub_total_price',

        'sales_delivery_ati',

        'sales_delivery_commission',
        'sales_delivery_discount',
        'sales_delivery_vat',
        'sales_delivery_vat_type',
        'sales_delivery_total_price',
        'approve_status',
        'account_status',
        'status',
        'created_by',
        'updated_by','approve_by'];


    public  function get_customer(){
        return $this->belongsTo(SalesCustomer::class,'sales_customer_id');
    }

    public function get_wh(){
        return $this->belongsTo(PurchaseWareHouse::class,'sales_warehouse_id')
            ->select('purchase_ware_houses_name','id');
    }

    public function get_inv(){
        return $this->belongsTo(SalesInvoice::class,'sales_invoice_id')
            ->select('sales_invoices_no','id');
    }

}
