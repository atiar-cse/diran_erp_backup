<?php

namespace App\Model\Accounts;

use Illuminate\Database\Eloquent\Model;

class AccountsPurchaseOrderVoucherDetails extends Model
{
    public $timestamps = false;
    protected $fillable = ['ac_pruchase_stock_voucher_id', 'product_id', 'remarks', 'm_unit', 'unit_price', 'qty', 'total_price'];
}
