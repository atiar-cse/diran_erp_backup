<?php

namespace App\Model\Production;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User;

class ProductionMassbody extends Model
{
    use SoftDeletes;
    protected $softDelete = true;
    protected $dates = ['deleted_at'];

    protected $fillable = ['massbody_no','massbody_date','requisition_for_raw_material_id','narration',
        'green_chip_name','green_chip_percentage','green_chip_weight','green_chip_unit_price',

        'recycle_chip_name','recycle_chip_percentage','available_recycle_chip',
        'recycle_chip_weight','recycle_chip_unit_price','total_percentage','total_weight_qty',

        'status','created_by','updated_by','approve_by'];

    public  function requisition(){
        return $this->belongsTo(ProductionRequisitionForRm::class,'requisition_for_raw_material_id');
    }


}
