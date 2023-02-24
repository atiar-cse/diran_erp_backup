<?php

namespace App\Model\LC;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LcVoucherType extends Model
{
    use SoftDeletes;
    protected $table = 'lc_voucher_type';
    protected $softDelete = true;
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'voucher_type_name',
        'menu_link_db_tbl',
        'transaction_column',
        'narration',
        'status',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];
}
