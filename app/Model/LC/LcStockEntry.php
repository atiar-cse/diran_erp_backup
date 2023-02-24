<?php

namespace App\Model\LC;

use Illuminate\Database\Eloquent\Model;
use App\Model\LC\LcOpeningSection;
use App\Model\LC\LcCommercialInvoiceEntry;
use App\Model\Purchase\PurchaseWareHouse;
use App\Model\Purchase\PurchaseSupplierEntry;
use Illuminate\Database\Eloquent\SoftDeletes;

class LcStockEntry extends Model
{
    use SoftDeletes;
    protected $softDelete = true;
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'lc_opening_info_id',
        'lc_commercial_invoice_id',
        'warehouse_id',
        'supplier_id'
        ,'entry_date',
        'narration',
        'total_qty',
        'total_net_weight',
        'total_gross_weight',
        'total_amount',
        'status',
        'created_by',
        'updated_by','approve_by'];

    protected $guarded = [
        'approve_status',
        'account_status'];
    /*
     * These two filled should be filled by object  for security purpuse
    'approve_status'
	'account_status'
    */

    public  function get_lc_no(){
        return $this->belongsTo(LcOpeningSection::class,'lc_opening_info_id');
    }
    public  function get_ci_no(){
        return $this->belongsTo(LcCommercialInvoiceEntry::class,'lc_commercial_invoice_id');
    } 
    public  function get_warehouse(){
        return $this->belongsTo(PurchaseWareHouse::class,'warehouse_id');
    } 
    public  function get_supplier(){
        return $this->belongsTo(PurchaseSupplierEntry::class,'supplier_id');
    } 
}
