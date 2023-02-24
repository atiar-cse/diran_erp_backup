<?php

namespace App\Model\Production;

use Illuminate\Database\Eloquent\Model;

class ProductionAssemblingSetupDetails extends Model
{
    public $timestamps = false;
    protected $fillable = ['production_assembling_setup_id','fitting_product_id','fitting_product_qty'];
}
