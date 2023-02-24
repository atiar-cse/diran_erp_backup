<?php

Route::group(['middleware' => ['preventbackbutton','auth']], function(){

    Route::resource('lc-voucher-type', 'LcImport\LcVoucherTypeController');
    Route::resource('lc-cost-name-category-entry','LcImport\LcCostNameCategoryEntryController');

    Route::resource('lc-cost-name-entry','LcImport\LcCostNameEntryController',['parameters'=> ['lc-cost-name-entry'=>'id']]);
    Route::get('lc-cost-name-entry/{id}/list-by-category','LcImport\LcCostNameEntryController@getListByCategory',['parameters'=> ['commercial-invoice-entry'=>'id']]);

    Route::resource('lc-custom-duty-cost-name-entry','LcImport\LcCustomDutyCostNameEntryController');
    Route::resource('lc-cnf-agent-entry','LcImport\LcCnfAgentEntryController');

    Route::resource('work-order-import','LcImport\WorkOrderImportController',['parameters'=> ['work-order-import'=>'id']]);
    Route::get('work-order-no-generate', 'LcImport\WorkOrderImportController@workOrderNoGenerate');
    Route::get('work-order-import/{id}/lc-product-list', 'LcImport\WorkOrderImportController@getProductList');

    Route::resource('proforma-invoice-entry','LcImport\ProformaInvoiceEntryController',['parameters'=> ['proforma-invoice-entry'=>'id']]);
    Route::get('pi-no-generate', 'LcImport\ProformaInvoiceEntryController@piNoGenerate');

    Route::resource('lc-opening-section','LcImport\LcOpeningSectionController',['parameters'=> ['lc-opening-section'=>'id']]);
    Route::get('lc-no-generate', 'LcImport\LcOpeningSectionController@lcNoGenerate');

    Route::post('lc-opening-section/add-lc-charge','LcImport\LcOpeningSectionController@AddLcCharge');

    Route::resource('lc-insurance','LcImport\LcInsuranceController');
    Route::get('lc-insurance-no-generate', 'LcImport\LcInsuranceController@lcNoGenerate');

    Route::resource('lc-cf-value-margin-entry','LcImport\LcCandFValueEntryController',['parameters'=> ['lc-cf-value-margin-entry'=>'id']]);
    Route::get('lc-cf-value-margin-entry-no-generate', 'LcImport\LcCandFValueEntryController@lcNoGenerate');

    Route::resource('commercial-invoice-entry','LcImport\CommercialInvoiceEntryController',['parameters'=> ['commercial-invoice-entry'=>'id']]);
    Route::get('ci-no-generate', 'LcImport\CommercialInvoiceEntryController@ciNoGenerate');
    Route::get('commercial-invoice-entry/{id}/lc-product-list','LcImport\CommercialInvoiceEntryController@getLcProductList',['parameters'=> ['commercial-invoice-entry'=>'id']]);
    Route::get('commercial-invoice-entry/{id}/ci-product-list','LcImport\CommercialInvoiceEntryController@getCiProductList',['parameters'=> ['commercial-invoice-entry'=>'id']]);

    Route::resource('latr-entry','LcImport\LATREntryController',['parameters'=> ['latr-entry'=>'id']]);
    Route::get('latr-entry/{id}/get-commercial-invoices-by-lcno','LcImport\LATREntryController@getCommercialInvoicesByLcNo',['parameters'=> ['commercial-invoice-entry'=>'id']]);

    Route::resource('custom-duty-charge-entry','LcImport\CustomDutyChargeEntryController',['parameters'=> ['custom-duty-charge-entry'=>'id']]);
    Route::get('custom-duty-charge-entry-no-generate', 'LcImport\CustomDutyChargeEntryController@lcNoGenerate');

    Route::resource('others-charge-entry','LcImport\OthersChargeEntryController',['parameters'=> ['others-charge-entry'=>'id']]);
    Route::get('others-charge-entry-no-generate', 'LcImport\OthersChargeEntryController@lcNoGenerate');
    // Route::get('others-charge-entry-cost-category', 'LcImport\OthersChargeEntryController@getLcCostCategory');

    Route::resource('lc-ci-landed-cost','LcImport\LcCiLandedCostController');

    Route::resource('lc-stock-entry','LcImport\LcStockEntryController');

    Route::resource('lc-closing','LcImport\LcClosingController');
    Route::get('lc-closing/{id}/commercial-invoices','LcImport\LcClosingController@getCommercialInvoices');

    Route::resource('lc-report','LcImport\LcReportController', ['only' => ['index', 'show']]);

    Route::get('lc-ledger-report',[ 'as' => 'lc-ledger-report.index', 'uses' => 'LcImport\LcLedgerReportController@index']);
    Route::any('lc-ledger-report/lc-ledger-index', ['as' => 'lc-ledger-report.lc-ledger-index', 'uses' => 'LcImport\LcLedgerReportController@ledgerFilterReport']);
});
