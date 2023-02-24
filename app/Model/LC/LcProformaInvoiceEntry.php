<?php

namespace App\Model\LC;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Model\Purchase\PurchaseSupplierEntry;
use App\Model\LC\LcWorkOrderImport;
use App\Model\Country;

class LcProformaInvoiceEntry extends Model
{
    use SoftDeletes;
    protected $softDelete = true;
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'pi_no',
        'work_order_id',
        'supplier_id',
        'usd_account_no',
        'pi_date',
        'consignee',
        'swift_code',
        'terms_of_loading',
        'port_of_discharger',
        'delivery_time',
        'terms_of_delivery',
        'terms_of_payment',
        'origin_of_goods',
        'final_destination',
        'narration',
        'total_qty',
        'total_amount',
        'total_amount_taka',
        'approve_status',
        'status',
        'created_by',
        'updated_by' ,'approve_by'];

    public  function get_supplier(){
        return $this->belongsTo(PurchaseSupplierEntry::class,'supplier_id');
    } 
    public  function get_work_order(){
        return $this->belongsTo(LcWorkOrderImport::class,'work_order_id');
    } 
    public  function get_country(){
        return $this->belongsTo(Country::class,'origin_of_goods');
    }     
}
