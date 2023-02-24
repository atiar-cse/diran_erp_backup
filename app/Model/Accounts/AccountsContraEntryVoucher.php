<?php

namespace App\Model\Accounts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountsContraEntryVoucher extends Model
{
    use SoftDeletes;
    protected $softDelete = true;
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'contra_entry_no',
        'contra_date',

        'type',
        'type_id',
        'type_desc',

        'payment_method',
        'party_id',
        'cost_center_id',
        'branch_id',
        'amount',
        'debit_account_id',
        'debit_remarks',
        'credit_account_id',
        'credit_remarks',
        'bank_id',
        'cheque_date',
        'account_name',
        'account_no',
        'cheque_book',
        'cheque_leaf',
        'cheque_remarks',
        'narration',
        'voucher_type_id',
        'voucher_ref_id',

        'approve_status',
        'created_by',
        'updated_by',
        'approve_by'
    ];


    public function get_coa(){
        return $this->belongsTo(AccountsChartofAccounts::class,'account_no');
    }

    public function cost_center(){
        return $this->belongsTo(AccountsCostCenter::class,'cost_center_id');
    }

    public function debit_account(){
        return $this->belongsTo(AccountsChartofAccounts::class,'debit_account_id');
    }

    public function credit_account(){
        return $this->belongsTo(AccountsChartofAccounts::class,'credit_account_id');
    }

    public function get_journal_id(){
        return $this->belongsTo(AccountsJournalEntry::class,'transaction_reference_id','id');
    }

    public function get_bank_account_no(){
        return $this->belongsTo(AccountsBankAccount::class,'account_no');
    }

    public function get_check_leaf(){
        return $this->belongsTo(CheckBookLeafGenDetails::class,'cheque_leaf');
    }

}
