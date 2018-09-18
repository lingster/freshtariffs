<?php

Route::pattern('companyId', '[0-9]*');
Route::pattern('typeId', '[0-9]*');
Route::pattern('userId', '[0-9]*');
Route::pattern('serviceId', '[0-9]*');
Route::pattern('invoiceId', '[A-Za-z0-9_\-]*');
Route::pattern('priceId', '[a-z0-9\-]*');

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get ('/', ['uses' => 'HomeController@index', 'before' => 'auth']);
Route::get ('/test', ['uses' => 'HomeController@test']);

Route::get ('/users/login', ['uses' => 'UsersController@login', 'before' => 'guest']);
Route::post('/users/login', ['uses' => 'UsersController@loginSubmit', 'before' => 'guest|csrf']);
Route::get ('/users/logout', ['uses' => 'UsersController@logout', 'before' => 'auth|csrf']);
Route::get ('/users/create', ['uses' => 'UsersController@create', 'before' => 'guest']);
Route::post('/users/create', ['uses' => 'UsersController@createSubmit', 'before' => 'guest|csrf']);

Route::get ('/admin', ['uses' => 'AdminController@index', 'before' => 'admin']);
Route::get ('/admin/settings', ['uses' => 'AdminController@settings', 'before' => 'admin']);
Route::post('/admin/settings', ['uses' => 'AdminController@settingsSubmit', 'before' => 'admin|csrf']);
Route::get ('/admin/customtypes', ['uses' => 'AdminController@customTypes', 'before' => 'admin']);
Route::get ('/admin/customtypes/create', ['uses' => 'AdminController@customTypesCreate', 'before' => 'admin']);
Route::post('/admin/customtypes/create', ['uses' => 'AdminController@customTypesCreateSubmit', 'before' => 'admin|csrf']);
Route::get ('/admin/customtypes/{typeId}/edit', ['uses' => 'AdminController@customTypesEdit', 'before' => 'admin']);
Route::post('/admin/customtypes/{typeId}/edit', ['uses' => 'AdminController@customTypesEditSubmit', 'before' => 'admin|csrf']);
Route::get ('/admin/customtypes/{typeId}/delete', ['uses' => 'AdminController@customTypesDelete', 'before' => 'admin|csrf']);
Route::get ('/admin/companies', ['uses' => 'AdminController@companies', 'before' => 'admin']);
Route::get ('/admin/companies/create', ['uses' => 'AdminController@companiesCreate', 'before' => 'admin']);
Route::post('/admin/companies/create', ['uses' => 'AdminController@companiesCreateSubmit', 'before' => 'admin|csrf']);
Route::get ('/admin/companies/{companyId}/edit', ['uses' => 'AdminController@companiesEdit', 'before' => 'admin']);
Route::post('/admin/companies/{companyId}/edit', ['uses' => 'AdminController@companiesEditSubmit', 'before' => 'admin|csrf']);
Route::get ('/admin/companies/{companyId}/delete', ['uses' => 'AdminController@companiesDelete', 'before' => 'admin|csrf']);
Route::get ('/admin/destinations', ['uses' => 'AdminController@destinations', 'before' => 'admin']);
Route::post('/admin/destinations', ['uses' => 'AdminController@destinationsSubmit', 'before' => 'admin|csrf']);
Route::post('/admin/destinations/add', ['uses' => 'AdminController@destinationsAddSubmit', 'before' => 'admin|csrf']);
Route::get ('/admin/destinations/import', ['uses' => 'AdminController@destinationsImport', 'before' => 'admin']);
Route::post('/admin/destinations/import', ['uses' => 'AdminController@destinationsImportSubmit', 'before' => 'admin|csrf']);
Route::get ('/admin/destinations/search', ['uses' => 'AdminController@destinationsSearch', 'before' => 'admin']);
Route::get ('/admin/destinations/download', ['uses' => 'AdminController@destinationsDownload', 'before' => 'admin']);
Route::get ('/admin/users/{companyId}', ['uses' => 'AdminController@users', 'before' => 'admin']);
Route::get ('/admin/users/{companyId}/create', ['uses' => 'AdminController@usersCreate', 'before' => 'admin']);
Route::post('/admin/users/{companyId}/create', ['uses' => 'AdminController@usersCreateSubmit', 'before' => 'admin|csrf']);
Route::get ('/admin/users/{userId}/edit', ['uses' => 'AdminController@usersEdit', 'before' => 'admin']);
Route::post('/admin/users/{userId}/edit', ['uses' => 'AdminController@usersEditSubmit', 'before' => 'admin|csrf']);
Route::get ('/admin/users/{userId}/delete', ['uses' => 'AdminController@usersDelete', 'before' => 'admin|csrf']);
Route::get ('/admin/price/{userId}', ['uses' => 'AdminController@price', 'before' => 'admin']);
Route::post('/admin/price/{userId}', ['uses' => 'AdminController@priceSubmit', 'before' => 'admin|csrf']);
Route::get ('/admin/price/{userId}/xls', ['uses' => 'AdminController@priceXls', 'before' => 'admin']);
Route::post('/admin/price/{userId}/xls', ['uses' => 'AdminController@priceXlsSubmit', 'before' => 'admin|csrf']);
Route::get ('/admin/test', ['uses' => 'AdminController@test', 'before' => 'admin']);

Route::get ('/staff/dashboard', ['uses' => 'StaffController@dashboard', 'before' => 'staff']);
Route::get ('/staff/customers', ['uses' => 'StaffController@customers', 'before' => 'staff']);
Route::get ('/staff/customers/create', ['uses' => 'StaffController@customersCreate', 'before' => 'staff']);
Route::post('/staff/customers/create', ['uses' => 'StaffController@customersCreateSubmit', 'before' => 'staff|csrf']);
Route::get ('/staff/customers/{userId}/edit', ['uses' => 'StaffController@customersEdit', 'before' => 'staff']);
Route::post('/staff/customers/{userId}/edit', ['uses' => 'StaffController@customersEditSubmit', 'before' => 'staff|csrf']);
Route::get ('/staff/customers/{userId}/delete', ['uses' => 'StaffController@customersDelete', 'before' => 'staff|csrf']);
Route::get ('/staff/pricelists', ['uses' => 'StaffController@pricelists', 'before' => 'staff']);
Route::get ('/staff/settings', ['uses' => 'StaffController@settings', 'before' => 'staff']);
Route::post('/staff/settings', ['uses' => 'StaffController@settingsSubmit', 'before' => 'staff|csrf']);
Route::get ('/staff/settings/integrations', ['uses' => 'StaffController@integrations', 'before' => 'staff']);
Route::any ('/staff/settings/integrations/connect/{serviceId}', ['uses' => 'StaffController@integrationsConnect', 'before' => 'staff|csrf']);
Route::get ('/staff/settings/integrations/disconnect/{serviceId}', ['uses' => 'StaffController@integrationsDisconnect', 'before' => 'staff|csrf']);
Route::get ('/staff/settings/integrations/callback/{serviceId}', ['uses' => 'StaffController@integrationsCallback', 'before' => 'staff']);
Route::get ('/staff/settings/integrations/freshbooks', ['uses' => 'StaffController@integrationsFreshbooks', 'before' => 'staff']);
Route::post('/staff/settings/integrations/freshbooks', ['uses' => 'StaffController@integrationsFreshbooksSubmit', 'before' => 'staff|csrf']);
Route::get ('/staff/pricelist/{userId}', ['uses' => 'StaffController@pricelist', 'before' => 'staff']);
Route::post('/staff/pricelist/{userId}', ['uses' => 'StaffController@pricelistSubmit', 'before' => 'staff|csrf']);
Route::get ('/staff/pricelist/{userId}/xls', ['uses' => 'StaffController@pricelistXls', 'before' => 'staff']);
Route::post('/staff/pricelist/{userId}/xls', ['uses' => 'StaffController@pricelistXlsSubmit', 'before' => 'staff|csrf']);
Route::get ('/staff/subscription', ['uses' => 'StaffController@subscription', 'before' => 'staff']);
Route::get ('/staff/subscription/cancel', ['uses' => 'StaffController@subscriptionCancel', 'before' => 'staff|subscribed|csrf']);
Route::get ('/staff/subscription/invoices', ['uses' => 'StaffController@subscriptionInvoices', 'before' => 'staff']);
Route::get ('/staff/subscription/invoices/{invoiceId}/download', ['uses' => 'StaffController@subscriptionInvoicesDownload', 'before' => 'staff']);

Route::get ('/customer/dashboard', ['uses' => 'CustomerController@dashboard', 'before' => 'customer']);
    
Route::get ('/api/destinations', ['uses' => 'APIController@destinations', 'before' => 'csrf']);
Route::get ('/api/destinations/country', ['uses' => 'APIController@destinationsCountry', 'before' => 'csrf']);
Route::post('/api/subscription', ['uses' => 'APIController@subscription', 'before' => 'staff|csrf']);
Route::post('/api/subscription/swap', ['uses' => 'APIController@subscriptionSwap', 'before' => 'subscribed|staff|csrf']);

/** API section */
Route::group(['prefix' => '/api/v1'], function () {
    Route::get ('customers', ['uses' => 'API_v1_CustomerController@all']);
    Route::put ('customers', ['uses' => 'API_v1_CustomerController@create']);
    Route::get ('customers/{userId}', ['uses' => 'API_v1_CustomerController@show']);
    Route::post('customers/{userId}', ['uses' => 'API_v1_CustomerController@update']);
    Route::delete('customers/{userId}', ['uses' => 'API_v1_CustomerController@delete']);

    Route::get ('staff', ['uses' => 'API_v1_StaffController@all']);
    Route::put ('staff', ['uses' => 'API_v1_StaffController@create']);
    Route::get ('staff/{userId}', ['uses' => 'API_v1_StaffController@show']);
    Route::post('staff/{userId}', ['uses' => 'API_v1_StaffController@update']);
    
    Route::get ('destinations', ['uses' => 'API_v1_DestinationController@all']);
    Route::post('destinations/search', ['uses' => 'API_v1_DestinationController@search']);

    Route::get ('pricelists', ['uses' => 'API_v1_PricelistController@all']);
    Route::get ('pricelists/{priceId}', ['uses' => 'API_v1_PricelistController@show']);
    Route::put ('pricelists', ['uses' => 'API_v1_PricelistController@create']);
});