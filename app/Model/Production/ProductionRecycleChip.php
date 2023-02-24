<?php

namespace App\Model\Production;

use Illuminate\Database\Eloquent\Model;

class ProductionRecycleChip extends Model
{
    protected $fillable = [
        'recycle_chip_current_weight',
        'recycle_chip_unit_price',
        'recycle_chip_total_amt',
        
        'glaze_material_price_per_kg',
        
        'last_month_overhead_amt',
        'last_month_production_kg',
        'overhead_per_kg',

        'created_by',
        'updated_by'];
}
