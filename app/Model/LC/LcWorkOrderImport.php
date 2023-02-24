<?php

namespace App\Model\LC;

use App\Model\Accounts\AccountsChartofAccounts;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Model\Purchase\PurchaseSupplierEntry;

class LcWorkOrderImport extends Model
{
    use SoftDeletes;
    protected $softDelete = true;
    protected $dates = ['deleted_at'];
    protected $fillable = ['work_order_no','supplier_id','coa_id','order_date','narration','total_qty','total_amount','total_amount_taka','status','created_by','updated_by','approve_by'];

    public  function get_supplier(){
        return $this->belongsTo(PurchaseSupplierEntry::class,'supplier_id');
    }

    public function get_coa(){
        return $this->belongsTo(AccountsChartofAccounts::class,'coa_id');
    }
}
