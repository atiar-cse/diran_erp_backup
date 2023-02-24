<?php
Route::group(['middleware' => ['preventbackbutton','auth']], function(){

    Route::group(['prefix' => 'stock-open'], function () {
        Route::get('/',['as' => 'stock-open.index', 'uses'=>'Inventory\InventoryCurrentStockController@index']);
        Route::get('/create',['as' => 'stock-open.create', 'uses'=>'Inventory\InventoryCurrentStockController@create']);
        Route::post('/store',['as' => 'stock-open.store', 'uses'=>'Inventory\InventoryCurrentStockController@store']);
        Route::get('/{id}',['as'=> 'stock-open.show','uses'=>'Inventory\InventoryCurrentStockController@show']);
        Route::get('/{id}/edit',['as'=> 'stock-open.edit','uses'=>'Inventory\InventoryCurrentStockController@edit']);
        Route::put('/{id}/update',['as' => 'stock-open.update', 'uses'=>'Inventory\InventoryCurrentStockController@update']);
        Route::delete('/{id}/delete',['as'=> 'stock-open.delete','uses'=>'Inventory\InventoryCurrentStockController@destroy']);

    });
    Route::get('inv_load_product/{ware_house_id}',['as' => 'inv_load_product.ware_house_id', 'uses'=>'Inventory\InventoryCurrentStockController@inv_load_product']);

    Route::group(['prefix' => 'stock-adjust'], function () {
        Route::get('/',['as' => 'stock-adjust.index', 'uses'=>'Inventory\InventoryStockAdjustController@index']);
        Route::get('/create',['as' => 'stock-adjust.create', 'uses'=>'Inventory\InventoryStockAdjustController@create']);
        Route::post('/store',['as' => 'stock-adjust.store', 'uses'=>'Inventory\InventoryStockAdjustController@store']);
        Route::get('/{id}',['as'=> 'stock-adjust.show','uses'=>'Inventory\InventoryStockAdjustController@show']);
        Route::get('/{id}/edit',['as'=> 'stock-adjust.edit','uses'=>'Inventory\InventoryStockAdjustController@edit']);
        Route::put('/{id}/update',['as' => 'stock-adjust.update', 'uses'=>'Inventory\InventoryStockAdjustController@update']);
        Route::delete('/{id}/delete',['as'=> 'stock-adjust.delete','uses'=>'Inventory\InventoryStockAdjustController@destroy']);
    });
	//Route::get('stock-adjust-no', 'Inventory\InventoryStockAdjustController@StockAdjustGenerate');

	Route::group(['prefix' => 'product-damage'], function () {
        Route::get('/',['as' => 'product-damage.index', 'uses'=>'Inventory\InventoryProductDamageController@index']);
        Route::get('/create',['as' => 'product-damage.create', 'uses'=>'Inventory\InventoryProductDamageController@create']);
        Route::post('/store',['as' => 'product-damage.store', 'uses'=>'Inventory\InventoryProductDamageController@store']);
        Route::get('/{id}',['as'=>'product-damage.show','uses'=>'Inventory\InventoryProductDamageController@show']);
        Route::get('/{id}/edit',['as'=>'product-damage.edit','uses'=>'Inventory\InventoryProductDamageController@edit']);
        Route::put('/{id}/update',['as' => 'product-damage.update', 'uses'=>'Inventory\InventoryProductDamageController@update']);
        Route::delete('/{id}/delete',['as'=>'product-damage.delete','uses'=>'Inventory\InventoryProductDamageController@destroy']);
    });
	//Route::get('damages-no', 'Inventory\InventoryProductDamageController@ProductDamageGenerate');
	
	Route::group(['prefix' => 'stock-transfer'], function () {
        Route::get('/',['as' => 'stock-transfer.index', 'uses'=>'Inventory\InventoryStockTransferController@index']);
        Route::get('/create',['as' => 'stock-transfer.create', 'uses'=>'Inventory\InventoryStockTransferController@create']);
        Route::post('/store',['as' => 'stock-transfer.store', 'uses'=>'Inventory\InventoryStockTransferController@store']);
        Route::get('/{id}',['as'=> 'stock-transfer.show','uses'=>'Inventory\InventoryStockTransferController@show']);
        Route::get('/{id}/edit',['as'=> 'stock-transfer.edit','uses'=>'Inventory\InventoryStockTransferController@edit']);
        Route::put('/{id}/update',['as' => 'stock-transfer.update', 'uses'=>'Inventory\InventoryStockTransferController@update']);
        Route::delete('/{id}/delete',['as'=> 'stock-transfer.delete','uses'=>'Inventory\InventoryStockTransferController@destroy']);
    });
    Route::get('transfer-no', 'Inventory\InventoryStockTransferController@transNoGenerate');
    Route::get('/{wid}/load-transfer-product','Inventory\InventoryStockTransferController@listProduct');


    Route::group(['prefix' => 'stock-report'], function () {
        Route::any('/stocks',['as' => 'stock-report.stocks', 'uses'=>'Inventory\Report\StockReportController@stocks']);
        /*Route::any('/stocks-entry',['as' => 'stock-report.stocks-entry', 'uses'=>'Inventory\Report\StockReportController@stocks_entry']);
        Route::any('/product-stock',['as' => 'stock-report.product-stock', 'uses'=>'Inventory\Report\StockReportController@product_stock']);
        Route::any('/transection-stock',['as' => 'stock-report.transection-stock', 'uses'=>'Inventory\Report\StockReportController@transection_stock']);
        Route::any('/stock-ledger',['as' => 'stock-report.stock-ledger', 'uses'=>'Inventory\Report\StockReportController@stock_ledger']);*/
        //Route::get('/',['as' => 'stock-report.index', 'uses'=>'Inventory\Report\StockReportController@index']);
    });
	
	//Route::get('stock-transfer-report',['as'=>'stock-transfer-report.index','uses'=>'Inventory\Report\StockTransferReportController@index']);
    Route::any('inventory-report/sales-price','Inventory\Report\SalesPriceReportController@index')->name('inventory-report.sales-price');

    Route::get('get_cat_wise_product_list/{type_id}', 'Inventory\Report\SalesPriceReportController@getCatWiseProductList');

});
?>
