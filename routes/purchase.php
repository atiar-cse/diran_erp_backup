<?php
Route::group(['middleware' => ['preventbackbutton','auth']], function(){

	Route::group(['prefix' => 'supplier-entry'], function () {

        Route::get('/',['as' => 'supplier-entry.index', 'uses'=>'Purchase\Setup\PurchaseSupplierEntryController@index']);
        Route::get('/create',['as' => 'supplier-entry.create', 'uses'=>'Purchase\Setup\PurchaseSupplierEntryController@create']);
        Route::post('/store',['as' => 'supplier-entry.store', 'uses'=>'Purchase\Setup\PurchaseSupplierEntryController@store']);
        Route::get('/{id}',['as'=>'supplier-entry.show','uses'=>'Purchase\Setup\PurchaseSupplierEntryController@show']);
        Route::get('/{id}/edit',['as'=>'supplier-entry.edit','uses'=>'Purchase\Setup\PurchaseSupplierEntryController@edit']);
        Route::put('/{id}/update',['as' => 'supplier-entry.update', 'uses'=>'Purchase\Setup\PurchaseSupplierEntryController@update']);
        Route::delete('/{id}/delete',['as'=>'supplier-entry.delete','uses'=>'Purchase\Setup\PurchaseSupplierEntryController@destroy']);

    });

    Route::group(['prefix' => 'cost-type'], function () {

        Route::get('/',['as' => 'cost-type.index', 'uses'=>'Purchase\Setup\PurchaseCostTypeController@index']);
        Route::get('/create',['as' => 'cost-type.create', 'uses'=>'Purchase\Setup\PurchaseCostTypeController@create']);
        Route::post('/store',['as' => 'cost-type.store', 'uses'=>'Purchase\Setup\PurchaseCostTypeController@store']);
        Route::get('/{id}',['as'=>'cost-type.show','uses'=>'Purchase\Setup\PurchaseCostTypeController@show']);
        Route::get('/{id}/edit',['as'=>'cost-type.edit','uses'=>'Purchase\Setup\PurchaseCostTypeController@edit']);
        Route::put('/{id}/update',['as' => 'cost-type.update', 'uses'=>'Purchase\Setup\PurchaseCostTypeController@update']);
        Route::delete('/{id}/delete',['as'=>'cost-type.delete','uses'=>'Purchase\Setup\PurchaseCostTypeController@destroy']);

    });

    Route::group(['prefix' => 'return-type'], function () {

        Route::get('/',['as' => 'return-type.index', 'uses'=>'Purchase\Setup\PurchaseReturnTypeController@index']);
        Route::get('/create',['as' => 'return-type.create', 'uses'=>'Purchase\Setup\PurchaseReturnTypeController@create']);
        Route::post('/store',['as' => 'return-type.store', 'uses'=>'Purchase\Setup\PurchaseReturnTypeController@store']);
        Route::get('/{id}',['as'=>'return-type.show','uses'=>'Purchase\Setup\PurchaseReturnTypeController@show']);
        Route::get('/{id}/edit',['as'=>'return-type.edit','uses'=>'Purchase\Setup\PurchaseReturnTypeController@edit']);
        Route::put('/{id}/update',['as' => 'return-type.update', 'uses'=>'Purchase\Setup\PurchaseReturnTypeController@update']);
        Route::delete('/{id}/delete',['as'=>'return-type.delete','uses'=>'Purchase\Setup\PurchaseReturnTypeController@destroy']);

    });


    Route::group(['prefix' => 'ware-house'], function () {

        Route::get('/',['as' => 'ware-house.index', 'uses'=>'Purchase\Setup\PurchaseWareHouseController@index']);
        Route::get('/create',['as' => 'ware-house.create', 'uses'=>'Purchase\Setup\PurchaseWareHouseController@create']);
        Route::post('/store',['as' => 'ware-house.store', 'uses'=>'Purchase\Setup\PurchaseWareHouseController@store']);
        Route::get('/{id}',['as'=>'ware-house.show','uses'=>'Purchase\Setup\PurchaseWareHouseController@show']);
        Route::get('/{id}/edit',['as'=>'ware-house.edit','uses'=>'Purchase\Setup\PurchaseWareHouseController@edit']);
        Route::put('/{id}/update',['as' => 'ware-house.update', 'uses'=>'Purchase\Setup\PurchaseWareHouseController@update']);
        Route::delete('/{id}/delete',['as'=>'ware-house.delete','uses'=>'Purchase\Setup\PurchaseWareHouseController@destroy']);

    });



    Route::group(['prefix' => 'pr-requisition'], function () {

        Route::get('/',['as' => 'pr-requisition.index', 'uses'=>'Purchase\PurchaseRequisitionController@index']);
        Route::get('/create',['as' => 'pr-requisition.create', 'uses'=>'Purchase\PurchaseRequisitionController@create']);
        Route::post('/store',['as' => 'pr-requisition.store', 'uses'=>'Purchase\PurchaseRequisitionController@store']);
        Route::get('/{id}',['as'=> 'pr-requisition.show','uses'=>'Purchase\PurchaseRequisitionController@show']);
        Route::get('/{id}/edit',['as'=> 'pr-requisition.edit','uses'=>'Purchase\PurchaseRequisitionController@edit']);
        Route::put('/{id}/update',['as' => 'pr-requisition.update', 'uses'=>'Purchase\PurchaseRequisitionController@update']);
        Route::delete('/{id}/delete',['as'=> 'pr-requisition.delete','uses'=>'Purchase\PurchaseRequisitionController@destroy']);
    });
    Route::get('requisition-no', 'Purchase\PurchaseRequisitionController@PrReqNoGenerate');

    Route::group(['prefix' => 'po-receive'], function () {

        Route::get('/',['as' => 'po-receive.index', 'uses'=>'Purchase\PurchaseOrderReceiveController@index']);
        Route::get('/create',['as' => 'po-receive.create', 'uses'=>'Purchase\PurchaseOrderReceiveController@create']);
        Route::post('/store',['as' => 'po-receive.store', 'uses'=>'Purchase\PurchaseOrderReceiveController@store']);
        Route::get('/{id}',['as'=> 'po-receive.show','uses'=>'Purchase\PurchaseOrderReceiveController@show']);
        Route::get('/{id}/edit',['as'=> 'po-receive.edit','uses'=>'Purchase\PurchaseOrderReceiveController@edit']);
        Route::put('/{id}/update',['as' => 'po-receive.update', 'uses'=>'Purchase\PurchaseOrderReceiveController@update']);
        Route::delete('/{id}/delete',['as'=> 'po-receive.delete','uses'=>'Purchase\PurchaseOrderReceiveController@destroy']);
        Route::get('/{id}/po-requisition-list',['as' => 'po-receive.po-requisition-list', 'uses'=>'Purchase\PurchaseOrderReceiveController@porequisitionlist']);
    });
    Route::get('receive-no', 'Purchase\PurchaseOrderReceiveController@PoOrderGenerate');


    Route::group(['prefix' => 'po-cost-entry'], function () {

        Route::get('/',['as' => 'po-cost-entry.index', 'uses'=>'Purchase\PurchaseRelatedCostEntryController@index']);
        Route::get('/create',['as' => 'po-cost-entry.create', 'uses'=>'Purchase\PurchaseRelatedCostEntryController@create']);
        Route::post('/store',['as' => 'po-cost-entry.store', 'uses'=>'Purchase\PurchaseRelatedCostEntryController@store']);
        Route::get('/{id}',['as'=> 'po-cost-entry.show','uses'=>'Purchase\PurchaseRelatedCostEntryController@show']);
        Route::get('/{id}/edit',['as'=> 'po-cost-entry.edit','uses'=>'Purchase\PurchaseRelatedCostEntryController@edit']);
        Route::put('/{id}/update',['as' => 'po-cost-entry.update', 'uses'=>'Purchase\PurchaseRelatedCostEntryController@update']);
        Route::delete('/{id}/delete',['as'=> 'po-cost-entry.delete','uses'=>'Purchase\PurchaseRelatedCostEntryController@destroy']);
    });

    Route::group(['prefix' => 'po-return'], function () {

        Route::get('/',['as' => 'po-return.index', 'uses'=>'Purchase\PurchaseReturnController@index']);
        Route::get('/create',['as' => 'po-return.create', 'uses'=>'Purchase\PurchaseReturnController@create']);
        Route::post('/store',['as' => 'po-return.store', 'uses'=>'Purchase\PurchaseReturnController@store']);
        Route::get('/{id}',['as'=> 'po-return.show','uses'=>'Purchase\PurchaseReturnController@show']);
        Route::get('/{id}/edit',['as'=> 'po-return.edit','uses'=>'Purchase\PurchaseReturnController@edit']);
        Route::put('/{id}/update',['as' => 'po-return.update', 'uses'=>'Purchase\PurchaseReturnController@update']);
        Route::delete('/{id}/delete',['as'=> 'po-return.delete','uses'=>'Purchase\PurchaseReturnController@destroy']);
    });
    Route::get('return-no', 'Purchase\PurchaseReturnController@PoRtnNoGenerate');


    Route::any('purchase-report/purchase','Purchase\Report\PurchaseReportController@purchase')->name('purchase-report.purchase');
    Route::any('purchase-report/return_purchase','Purchase\Report\PurchaseReportController@return_purchase')->name('purchase-report.return_purchase');
    Route::any('purchase-report/supplier','Purchase\Report\PurchaseReportController@supplier')->name('purchase-report.supplier');
    Route::any('purchase-report/purchase_summary','Purchase\Report\PurchaseReportController@purchase_summary')->name('purchase-report.purchase_summary');
});
?>
