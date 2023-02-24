<?php

namespace App\Model\Accounts;

use App\Model\HR\HrManageEmployee;
use App\Model\LC\LcOpeningSection;
use App\Model\Purchase\PurchaseSupplierEntry;
use App\Model\Sales\SalesCustomer;
use Illuminate\Database\Eloquent\Model;

class AccountsCashPaymentDetailsVoucher extends Model
{
    public $timestamps = false;
    protected $fillable = ['cash_payment_id', 'type_id', 'type_desc', 'sub_code2_id', 'debit_account_id', 'credit_account_id', 'remarks', 'payment_person', 'debit_amount', 'credit_amount', 'voucher_type_id', 'voucher_ref_id'];

    public function get_debit(){
        return $this->belongsTo(AccountsChartofAccounts::class,'debit_account_id')->with('sub_code2');
    }

    public function get_credit(){
        return $this->belongsTo(AccountsChartofAccounts::class,'credit_account_id');
    }

    public function get_remarks(){

        return $this->belongsTo(AccountsCashPaymentVoucher::class,'cash_payment_id');
    }

    public function get_customer_code(){
        return $this->belongsTo(SalesCustomer::class,'type_id');
    }
    public function get_supplier_code(){
        return $this->belongsTo(PurchaseSupplierEntry::class,'type_id');
    }

    public function get_lc_code(){
        return $this->belongsTo(LcOpeningSection::class,'type_id');
    }
    public function get_employee_id(){
        return $this->belongsTo(HrManageEmployee::class,'type_id');
    }


}
