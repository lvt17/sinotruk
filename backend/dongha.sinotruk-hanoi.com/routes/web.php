<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'HomeController@index')->name('wellcome');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');


Route::middleware('auth')->group(function() {
    Route::get('/exports/{id}/exportpdf', 'ExportController@exportpdf');
	Route::get('/exports/{id}/exportpdfseal', 'ExportController@exportpdfseal');
	Route::get('/exports/{id}/exportexcel', 'ExportController@exportexcel');
    Route::get('/exports/exportreport', 'ExportController@exportreport');
	Route::get('/exports/exportreportdetail', 'ExportController@exportreportdetail');
    Route::get('/exports/showstatus', 'ExportController@showstatus');
    Route::post('/exports/updateLock', 'ExportController@updateLock');
    Route::get('/exports/{id}/exporta5', 'ExportController@exporta5');
    Route::post('/products/bulkdelete', 'ProductController@bulkDelete');
    Route::post('/products/uploadexcel', 'ProductController@uploadExcel');
    Route::post('/products/filter', 'ProductController@filter');
    Route::get('/products/exportton', 'ProductController@exportton');
    Route::post('/products/uploadimage', 'ProductController@uploadimage');
    Route::post('/customers/bulkdelete', 'CustomerController@bulkDelete');
    Route::post('/customers/uploadexcel', 'CustomerController@uploadExcel');
    Route::post('/customers/filter', 'CustomerController@filter');
    Route::get('/customers/exportExcel', 'CustomerController@exportExcel');
    Route::get('/customerpays/{id}/checknew', 'CustomerPayController@checknew');
    Route::get('/customerpays/{id}/exportpdf', 'CustomerPayController@exportpdf');
    Route::get('/customerpays/exportExcel', 'CustomerPayController@exportExcel');
    Route::get('/debts/exportExcel', 'DebtController@exportExcel');
    Route::get('/debts/exportExcelTotal', 'DebtController@exportExcelTotal');
    Route::delete('/exports/{id}/recreate', 'ExportController@recreate');
    Route::delete('/imports/{id}/recreate', 'ImportController@recreate');
    Route::get('/products/logs', 'ProductController@logs');
    Route::get('/customers/logs', 'CustomerController@logs');
    Route::get('/quotes/{id}/exportExcel', 'QuoteController@exportExcel');
    Route::get('/quotes/{id}/exportPDF', 'QuoteController@exportPDF');
	Route::get('/quotes/{id}/exportPDFSeal', 'QuoteController@exportPDFSeal');
    Route::post('/quotes/bulkdelete', 'QuoteController@bulkdelete');
    Route::get('/options/thongbao', 'OptionController@thongbao');
    Route::get('/users/getalluser', 'UserController@getalluser');
    Route::get('/users/find', 'UserController@find');
    Route::get('/imports/exportreport', 'ImportController@exportreport');
    Route::get('/imports/exportreportdetail', 'ImportController@exportreportdetail');
    Route::post('/users/filter', 'UserController@filter');
    Route::get('/roles/{role}/users', 'RoleController@users');
    Route::post('/roles/{role}/users', 'RoleController@storeUser');
    Route::post('/roles/{role}/allusers', 'RoleController@storeAllUser');
    Route::delete('/roles/{role}/users/{user}', 'RoleController@destroyUser');
    Route::get('/roles/{role}/permissions', 'RoleController@permissions');
    Route::put('/roles/{role}/permissions', 'RoleController@updatePermissions');
    Route::put('/roles/{role}/allpermissions', 'RoleController@updateAllPermissions');
    Route::get('/orders/{id}/exportExcel', 'OrderController@exportExcel');
	Route::get('/orders/{id}/exportPDF', 'OrderController@exportPDF');
    Route::get('/orders/{id}/exportExcelUpdate', 'OrderController@exportExcelUpdatePrice');
    Route::post('/orders/importExcel', 'OrderController@importExcel');
    Route::post('/orders/updateLock', 'OrderController@updateLock');
    Route::post('/orders/updateInvisible', 'OrderController@updateInvisible');
    Route::post('/orders/updateCompleted', 'OrderController@updateCompleted');
    Route::post('/orders/uploadImage', 'OrderController@uploadImage');

    Route::get('/customers/getallcustomer', 'CustomerController@getallcustomer');
    Route::get('/products/getallproduct', 'ProductController@getallproduct');
    Route::get('/products/exportexcellogs', 'ProductController@exportexcellogs');
    Route::post('/imports/uploadexcelnhapkhodssanpham', 'ImportController@uploadexcelnhapkhodssanpham');
    Route::post('/categories/uploadExcel', 'CategoryController@uploadExcel');
    Route::get('/categories/getallcategories', 'CategoryController@getallcategories');
	
	Route::get('home/dashboard', 'DashboardController@index')->name('dashboard.index')->middleware('dashboard.permission:dashboard_read');
	
    Route::resource('/users', 'UserController');
    Route::resource('/products', 'ProductController', ['only'=> ['index','create','store', 'update']]);
    Route::resource('/options', 'OptionController');
    Route::resource('/customers', 'CustomerController');
    Route::resource('/exports', 'ExportController');
    Route::resource('/imports', 'ImportController');
    Route::resource('/orders', 'OrderController');
    Route::resource('/customerpays', 'CustomerPayController');
    Route::resource('/debts', 'DebtController');
    Route::resource('/quotes', 'QuoteController');
    Route::resource('/roles', 'RoleController');
    Route::resource('/jobs', 'JobController');
    Route::resource('/categories', 'CategoryController');
});