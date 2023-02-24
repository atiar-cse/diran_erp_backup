<?php

namespace App\Model\Purchase;

use Illuminate\Database\Eloquent\Model;

class PurchaseRelatedCostEntryDetails extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'po_cost_id',
        'po_cost_type_id',
        'remarks',
        'po_cost_amount'];

    public function get_type(){
        return $this->belongsTo(PurchaseCostType::class,'po_cost_type_id')
            ->select('purchase_cost_types_name','id');//
    }

}
