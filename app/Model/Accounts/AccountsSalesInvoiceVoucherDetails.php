<?php

namespace App\Model\Accounts;

use Illuminate\Database\Eloquent\Model;

class AccountsSalesInvoiceVoucherDetails extends Model
{
    public $timestamps = false;
    protected $fillable = ['ac_sales_voucher_id', 'product_id', 'remarks', 'm_unit', 'unit_price',
        'discount_type', 'discount', 'vat_sub', 'qty', 'total_price'];
}
