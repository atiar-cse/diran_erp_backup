<?php
Route::group(['middleware' => ['preventbackbutton','auth']], function(){

	   Route::group(['prefix' => 'customer'], function () {
        Route::get('/',['as' => 'customer.index', 'uses'=>'Sales\Setup\SalesCustomerController@index']);
        Route::get('/create',['as' => 'customer.create', 'uses'=>'Sales\Setup\SalesCustomerController@create']);
        Route::post('/store',['as' => 'customer.store', 'uses'=>'Sales\Setup\SalesCustomerController@store']);
        Route::get('/{id}',['as'=> 'customer.show','uses'=>'Sales\Setup\SalesCustomerController@show']);
        Route::get('/{id}/edit',['as'=> 'customer.edit','uses'=>'Sales\Setup\SalesCustomerController@edit']);
        Route::put('/{id}/update',['as' => 'customer.update', 'uses'=>'Sales\Setup\SalesCustomerController@update']);
        Route::delete('/{id}/delete',['as'=> 'customer.delete','uses'=>'Sales\Setup\SalesCustomerController@destroy']);
    });

	Route::group(['prefix' => 'sales-invoice'], function () {
        Route::get('/',['as' => 'sales-invoice.index', 'uses'=>'Sales\SalesInvoiceController@index']);
        Route::get('/create',['as' => 'sales-invoice.create', 'uses'=>'Sales\SalesInvoiceController@create']);
        Route::post('/store',['as' => 'sales-invoice.store', 'uses'=>'Sales\SalesInvoiceController@store']);
        Route::get('/{id}',['as'=>'sales-invoice.show','uses'=>'Sales\SalesInvoiceController@show']);
        Route::get('/{id}/edit',['as'=>'sales-invoice.edit','uses'=>'Sales\SalesInvoiceController@edit']);
        Route::put('/{id}/update',['as' => 'sales-invoice.update', 'uses'=>'Sales\SalesInvoiceController@update']);
        Route::delete('/{id}/delete',['as'=>'sales-invoice.delete','uses'=>'Sales\SalesInvoiceController@destroy']);
        Route::post('/{id}/approve',['as' => 'sales-invoice.approve', 'uses'=>'Sales\SalesInvoiceController@approve']);
        Route::get('/{id}',['as'=>'sales-invoice.show-without-price','uses'=>'Sales\SalesInvoiceController@showWithoutPrice']);

    });

    Route::get('/{wid}/load-products-list','Sales\SalesInvoiceController@listProduct');
    Route::get('sales-invoice-no', 'Sales\SalesInvoiceController@SalesInvoiceGenerate');

    Route::resource('sales-invoice','Sales\SalesInvoiceController',['parameters'=> ['sales-invoice'=>'id']]);

    Route::group(['prefix' => 'sales-delivery-challan'], function () {
        Route::get('/',['as' => 'sales-delivery-challan.index', 'uses'=>'Sales\SalesDeliveryChallanController@index']);
        Route::get('/create',['as' => 'sales-delivery-challan.create', 'uses'=>'Sales\SalesDeliveryChallanController@create']);
        Route::post('/store',['as' => 'sales-delivery-challan.store', 'uses'=>'Sales\SalesDeliveryChallanController@store']);
        Route::get('/{id}',['as'=>'sales-delivery-challan.show','uses'=>'Sales\SalesDeliveryChallanController@show']);
        Route::get('/{id}/edit',['as'=>'sales-delivery-challan.edit','uses'=>'Sales\SalesDeliveryChallanController@edit']);
        Route::put('/{id}/update',['as' => 'sales-delivery-challan.update', 'uses'=>'Sales\SalesDeliveryChallanController@update']);
        Route::delete('/{id}/delete',['as'=> 'sales-delivery-challan.delete','uses'=>'Sales\SalesDeliveryChallanController@destroy']);
        Route::get('/{challan_no}/{AddEditId}/{id}/sales-invoice-list',['as' => 'sales-delivery-challan.sales-invoice-list', 'uses'=>'Sales\SalesDeliveryChallanController@salesInvoicelist']);
        Route::post('/{id}/approve',['as' => 'sales-delivery-challan.approve', 'uses'=>'Sales\SalesDeliveryChallanController@approve']);
        Route::get('/{id}',['as'=>'sales-delivery-challan.show-without-price','uses'=>'Sales\SalesDeliveryChallanController@showWithoutPrice']);
    });
    Route::get('/{wid}/load-sales-product','Sales\SalesDeliveryChallanController@listProduct');
    Route::get('sales-challan-no', 'Sales\SalesDeliveryChallanController@SalesDeliveryChallanGenerate');

    Route::group(['prefix' => 'sales-delivery-return'], function () {
        Route::get('/',['as' => 'sales-delivery-return.index', 'uses'=>'Sales\SalesDeliveryReturnController@index']);
        Route::get('/create',['as' => 'sales-delivery-return.create', 'uses'=>'Sales\SalesDeliveryReturnController@create']);
        Route::post('/store',['as' => 'sales-delivery-return.store', 'uses'=>'Sales\SalesDeliveryReturnController@store']);
        Route::get('/{id}',['as'=>'sales-delivery-return.show','uses'=>'Sales\SalesDeliveryReturnController@show']);
        Route::get('/{id}/edit',['as'=>'sales-delivery-return.edit','uses'=>'Sales\SalesDeliveryReturnController@edit']);
        Route::put('/{id}/update',['as' => 'sales-delivery-return.update', 'uses'=>'Sales\SalesDeliveryReturnController@update']);
        Route::delete('/{id}/delete',['as'=> 'sales-delivery-return.delete','uses'=>'Sales\SalesDeliveryReturnController@destroy']);
        Route::post('/{id}/approve',['as' => 'sales-delivery-return.approve', 'uses'=>'Sales\SalesDeliveryReturnController@approve']);
    });
    Route::get('sales-return-no', 'Sales\SalesDeliveryReturnController@SalesReturnGenerate');

    Route::group(['prefix' => 'sales-report'], function () {
        Route::any('/sales',['as' => 'sales-report.sales', 'uses'=>'Sales\Report\SalesReportController@sales']);
        Route::any('/returnd',['as' => 'sales-report.returnd', 'uses'=>'Sales\Report\SalesReportController@returnd']);
        Route::any('/sales_summary',['as' => 'sales-report.sales_summary', 'uses'=>'Sales\Report\SalesReportController@sales_summary']);
    });

   /* Route::group(['prefix' => 'sales-return-report'], function () {

    });*/

    //Route::get('massbody-report',['as'=>'massbody-report.index','uses'=>'Production\Reports\MassbodyReportController@index']);

    /*Route::group(['prefix' => 'sales-return-report'], function () {
        Route::any('/return-index',['as' => 'sales-return-report.return-index', 'uses'=>'Sales\Report\SalesReportController@return_index']);
    });*/

    /*Route::group(['prefix' => 'customer-ledeger-report'], function () {
        Route::get('/customer-index',['as' => 'customer-ledeger-report.customer-index', 'uses'=>'Sales\Report\SalesReportController@customer_index']);
    });*/

    //Route::any('sales-report','Sales\Report\SalesReportController@index')->name('sales-report.index');
   //Route::any('sales-return-report/return-index','Sales\Report\SalesReportController@return_index')->name('sales-return-report.return-index');
   Route::any('customer-ledeger-report/customer-index',['as' => 'customer-ledeger-report.customer-index', 'uses'=>'Sales\Report\SalesReportController@customer_index']);


});
?>
