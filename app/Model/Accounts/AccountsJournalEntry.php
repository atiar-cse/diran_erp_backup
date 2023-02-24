<?php

namespace App\Model\Accounts;

use App\Model\Sales\SalesCustomer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountsJournalEntry extends Model
{
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'transaction_reference_id',
        'transaction_reference_no',
        'transaction_date',
        'type',
        'transaction_type',
        'transaction_type_name',
        'party_id',
        'branch_id',
        'cost_center_id',
        'bank_id',
        'bank_reference',
        'total_debit',
        'total_credit',
        'narration',
        'approve_status',
        'created_by',
        'updated_by',
        'approve_by'
    ];


    public function cost_center(){
        return $this->belongsTo(AccountsCostCenter::class,'cost_center_id');
    }
    public function get_jrnl_detail(){
        return $this->hasOne(AccountsJournalEntryDetails::class,'journal_entry_id');
    }
    public function details(){
        return $this->hasMany(AccountsJournalEntryDetails::class,'journal_entry_id')->with('coa')->selectRaw('accounts_journal_entry_details.*,"" as current_balance');
    }

    public function getPurchaseInvVoucer(){
        return $this->belongsTo(AccountsPurchaseOrderVoucher::class,'transaction_reference_id')->select('id','vouchers_no');
    }

    public function cash_payment_debit_check($journal_entry_id, $char_of_account_id,$row_id){
        $response = AccountsJournalEntryDetails::where('journal_entry_id',$journal_entry_id)
                            ->where('char_of_account_id',$char_of_account_id)
                            ->where('debit_amount','>',0)
                            ->where('id',$row_id)
                            ->First();
        return $response;
    }
    public function cash_payment_credit_row(){
        return $this->hasOne(AccountsJournalEntryDetails::class,'journal_entry_id')->where('debit_amount','=',0)->with('coa');
    }

    public function cash_receipt_credit_check($journal_entry_id, $char_of_account_id){
        $response = AccountsJournalEntryDetails::where('journal_entry_id',$journal_entry_id)
            ->where('char_of_account_id',$char_of_account_id)
            ->where('credit_amount','>',0)
            ->First();
        return $response;
    }
    public function cash_receipt_debit_row(){
        return $this->hasOne(AccountsJournalEntryDetails::class,'journal_entry_id')->where('credit_amount','=',0)->with('coa');
    }

    public  function get_customer(){
        return $this->belongsTo(SalesCustomer::class,'sales_invoices_customer_id')
            ->select('id','sales_customer_name');
    }

}
