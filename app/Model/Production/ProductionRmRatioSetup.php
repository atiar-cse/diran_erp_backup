<?php

namespace App\Model\Production;

use Illuminate\Database\Eloquent\Model;


class ProductionRmRatioSetup extends Model
{
    protected $fillable = ['product_id','thousand_kgs_weight','one_kg_weight','narration','created_by','updated_by'];


   
}
