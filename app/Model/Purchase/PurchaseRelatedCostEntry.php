<?php

namespace App\Model\Purchase;

use App\Model\Accounts\AccountsChartofAccounts;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseRelatedCostEntry extends Model
{
    use SoftDeletes;
    protected $softDelet = true;
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'po_order_receive_id',
        'po_cost_date',
        'po_cost_narration',
        'po_total_cost',
        'credit_ac_id',
        'status',
		'approve_status',
        'created_by',
        'updated_by',
        'approve_by'
    ];

    public function get_coa(){
        return $this->belongsTo(AccountsChartofAccounts::class,'credit_ac_id')
            ->select('chart_of_accounts_title');
    }
}
