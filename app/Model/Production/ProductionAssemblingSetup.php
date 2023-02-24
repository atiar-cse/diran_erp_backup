<?php

namespace App\Model\Production;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Model\Production\ProductionProducts;

class ProductionAssemblingSetup extends Model
{
    use SoftDeletes;
    protected $softDelete = true;
    protected $dates = ['deleted_at'];

    protected $fillable = ['finishing_product_id','finishing_product_qty','status','created_by','updated_by'];

    public function product(){
        return $this->belongsTo(ProductionProducts::class,'finishing_product_id');
    }
}
