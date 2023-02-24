<?php

namespace App\Model\Accounts;

use App\Model\HR\HrManageEmployee;
use App\Model\LC\LcOpeningSection;
use App\Model\Purchase\PurchaseSupplierEntry;
use App\Model\Sales\SalesCustomer;
use Illuminate\Database\Eloquent\Model;

class AccountsBankReceiptDetailsVoucher extends Model
{
    public $timestamps = false;
    protected $fillable = ['bank_receipt_id','sub_code2_id','type_id','type_desc','debit_account_id','credit_account_id','debit_amount','credit_amount','remarks','payment_person','cheque_no','cheque_date'];

    public function get_debit(){
        return $this->belongsTo(AccountsChartofAccounts::class,'debit_account_id')->with('sub_code2');
    }

    public function get_credit(){
        return $this->belongsTo(AccountsChartofAccounts::class,'credit_account_id')->with('sub_code2');
    }

    public function get_remarks(){

        return $this->belongsTo(AccountsBankReceiptVoucher::class,'bank_receipt_id');
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
