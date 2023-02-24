<?php

Route::group(['middleware' => ['preventbackbutton','auth']], function(){

    Route::group(['prefix' => 'bank'], function () {
        Route::get('/',['as' => 'bank.index', 'uses'=>'Accounts\BasicSetup\BankNameController@index']);
        Route::get('/create',['as' => 'bank.create', 'uses'=>'Accounts\BasicSetup\BankNameController@create']);
        Route::post('/store',['as' => 'bank.store', 'uses'=>'Accounts\BasicSetup\BankNameController@store']);
        Route::get('/{id}',['as'=> 'bank.show','uses'=>'Accounts\BasicSetup\BankNameController@show']);
        Route::get('/{id}/edit',['as'=> 'bank.edit','uses'=>'Accounts\BasicSetup\BankNameController@edit']);
        Route::put('/{id}/update',['as' => 'bank.update', 'uses'=>'Accounts\BasicSetup\BankNameController@update']);
        Route::delete('/{id}/delete',['as'=> 'bank.delete','uses'=>'Accounts\BasicSetup\BankNameController@destroy']);
    });


    Route::group(['prefix' => 'bank-branch'], function () {
        Route::get('/',['as' => 'bank-branch.index', 'uses'=>'Accounts\BasicSetup\BankBranchController@index']);
        Route::get('/create',['as' => 'bank-branch.create', 'uses'=>'Accounts\BasicSetup\BankBranchController@create']);
        Route::post('/store',['as' => 'bank-branch.store', 'uses'=>'Accounts\BasicSetup\BankBranchController@store']);
        Route::get('/{id}',['as'=> 'bank-branch.show','uses'=>'Accounts\BasicSetup\BankBranchController@show']);
        Route::get('/{id}/edit',['as'=> 'bank-branch.edit','uses'=>'Accounts\BasicSetup\BankBranchController@edit']);
        Route::put('/{id}/update',['as' => 'bank-branch.update', 'uses'=>'Accounts\BasicSetup\BankBranchController@update']);
        Route::delete('/{id}/delete',['as'=> 'bank-branch.delete','uses'=>'Accounts\BasicSetup\BankBranchController@destroy']);
    });

    Route::group(['prefix' => 'bank-account'], function () {
        Route::get('/',['as' => 'bank-account.index', 'uses'=>'Accounts\BasicSetup\BankAccountController@index']);
        Route::get('/create',['as' => 'bank-account.create', 'uses'=>'Accounts\BasicSetup\BankAccountController@create']);
        Route::post('/store',['as' => 'bank-account.store', 'uses'=>'Accounts\BasicSetup\BankAccountController@store']);
        Route::get('/{id}',['as'=> 'bank-account.show','uses'=>'Accounts\BasicSetup\BankAccountController@show']);
        Route::get('/{id}/edit',['as'=> 'bank-account.edit','uses'=>'Accounts\BasicSetup\BankAccountController@edit']);
        Route::put('/{id}/update',['as' => 'bank-account.update', 'uses'=>'Accounts\BasicSetup\BankAccountController@update']);
        Route::delete('/{id}/delete',['as'=> 'bank-account.delete','uses'=>'Accounts\BasicSetup\BankAccountController@destroy']);
    });

    Route::get('bank-wise-branch', 'Accounts\BasicSetup\BankAccountController@getBankWiseBankBranch');

    Route::group(['prefix' => 'cost-centre'], function () {
        Route::get('/',['as' => 'cost-centre.index', 'uses'=>'Accounts\BasicSetup\CostCenterController@index']);
        Route::get('/create',['as' => 'cost-centre.create', 'uses'=>'Accounts\BasicSetup\CostCenterController@create']);
        Route::post('/store',['as' => 'cost-centre.store', 'uses'=>'Accounts\BasicSetup\CostCenterController@store']);
        Route::get('/{id}',['as'=> 'cost-centre.show','uses'=>'Accounts\BasicSetup\CostCenterController@show']);
        Route::get('/{id}/edit',['as'=> 'cost-centre.edit','uses'=>'Accounts\BasicSetup\CostCenterController@edit']);
        Route::put('/{id}/update',['as' => 'cost-centre.update', 'uses'=>'Accounts\BasicSetup\CostCenterController@update']);
        Route::delete('/{id}/delete',['as'=> 'cost-centre.delete','uses'=>'Accounts\BasicSetup\CostCenterController@destroy']);
    });

    Route::resource('check-book-leaf','Accounts\BasicSetup\CheckBookLeafController',['parameters'=> ['check-book-leaf'=>'id']]);
    Route::resource('project-info','Accounts\BasicSetup\ProjectInfoController',['parameters'=> ['project-info'=>'id']]);
    //Route::post('check-book-leaf',['as'=>'check-book-leaf.approve','uses'=>'Accounts\BasicSetup\CheckBookLeafController@approve']);



    Route::resource('account-main-code','Accounts\ChartOfAccounts\AccountMainCodeController',['parameters'=> ['account-main-code'=>'id']]);
    Route::resource('account-sub-code','Accounts\ChartOfAccounts\AccountSubCodeController',['parameters'=> ['account-sub-code'=>'id']]);
    Route::resource('account-sub-code2','Accounts\ChartOfAccounts\AccountSubCode2Controller',['parameters'=> ['account-sub-code2'=>'id']]);
    Route::resource('chart-of-accounts','Accounts\ChartOfAccounts\ChartOfAccountsController',['parameters'=> ['chart-of-accounts'=>'id']]);
    Route::get('acc-chart-code', 'Accounts\ChartOfAccounts\ChartOfAccountsController@chart_code');
    Route::get('print-chart-of-acct','Accounts\ChartOfAccounts\ChartOfAccountsController@getPrintChartOfAccount');

    Route::resource('tax-rules','Accounts\BasicSetup\TaxRulesController',['parameters'=> ['tax-rules'=>'id']]);
    Route::resource('cost-centre','Accounts\BasicSetup\CostCenterController',['parameters'=> ['cost-centre'=>'id']]);




    /*Lc Related Voucher  Start*/
    Route::resource('acc-lc-opening-section','Accounts\Lc\LcOpeningVoucherController',['parameters'=> ['acc-lc-opening-section'=>'id']]);
    Route::post('acc-lc-opening-section/add-lc-charge','Accounts\Lc\LcOpeningVoucherController@AddLcCharge');

    Route::resource('acc-lc-insurance','Accounts\Lc\LcInsuranceVoucherController');

    Route::resource('acc-lc-cf-value-margin-entry','Accounts\Lc\LcMarginVoucherController',['parameters'=> ['acc-lc-cf-value-margin-entry'=>'id']]);

    Route::resource('acc-latr-entry','Accounts\Lc\LcLatrVoucherController',['parameters'=> ['acc-latr-entry'=>'id']]);
    Route::get('acc-latr-entry/{id}/get-commercial-invoices-by-lcno','Accounts\Lc\LcLatrVoucherController@getCommercialInvoicesByLcNo',['parameters'=> ['commercial-invoice-entry'=>'id']]);

    Route::resource('acc-custom-duty-charge-entry','Accounts\Lc\LcCustomVoucherController',['parameters'=> ['acc-custom-duty-charge-entry'=>'id']]);

    Route::resource('acc-others-charge-entry','Accounts\Lc\LcOtherVoucherController',['parameters'=> ['acc-others-charge-entry'=>'id']]);

    Route::resource('acc-lc-stock-entry','Accounts\Lc\LcStockEntryVoucherController',['parameters'=> ['acc-lc-stock-entry'=>'id']]);


    /*Lc Related voucher End*/







    Route::resource('bank-payment-voucher','Accounts\BankPaymentVoucherController',['parameters'=> ['bank-payment-voucher'=>'id']]);
    Route::get('/sbc2-wise-coa/{id}',['as' => 'sbc2-wise-coa.coa_list', 'uses'=>'Accounts\BankPaymentVoucherController@getSubCode2WiseCoaLists']);
    Route::get('/coa-wise-check-leaf/{id}',['as' => 'coa-wise-check-leaf.leaf_list', 'uses'=>'Accounts\BankPaymentVoucherController@getCheckLeafLists']);

    Route::resource('bank-receipt-voucher','Accounts\BankReceiptVoucherController',['parameters'=> ['bank-receipt-voucher'=>'id']]);


    /*Route::resource('bank-reconciliation','Accounts\BankReconciliationController',['parameters'=> ['bank-reconciliation'=>'id']]);*/
    Route::resource('bank-transfer-deposit','Accounts\BankTransferDepoditController',['parameters'=> ['bank-transfer-deposit'=>'id']]);
        Route::get('get_account_type_list/{type}', 'Accounts\BankTransferDepoditController@getAccountTypeList');
        Route::get('bank-transfer-bank-wise-account-no','Accounts\BankTransferDepoditController@getAccountNumbList');
        Route::get('bank-transfer-acct-num-wise-check-leaf-list','Accounts\BankTransferDepoditController@getBankCheckLeafList');

    Route::resource('bill-paymnt-voucher','Accounts\BillPaymentVoucherController',['parameters'=> ['bill-paymnt-voucher'=>'id']]);


    Route::resource('cash-payment-voucher','Accounts\CashPaymentVoucherController',['parameters'=> ['cash-payment-voucher'=>'id']]);
        Route::get('get_account_type_list/{type}', 'Accounts\CashPaymentVoucherController@getAccountTypeList');
        Route::post('store-project','Accounts\BasicSetup\ProjectInfoController@store_project');



    Route::resource('cash-receipt-voucher','Accounts\CashReceiptVoucherController',['parameters'=> ['cash-receipt-voucher'=>'id']]);

    Route::resource('contra-entry-voucher','Accounts\ContraEntryVoucherController',['parameters'=> ['contra-entry-voucher'=>'id']]);
        Route::get('get_account_type_list/{type}', 'Accounts\ContraEntryVoucherController@getAccountTypeList');

    Route::get('bank-wise-account-no','Accounts\ContraEntryVoucherController@getAcctNumbList');
    Route::get('acct-num-wise-check-leaf-list','Accounts\ContraEntryVoucherController@getCheckLeafList');

    Route::resource('fixed-asset-depreciation','Accounts\FixedAssetDepreciationController',['parameters'=> ['fixed-asset-depreciation'=>'id']]);
    Route::get('sl-no', 'Accounts\FixedAssetDepreciationController@SlGenerate');
    Route::get('fixed-asset-depreciation/{id}/chart-list','Accounts\FixedAssetDepreciationController@chartlist');


    Route::resource('journal-voucher','Accounts\JournalVoucherController',['parameters'=> ['journal-voucher'=>'id']]);
        Route::get('get_account_type_list/{type}', 'Accounts\JournalVoucherController@getAccountTypeList');
    Route::get('get_voucher_type_list/', 'Accounts\JournalVoucherController@getVoucherType');
    Route::get('get_voucher_ref_list/{id}', 'Accounts\JournalVoucherController@getVoucherRef');

    Route::resource('opening-balance','Accounts\OpeningBalanceController',['parameters'=> ['opening-balance'=>'id']]);

    Route::post('bank-payment-vouchers',['as'=>'bank-payment-vouchers.approve','uses'=>'Accounts\BankPaymentVoucherController@approve']);
    Route::post('bank-receipt-vouchers',['as'=>'bank-receipt-vouchers.approve','uses'=>'Accounts\BankReceiptVoucherController@approve']);

    Route::post('bank-transfer-deposits',['as'=>'bank-transfer-deposits.approve','uses'=>'Accounts\BankTransferDepoditController@approve']);

    Route::post('cash-payment-vouchers',['as'=>'cash-payment-vouchers.approve','uses'=>'Accounts\CashPaymentVoucherController@approve']);
    Route::post('cash-receipt-vouchers',['as'=>'cash-receipt-vouchers.approve','uses'=>'Accounts\CashReceiptVoucherController@approve']);
    Route::post('contra-entry-vouchers',['as'=>'contra-entry-vouchers.approve','uses'=>'Accounts\ContraEntryVoucherController@approve']);
    Route::post('journal-vouchers',['as'=>'journal-vouchers.approve','uses'=>'Accounts\JournalVoucherController@approve']);

    Route::get('load_chart_ac/{main_id}',['as' => 'load_chart_ac.main_id', 'uses'=>'Accounts\OpeningBalanceController@load_chart_ac']);

    Route::resource('purchase-invoice-voucher','Accounts\PurchaseOrderVoucherController',['parameters'=> ['purchase-invoice-voucher'=>'id']]);
    Route::get('purchase-inv-bank-wise-account-number','Accounts\PurchaseOrderVoucherController@getPurchInvAcctNumbList');
    Route::get('purchase-inv-acct-num-wise-check-leaf-list','Accounts\PurchaseOrderVoucherController@getPurchInvCheckLeafList');
    ///Route::get('pay-type/{id}', 'Accounts\PurchaseOrderVoucherController@pay_type');

    Route::resource('purchase-return-voucher','Accounts\PurchaseReturnVoucherController',['parameters'=> ['purchase-return-voucher'=>'id']]);
    Route::get('purchase-retn-bank-wise-account-number','Accounts\PurchaseReturnVoucherController@getPurchRetnAcctNumbList');
    Route::get('purchase-retn-acct-num-wise-check-leaf-list','Accounts\PurchaseReturnVoucherController@getPurchRetnCheckLeafList');

    Route::resource('sales-invoice-voucher','Accounts\SalesInvoiceVoucherController',['parameters'=> ['sales-invoice-voucher'=>'id']]);
    Route::get('sales_inv_bank-wise-account-number','Accounts\SalesInvoiceVoucherController@getSalesInvAcctNumbList');
    Route::get('sales_inv_acct-num-wise-check-leaf-list','Accounts\SalesInvoiceVoucherController@getSalesInvCheckLeafList');


    Route::resource('sales-return-voucher','Accounts\SalesInvoiceReturnVoucherController',['parameters'=> ['sales-return-voucher'=>'id']]);
    Route::get('sales_ret_bank-wise-account-number','Accounts\SalesInvoiceReturnVoucherController@getSalesRetAcctNumbList');
    Route::get('sales_ret_acct-num-wise-check-leaf-list','Accounts\SalesInvoiceReturnVoucherController@getSalesRetCheckLeafList');

    Route::resource('year-end','Accounts\YearEndController',['parameters'=> ['year-end'=>'id']]);

    Route::get('journal-transaction-no', 'Accounts\JournalVoucherController@getJournalTransactionNo');
    Route::get('contra-transaction-no', 'Accounts\ContraEntryVoucherController@getContraTransactionNo');
    Route::get('bank-payment-no', 'Accounts\BankPaymentVoucherController@getBankPaymentNo');
    Route::get('bank-receipt-no', 'Accounts\BankReceiptVoucherController@getBankReceiptNo');
    Route::get('cash-payment-no', 'Accounts\CashPaymentVoucherController@getCashPaymentNo');
    Route::get('cash-receipt-no', 'Accounts\CashReceiptVoucherController@getCashReceiptNo');
    Route::get('bank-transfer-no', 'Accounts\BankTransferDepoditController@getBankTransferNo');

    Route::get('journal-report',['as'=>'journal-report.index','uses'=>'Accounts\Reports\JournalReportController@index']);

    Route::get('ledger-report',['as'=>'ledger-report.index','uses'=>'Accounts\Reports\LedgerReportController@index']);
    Route::get('ledger-report/{id}/chart-list','Accounts\Reports\LedgerReportController@getChartOfAcctList');
    Route::get('get_coa_list_by_sub2/{sub_code2_id}', 'Accounts\Reports\LedgerReportController@getCoaListBySub2');
    Route::any('ledger-subcode2-report', ['as' => 'ledger-subcode2-report.index', 'uses' => 'Accounts\Reports\LedgerSubcCode2ReportController@index']);


    // Route::get('account-wise-ledger-report',['as'=>'account-wise-ledger-report.index','uses'=>'Accounts\Reports\AccountsWiseLedgerReportsController@index']);

    Route::get('trial-balance-report',['as'=>'trial-balance-report.index','uses'=>'Accounts\Reports\TrialBalanceController@index']);
    Route::get('trial-balance-report-sub2',['as'=>'trial-balance-report-sub2.index','uses'=>'Accounts\Reports\TrialBalanceController@sub2']);
        Route::any('trial-balance-report-sub2/trial-balance-index-sub2',['as' => 'trial-balance-report-sub2.trial-balance-index-sub2', 'uses'=>'Accounts\Reports\TrialBalanceController@trialBalanceFilterReportSub2']);
        Route::any('trial-balance-report/trial-balance-index',['as' => 'trial-balance-report.trial-balance-index', 'uses'=>'Accounts\Reports\TrialBalanceController@trialBalanceFilterReport']);
        Route::any('trial-balance-report/trial-balance-report',['as' => 'trial-balance-report.trial-balance-report', 'uses'=>'Accounts\Reports\TrialBalanceController@trialBalanceFilterDateReport']);
        Route::any('trial-balance-report-sub2/trial-balance-report-sub2',['as' => 'trial-balance-report-sub2.trial-balance-report-sub2', 'uses'=>'Accounts\Reports\TrialBalanceController@trialBalanceFilterDateReportSub2']);

    Route::any('income-statement-report',['as'=>'income-statement-report.index','uses'=>'Accounts\Reports\IncomeStatementReportController@index']);
    Route::any('expense-statement-report',['as'=>'expense-statement-report.index','uses'=>'Accounts\Reports\ExpenseReportController@index']);
    Route::any('balance-sheet-report',['as'=>'balance-sheet-report.index','uses'=>'Accounts\Reports\BalanceSheetController@index']);
    Route::get('daily-statement-report',['as'=>'daily-statement-report.index','uses'=>'Accounts\Reports\DailyStatementController@index']);
    Route::get('bank-statement-report',['as'=>'bank-statement-report.index','uses'=>'Accounts\Reports\BankStatementController@index']);
    Route::any('loss-profit-statement-report',['as'=>'loss-profit-statement-report.index','uses'=>'Accounts\Reports\LossAndProfitStatementReportController@index']);
    Route::get('bank-reconcilation-report',['as'=>'bank-reconcilation-report.index','uses'=>'Accounts\Reports\BankReconciliationReportController@index']);
    //Route::any('bank-reconcilation-report/report',['as'=>'bank-reconcilation-report.report','uses'=>'Accounts\Reports\BankReconciliationReportController@report']);


    //Report

    Route::any('journal-report/journal-index',['as' => 'journal-report.journal-index', 'uses'=>'Accounts\Reports\JournalReportController@journalFilterReport']);

    Route::any('ledger-report/ledger-index',['as' => 'ledger-report.ledger-index', 'uses'=>'Accounts\Reports\LedgerReportController@ledgerFilterReport']);
   // Route::any('ledger-subcode2-report/ledger-index',['as' => 'ledger-subcode2-report.ledger-index', 'uses'=>'Accounts\Reports\LedgerSubcCode2ReportController@ledgerSubCode2Report']);

    Route::post('year-closing','Accounts\YearEndController@yearClosing');

    Route::get('cash-book-report',['as'=>'cash-book-report.index','uses'=>'Accounts\Reports\CashBookReportController@index']);
    //Route::any('cash-book-report/report-index',['as'=>'cash-book-report.report-index','uses'=>'Accounts\Reports\CashBookReportController@reportCashBook']);

	//Account Voucher for EnterProse

    Route::resource('production-voucher','Accounts\ProductionVoucherController',['parameters'=> ['production-voucher'=>'id']]);

    //Account Voucher for Pole

    Route::resource('rmr-voucher','Accounts\RmrVoucherController',['parameters'=> ['rmr-voucher'=>'id']]);
     Route::resource('grm-voucher','Accounts\GmrVoucherController',['parameters'=> ['grm-voucher'=>'id']]);

});

