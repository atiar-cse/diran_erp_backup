<?php

namespace App\Model\Accounts;

use Illuminate\Database\Eloquent\Model;

class AccountsSalesInvoiceReturnVoucherDetails extends Model
{
    public $timestamps = false;
    protected $fillable = ['ac_sales_return_voucher_id', 'product_id', 'remarks', 'm_unit', 'unit_price','qty', 'total_price'];
}
