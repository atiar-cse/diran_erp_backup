<?php

namespace App\Model\Production;

use Illuminate\Database\Eloquent\Model;

class ProductionCurrentStock extends Model
{
    public $timestamps = false;
    protected $fillable = ['product_id',
        'forming_current_qty','shapping_current_qty','dryer_current_qty','glaze_current_qty',
        'kiln_current_qty','testing_current_qty','assembling_current_qty','packing_current_qty',

        'forming_receive_qty','shapping_receive_qty','dryer_receive_qty','glaze_receive_qty',
        'kiln_receive_qty','testing_receive_qty','assembling_receive_qty','packing_receive_qty',
        
        'finished_stk_current_qty','finished_stk_receive_qty'
        ];

}
