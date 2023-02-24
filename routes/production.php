<?php

Route::group(['middleware' => ['preventbackbutton','auth']], function(){

    Route::group(['prefix' => 'production-entry'], function () {
        Route::get('/',['as' => 'production-entry.index', 'uses'=>'Production\Setup\ProductEntryController@index']);
        Route::get('/create',['as' => 'production-entry.create', 'uses'=>'Production\Setup\ProductEntryController@create']);
        Route::post('/store',['as' => 'production-entry.store', 'uses'=>'Production\Setup\ProductEntryController@store']);
        Route::get('/{id}',['as'=>'production-entry.show','uses'=>'Production\Setup\ProductEntryController@show']);
        Route::get('/{id}/edit',['as'=>'production-entry.edit','uses'=>'Production\Setup\ProductEntryController@edit']);
        Route::put('/{id}/update',['as' => 'production-entry.update', 'uses'=>'Production\Setup\ProductEntryController@update']);
        Route::delete('/{id}/delete',['as'=>'production-entry.delete','uses'=>'Production\Setup\ProductEntryController@destroy']);
    });


    Route::group(['prefix' => 'production-category'], function () {
        Route::get('/',['as' => 'production-category.index', 'uses'=>'Production\Setup\ProductCategoryController@index']);
        Route::get('/create',['as' => 'production-category.create', 'uses'=>'Production\Setup\ProductCategoryController@create']);
        Route::post('/store',['as' => 'production-category.store', 'uses'=>'Production\Setup\ProductCategoryController@store']);
        Route::get('/{id}',['as'=>'production-category.show','uses'=>'Production\Setup\ProductCategoryController@show']);
        Route::get('/{id}/edit',['as'=>'production-category.edit','uses'=>'Production\Setup\ProductCategoryController@edit']);
        Route::put('/{id}/update',['as' => 'production-category.update', 'uses'=>'Production\Setup\ProductCategoryController@update']);
        Route::delete('/{id}/delete',['as'=>'production-category.delete','uses'=>'Production\Setup\ProductCategoryController@destroy']);
    });


    Route::group(['prefix' => 'production-brand'], function () {
        Route::get('/',['as' => 'production-brand.index', 'uses'=>'Production\Setup\ProductBrandController@index']);
        Route::get('/create',['as' => 'production-brand.create', 'uses'=>'Production\Setup\ProductBrandController@create']);
        Route::post('/store',['as' => 'production-brand.store', 'uses'=>'Production\Setup\ProductBrandController@store']);
        Route::get('/{id}',['as'=>'production-brand.show','uses'=>'Production\Setup\ProductBrandController@show']);
        Route::get('/{id}/edit',['as'=>'production-brand.edit','uses'=>'Production\Setup\ProductBrandController@edit']);
        Route::put('/{id}/update',['as' => 'production-brand.update', 'uses'=>'Production\Setup\ProductBrandController@update']);
        Route::delete('/{id}/delete',['as'=>'production-brand.delete','uses'=>'Production\Setup\ProductBrandController@destroy']);
    });

     Route::group(['prefix' => 'measure-unit'], function () {
        Route::get('/',['as' => 'measure-unit.index', 'uses'=>'Production\Setup\MeasureUnitController@index']);
        Route::get('/create',['as' => 'measure-unit.create', 'uses'=>'Production\Setup\MeasureUnitController@create']);
        Route::post('/store',['as' => 'measure-unit.store', 'uses'=>'Production\Setup\MeasureUnitController@store']);
        Route::get('/{id}',['as'=>'measure-unit.show','uses'=>'Production\Setup\MeasureUnitController@show']);
        Route::get('/{id}/edit',['as'=>'measure-unit.edit','uses'=>'Production\Setup\MeasureUnitController@edit']);
        Route::put('/{id}/update',['as' => 'measure-unit.update', 'uses'=>'Production\Setup\MeasureUnitController@update']);
        Route::delete('/{id}/delete',['as'=>'measure-unit.delete','uses'=>'Production\Setup\MeasureUnitController@destroy']);
    });

    Route::resource('indirect-costs-type','Production\Setup\IndirectCostsTypeController');

    Route::group(['prefix' => 'raw-material-ratio-setup'], function () {
        Route::get('/',['as' => 'raw-material-ratio-setup.index', 'uses'=>'Production\Setup\RawMaterialRatioSetupController@index']);
        Route::get('/create',['as' => 'raw-material-ratio-setup.create', 'uses'=>'Production\Setup\RawMaterialRatioSetupController@create']);
        Route::post('/store',['as' => 'raw-material-ratio-setup.store', 'uses'=>'Production\Setup\RawMaterialRatioSetupController@store']);
        Route::get('/{id}',['as'=>'raw-material-ratio-setup.show','uses'=>'Production\Setup\RawMaterialRatioSetupController@show']);
        Route::get('/{id}/edit',['as'=>'raw-material-ratio-setup.edit','uses'=>'Production\Setup\RawMaterialRatioSetupController@edit']);
        Route::put('/{id}',['as' => 'raw-material-ratio-setup.update', 'uses'=>'Production\Setup\RawMaterialRatioSetupController@update']);
        Route::delete('/{id}/delete',['as'=>'raw-material-ratio-setup.delete','uses'=>'Production\Setup\RawMaterialRatioSetupController@destroy']);
        Route::get('product-list', 'Production\Setup\RawMaterialRatioSetupController@getProductList');
    });

    Route::group(['prefix' => 'assembling-setup'], function () {
        Route::get('/',['as' => 'assembling-setup.index', 'uses'=>'Production\Setup\AssemblingSetupController@index']);
        Route::get('/create',['as' => 'assembling-setup.create', 'uses'=>'Production\Setup\AssemblingSetupController@create']);
        Route::post('/store',['as' => 'assembling-setup.store', 'uses'=>'Production\Setup\AssemblingSetupController@store']);
        Route::get('/{id}',['as'=>'assembling-setup.show','uses'=>'Production\Setup\AssemblingSetupController@show']);
        Route::get('/{id}/edit',['as'=>'assembling-setup.edit','uses'=>'Production\Setup\AssemblingSetupController@edit']);
        Route::put('/{id}',['as' => 'assembling-setup.update', 'uses'=>'Production\Setup\AssemblingSetupController@update']);
        Route::delete('/{id}/delete',['as'=>'assembling-setup.delete','uses'=>'Production\Setup\AssemblingSetupController@destroy']);
    });

    Route::group(['prefix' => 'requisition-for-raw-material'], function () {
        Route::get('/',['as' => 'requisition-for-raw-material.index', 'uses'=>'Production\RequisitionForRawMaterialController@index']);
        Route::get('/rm-ratio-setup-list',['as' => 'requisition-for-raw-material.rm-ratio-setup-list', 'uses'=>'Production\RequisitionForRawMaterialController@rmRatioSetupList']);
        Route::get('/create',['as' => 'requisition-for-raw-material.create', 'uses'=>'Production\RequisitionForRawMaterialController@create']);
        Route::post('/store',['as' => 'requisition-for-raw-material.store', 'uses'=>'Production\RequisitionForRawMaterialController@store']);
        Route::get('/{id}',['as'=>'requisition-for-raw-material.show','uses'=>'Production\RequisitionForRawMaterialController@show']);
        Route::get('/{id}/edit',['as'=>'requisition-for-raw-material.edit','uses'=>'Production\RequisitionForRawMaterialController@edit']);
        Route::put('/{id}',['as' => 'requisition-for-raw-material.update', 'uses'=>'Production\RequisitionForRawMaterialController@update']);
        Route::delete('/{id}/delete',['as'=>'requisition-for-raw-material.delete','uses'=>'Production\RequisitionForRawMaterialController@destroy']);
        Route::post('/{id}/approve',['as' => 'requisition-for-raw-material.approve', 'uses'=>'Production\RequisitionForRawMaterialController@approve']);
    });

    Route::get('rm-requisition-no', 'Production\RequisitionForRawMaterialController@requisitionRMNoGenerate');

    Route::group(['prefix' => 'massbody-section'], function () {
        Route::get('/',['as' => 'massbody-section.index', 'uses'=>'Production\MassbodySectionController@index']);
        Route::get('/create',['as' => 'massbody-section.create', 'uses'=>'Production\MassbodySectionController@create']);
        Route::post('/store',['as' => 'massbody-section.store', 'uses'=>'Production\MassbodySectionController@store']);
        Route::get('/{id}',['as'=>'massbody-section.show','uses'=>'Production\MassbodySectionController@show']);
        Route::get('/{id}/edit',['as'=>'massbody-section.edit','uses'=>'Production\MassbodySectionController@edit']);
        Route::put('/{id}',['as' => 'massbody-section.update', 'uses'=>'Production\MassbodySectionController@update']);
        Route::delete('/{id}/delete',['as'=>'massbody-section.delete','uses'=>'Production\MassbodySectionController@destroy']);
        Route::post('/{id}/approve',['as' => 'massbody-sectio.approve', 'uses'=>'Production\MassbodySectionController@approve']);

    });
    Route::get('get-requisition-weight', 'Production\MassbodySectionController@getRequisitionWeight');
    Route::get('massbody-no', 'Production\MassbodySectionController@massbodyNoGenerate');

    Route::group(['prefix' => 'forming-section'], function () {
        Route::get('/',['as' => 'forming-section.index', 'uses'=>'Production\FormingSectionController@index']);
        Route::get('/create',['as' => 'forming-section.create', 'uses'=>'Production\FormingSectionController@create']);
        Route::post('/store',['as' => 'forming-section.store', 'uses'=>'Production\FormingSectionController@store']);
        Route::get('/{id}',['as'=>'forming-section.show','uses'=>'Production\FormingSectionController@show']);
        Route::get('/{id}/edit',['as'=>'forming-section.edit','uses'=>'Production\FormingSectionController@edit']);
        Route::put('/{id}',['as' => 'forming-section.update', 'uses'=>'Production\FormingSectionController@update']);
        Route::delete('/{id}/delete',['as'=>'forming-section.delete','uses'=>'Production\FormingSectionController@destroy']);
        Route::post('/{id}/approve',['as' => 'forming-section.approve', 'uses'=>'Production\FormingSectionController@approve']);
    });
    Route::get('forming-no', 'Production\FormingSectionController@formingNoGenerate');

    Route::group(['prefix' => 'shapping-section'], function () {
        Route::get('/',['as' => 'shapping-section.index', 'uses'=>'Production\ShappingSectionController@index']);
        Route::get('/create',['as' => 'shapping-section.create', 'uses'=>'Production\ShappingSectionController@create']);
        Route::post('/store',['as' => 'shapping-section.store', 'uses'=>'Production\ShappingSectionController@store']);
        Route::get('/{id}',['as'=>'shapping-section.show','uses'=>'Production\ShappingSectionController@show']);
        Route::get('/{id}/edit',['as'=>'shapping-section.edit','uses'=>'Production\ShappingSectionController@edit']);
        Route::put('/{id}',['as' => 'shapping-section.update', 'uses'=>'Production\ShappingSectionController@update']);
        Route::delete('/{id}/delete',['as'=>'shapping-section.delete','uses'=>'Production\ShappingSectionController@destroy']);
        Route::post('/{id}/approve',['as' => 'shapping-section.approve', 'uses'=>'Production\ShappingSectionController@approve']);
    });

    Route::get('shapping-no', 'Production\ShappingSectionController@shappingNoGenerate');

    Route::group(['prefix' => 'dryer-section'], function () {
        Route::get('/',['as' => 'dryer-section.index', 'uses'=>'Production\DryerSectionController@index']);
        Route::get('/create',['as' => 'dryer-section.create', 'uses'=>'Production\DryerSectionController@create']);
        Route::post('/store',['as' => 'dryer-section.store', 'uses'=>'Production\DryerSectionController@store']);
        Route::get('/{id}',['as'=>'dryer-section.show','uses'=>'Production\DryerSectionController@show']);
        Route::get('/{id}/edit',['as'=>'dryer-section.edit','uses'=>'Production\DryerSectionController@edit']);
        Route::put('/{id}',['as' => 'dryer-section.update', 'uses'=>'Production\DryerSectionController@update']);
        Route::delete('/{id}/delete',['as'=>'dryer-section.delete','uses'=>'Production\DryerSectionController@destroy']);
        Route::post('/{id}/approve',['as' => 'dryer-section.approve', 'uses'=>'Production\DryerSectionController@approve']);
    });

    Route::get('dryer-no', 'Production\DryerSectionController@dryerNoGenerate');

    Route::group(['prefix' => 'glaze-section'], function () {
        Route::get('/',['as' => 'glaze-section.index', 'uses'=>'Production\GlazeSectionController@index']);
        Route::get('/create',['as' => 'glaze-section.create', 'uses'=>'Production\GlazeSectionController@create']);
        Route::post('/store',['as' => 'glaze-section.store', 'uses'=>'Production\GlazeSectionController@store']);
        Route::get('/{id}',['as'=>'glaze-section.show','uses'=>'Production\GlazeSectionController@show']);
        Route::get('/{id}/edit',['as'=>'glaze-section.edit','uses'=>'Production\GlazeSectionController@edit']);
        Route::put('/{id}',['as' => 'glaze-section.update', 'uses'=>'Production\GlazeSectionController@update']);
        Route::delete('/{id}/delete',['as'=>'glaze-section.delete','uses'=>'Production\GlazeSectionController@destroy']);
        Route::post('/{id}/approve',['as' => 'glaze-section.approve', 'uses'=>'Production\GlazeSectionController@approve']);
    });

    Route::get('glaze-no', 'Production\GlazeSectionController@glazeNoGenerate');

    Route::group(['prefix' => 'kiln-section'], function () {
        Route::get('/',['as' => 'kiln-section.index', 'uses'=>'Production\KilnSectionController@index']);
        Route::get('/create',['as' => 'kiln-section.create', 'uses'=>'Production\KilnSectionController@create']);
        Route::post('/store',['as' => 'kiln-section.store', 'uses'=>'Production\KilnSectionController@store']);
        Route::get('/{id}',['as'=>'kiln-section.show','uses'=>'Production\KilnSectionController@show']);
        Route::get('/{id}/edit',['as'=>'kiln-section.edit','uses'=>'Production\KilnSectionController@edit']);
        Route::put('/{id}',['as' => 'kiln-section.update', 'uses'=>'Production\KilnSectionController@update']);
        Route::delete('/{id}/delete',['as'=>'kiln-section.delete','uses'=>'Production\KilnSectionController@destroy']);
        Route::post('/{id}/approve',['as' => 'kiln-section.approve', 'uses'=>'Production\KilnSectionController@approve']);
    });

    Route::get('kiln-no', 'Production\KilnSectionController@kilnNoGenerate');


    Route::group(['prefix' => 'testing-section'], function () {
        Route::get('/',['as' => 'testing-section.index', 'uses'=>'Production\TestingSectionController@index']);
        Route::get('/create',['as' => 'testing-section.create', 'uses'=>'Production\TestingSectionController@create']);
        Route::post('/store',['as' => 'testing-section.store', 'uses'=>'Production\TestingSectionController@store']);
        Route::get('/{id}',['as'=>'testing-section.show','uses'=>'Production\TestingSectionController@show']);
        Route::get('/{id}/edit',['as'=>'testing-section.edit','uses'=>'Production\TestingSectionController@edit']);
        Route::put('/{id}',['as' => 'testing-section.update', 'uses'=>'Production\TestingSectionController@update']);
        Route::delete('/{id}/delete',['as'=>'testing-section.delete','uses'=>'Production\TestingSectionController@destroy']);
        Route::post('/{id}/approve',['as' => 'testing-section.approve', 'uses'=>'Production\TestingSectionController@approve']);
    });

    Route::get('testing-no', 'Production\TestingSectionController@testingNoGenerate');


    Route::get('semi-finished-stock','Production\SemiFinishedStockController@index');


    Route::resource('finished-stock-section','Production\FinishedStockSectionController');

    Route::group(['prefix' => 'assembling-section'], function () {
        Route::get('/',['as' => 'assembling-section.index', 'uses'=>'Production\AssemblingSectionController@index']);
        Route::get('/create',['as' => 'assembling-section.create', 'uses'=>'Production\AssemblingSectionController@create']);
        Route::post('/store',['as' => 'assembling-section.store', 'uses'=>'Production\AssemblingSectionController@store']);
        Route::get('/{id}',['as'=>'assembling-section.show','uses'=>'Production\AssemblingSectionController@show']);
        Route::get('/{id}/edit',['as'=>'assembling-section.edit','uses'=>'Production\AssemblingSectionController@edit']);
        Route::put('/{id}',['as' => 'assembling-section.update', 'uses'=>'Production\AssemblingSectionController@update']);
        Route::delete('/{id}/delete',['as'=>'assembling-section.delete','uses'=>'Production\AssemblingSectionController@destroy']);
        Route::post('/{id}/approve',['as' => 'assembling-section.approve', 'uses'=>'Production\AssemblingSectionController@approve']);
    });

    Route::get('get-assembling-setup-data/finishing_product_id', 'Production\AssemblingSectionController@getAssemblingSetupData');
    Route::get('assembling-no', 'Production\AssemblingSectionController@assemblingNoGenerate');

    Route::group(['prefix' => 'packing-section'], function () {
        Route::get('/',['as' => 'packing-section.index', 'uses'=>'Production\PackingSectionController@index']);
        Route::get('/create',['as' => 'packing-section.create', 'uses'=>'Production\PackingSectionController@create']);
        Route::post('/store',['as' => 'packing-section.store', 'uses'=>'Production\PackingSectionController@store']);
        Route::get('/{id}',['as'=>'packing-section.show','uses'=>'Production\PackingSectionController@show']);
        Route::get('/{id}/edit',['as'=>'packing-section.edit','uses'=>'Production\PackingSectionController@edit']);
        Route::put('/{id}',['as' => 'packing-section.update', 'uses'=>'Production\PackingSectionController@update']);
        Route::delete('/{id}/delete',['as'=>'packing-section.delete','uses'=>'Production\PackingSectionController@destroy']);
        Route::post('/{id}/approve',['as' => 'packing-section.approve', 'uses'=>'Production\PackingSectionController@approve']);
    });

    Route::get('packing-no', 'Production\PackingSectionController@packingNoGenerate');

    Route::resource('indirect-costs','Production\ProductionIndirectCostsController');

    /*-------------Reports-----------*/
    Route::get('massbody-report',['as'=>'massbody-report.index','uses'=>'Production\Reports\MassbodyReportController@index']);
    //Route::any('massbody-report/report-index',['as' => 'massbody-report.report-index', 'uses'=>'Production\Reports\MassbodyReportController@massbodyFilterReport']);

    Route::get('forming-report',['as'=>'forming-report.index','uses'=>'Production\Reports\FormingReportController@index']);
    Route::get('shapping-report',['as'=>'shapping-report.index','uses'=>'Production\Reports\ShappingReportController@index']);
    Route::get('dryer-report',['as'=>'dryer-report.index','uses'=>'Production\Reports\DryerReportController@index']);
    Route::get('glaze-report',['as'=>'glaze-report.index','uses'=>'Production\Reports\GlazeReportController@index']);
    Route::get('kiln-report',['as'=>'kiln-report.index','uses'=>'Production\Reports\KilnReportController@index']);
    Route::get('testing-hv-report',['as'=>'testing-hv-report.index','uses'=>'Production\Reports\TestingReportController@index']);
/*
    Route::get('semifinished-report',['as'=>'semifinished-report.index','uses'=>'Production\SemiFinishedStockController@index']);
*/
    Route::get('finished-report',['as'=>'finished-report.index','uses'=>'Production\Reports\FinishedReportController@index']);
    Route::get('assembling-report',['as'=>'assembling-report.index','uses'=>'Production\Reports\AssemblingReportController@index']);
    Route::get('cementing-report',['as'=>'cementing-report.index','uses'=>'Production\Reports\CementingReportController@index']);
    Route::get('cost-of-production',['as'=>'cost-of-production.index','uses'=>'Production\Reports\CostOfProductionReportController@index']);

});
