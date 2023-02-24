<?php

namespace App\Model\LC;

use Illuminate\Database\Eloquent\Model;
use App\Model\LC\LcOpeningSection;
use App\Model\Purchase\PurchaseSupplierEntry;

use Illuminate\Database\Eloquent\SoftDeletes;

class LcClosing extends Model
{
    use SoftDeletes;
    protected $softDelete = true;
    protected $dates = ['deleted_at'];
    protected $fillable = ['lc_opening_info_id','supplier_id','closing_date','narration','total_cost','landed_cost','status','created_by','updated_by','approve_by'];

    public  function get_lc_no(){
        return $this->belongsTo(LcOpeningSection::class,'lc_opening_info_id');
    }
    public  function get_supplier(){
        return $this->belongsTo(PurchaseSupplierEntry::class,'supplier_id');
    }
}
