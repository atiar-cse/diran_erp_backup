<?php

Auth::routes();

Route::get('/', 'Auth\LoginController@index');
Route::get('admin-login', 'Auth\LoginController@index')->name('admin-login');
Route::post('login', [ 'as' => 'login', 'uses' => 'Auth\LoginController@Auth']);

Route::group(['middleware' => ['preventbackbutton','auth']], function() {

    Route::get('dashboard', 'DashboardController@index')->name('dashboard');
    Route::get('logout', 'Auth\LoginController@logout');

    Route::resource('user','UserAccessControl\UserController',['parameters'=> ['user'=>'id']]);
    Route::resource('role','UserAccessControl\RoleController',['parameters'=> ['role'=>'id']]);
    Route::group(['prefix' => 'user-role-permissoin'], function () {
        Route::get('/',['as' => 'user-role-permissoin.index', 'uses'=>'UserAccessControl\PermissionController@index']);
        Route::post('/store',['as' => 'user-role-permissoin.store', 'uses'=>'UserAccessControl\PermissionController@store']);
    });

    Route::resource('Company-Info','CompanyInfoController');

    //Route::resource('user-role-permissoin','UserAccessControl\PermissionController',['parameters'=> ['user-role-permissoin'=>'id']]);

     Route::post('user-role-permission-get_all_menus', 'UserAccessControl\PermissionController@getAllMenus');

     Route::get('config',[ 'as' => 'config?module_id=7', 'uses' => 'ConfigController@index']);
     Route::post('config.update', [ 'as' => 'config.update', 'uses' => 'ConfigController@update']);

     //Advanced
    Route::get('backup', 'Advanced\BackupController@index')->name('backup');
    Route::get('backup-create', 'Advanced\BackupController@create')->name('backup-create');
    Route::get('backup/download/{file_name?}', 'Advanced\BackupController@download');
    Route::delete('backup/delete/{file_name?}', 'Advanced\BackupController@delete')->where('file_name', '(.*)');
});

