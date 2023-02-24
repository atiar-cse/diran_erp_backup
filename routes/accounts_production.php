<?php

Route::group(['middleware' => ['preventbackbutton','auth']], function(){


    /*Production Related Voucher  Start*/
    Route::resource('acc-req-rm-voucher','Accounts\Production\RequsitionRmVoucherController',['parameters'=> ['req-rm-voucher'=>'id']]);
    Route::resource('acc-mass-body-voucher','Accounts\Production\MassbodyVoucherController',['parameters'=> ['acc-mass-body-voucher'=>'id']]);
    Route::resource('acc-packing-voucher','Accounts\Production\PackingVoucherController',['parameters'=> ['acc-packing-voucher'=>'id']]);
    Route::resource('acc-assembly-voucher','Accounts\Production\AsssemblingVoucherController',['parameters'=> ['acc-assembly-voucher'=>'id']]);
    Route::resource('acc-finish-stock-voucher','Accounts\Production\FinishedVoucherController',['parameters'=> ['acc-finish-stock-voucher'=>'id']]);
    Route::resource('acc-testing-voucher','Accounts\Production\TestingVoucherController',['parameters'=> ['acc-testing-voucher'=>'id']]);
    Route::resource('acc-kiln-voucher','Accounts\Production\KilnVoucherController',['parameters'=> ['acc-kiln-voucher'=>'id']]);
    Route::resource('acc-glaze-voucher','Accounts\Production\GlazeVoucherController',['parameters'=> ['acc-glaze-voucher'=>'id']]);
    Route::resource('acc-dryer-voucher','Accounts\Production\DryerVoucherController',['parameters'=> ['acc-dryer-voucher'=>'id']]);
    Route::resource('acc-shapping-voucher','Accounts\Production\ShappingVoucherController',['parameters'=> ['acc-shapping-voucher'=>'id']]);
    Route::resource('acc-forming-voucher','Accounts\Production\FormingVoucherController',['parameters'=> ['acc-forming-voucher'=>'id']]);

    /*Production Related voucher End*/

	});

