<?php

namespace App\Model\Production;

use App\Model\Country;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductionProducts extends Model
{
    use SoftDeletes;
    protected $softDelete = true;
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'product_name',
        'product_code',
        'category_id',
        'brand_id',
        'measure_unit_id',
        'country_id',
        'credit_limit',
        'buying_price',
        'selling_price',
        'complete_product_weight',
        'forming_weight',
        'shapping_weight',
        'dryer_weight',
        'glaze_weight',
        'kiln_weight',
        'product_description',
        'is_raw_material_status',
        'status',
        'created_by',
        'updated_by'
    ];

    public  function category(){
        return $this->belongsTo(ProductionCategory::class,'category_id');
    }

    public  function brand(){
        return $this->belongsTo(ProductionBrand::class,'brand_id');
    }

    public  function measureunit(){
        return $this->belongsTo(ProductionMeasureUnit::class,'measure_unit_id');
    }

    public  function countryOfOrigin(){
        return $this->belongsTo(Country::class,'country_id');
    }
}
