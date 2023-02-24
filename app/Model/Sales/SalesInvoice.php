<?php

namespace App\Model\Sales;

use App\Model\Purchase\PurchaseWareHouse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesInvoice extends Model
{
    use SoftDeletes;
    protected $softDelet = true;
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'sales_invoices_no',
        'sales_invoices_contract_no',
        'sales_invoices_date',
        'sales_invoices_type',

        'sales_invoices_warehouse_id',
        'sales_invoices_customer_id',

        'sales_invoices_narration',
        'sales_invoices_total_qty',
        'sales_invoices_total_price',
        'approve_status',
        'challan_add_status',
        'status',
        'created_by',
        'updated_by','approve_by'];

    public  function get_customer(){
        return $this->belongsTo(SalesCustomer::class,'sales_invoices_customer_id')
            ->select('id','sales_customer_name');
    }

    public function get_wh(){
        return $this->belongsTo(PurchaseWareHouse::class,'sales_invoices_warehouse_id')
            ->select('purchase_ware_houses_name','id');
    }
}
