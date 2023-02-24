<?php

namespace App\Model\Accounts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountsSalesInvoiceVoucher extends Model
{
    use SoftDeletes;
    protected $softDelet = true;
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'vouchers_no','sales_invoice_id', 'sales_challan_id', 'sales_date', 'customer_id', 'tender_no',

        'dr_chart_of_account_id', 'cr_chart_of_account_id', 'cost_center_id',

        'narration',
		
		'payment_type', 'bank_id', 'cheque_date', 'account_name',

        'account_no', 'cheque_book', 'cheque_leaf', 'cheque_remarks',

        'total_qty', 'sub_total_price', 'commission', 'discount', 
		
		'vat', 'vat_type','total_price', 'price_paid', 'price_due',

		'approve_status','status', 'created_by', 'updated_by', 'approve_by'];

    public function get_coa(){
        return $this->belongsTo(AccountsChartofAccounts::class,'account_no');
    }
    
    public function get_bank_account_no(){
        return $this->belongsTo(AccountsBankAccount::class,'account_no');
    }

    public function get_check_leaf(){
        return $this->belongsTo(CheckBookLeafGenDetails::class,'cheque_leaf');
    }
}
