<?php

namespace App\Http\Controllers\Accounts\Reports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AccountsWiseLedgerReportsController extends Controller
{
    public function index()
    {
        return view('accounts.reports.accounts_wise_ledger_report');
    }
}
