<?php
date_default_timezone_set('Africa/Cairo');

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

#Home
Route::get('/','HomeController@index');

#Login
Route::get('login','HomeController@login');
Route::post('login','HomeController@doLogin');

#Logout
Route::get('logout','HomeController@logout');

#Category
Route::get('category/{id}/{name}','HomeController@display_category');

#Detail
Route::get('detail/{name}/{id}','HomeController@details');

#Search
Route::get('search','HomeController@search');




/* Dashboard */
#Profile
Route::get('profile','HomeController@profile');
Route::post('profile','HomeController@doProfile');

#Category
Route::get('categories','CategoryController@index');
Route::get('categories/create','CategoryController@create');
Route::post('categories/create','CategoryController@store');
Route::get('categories/edit/{id}','CategoryController@edit');
Route::post('categories/edit/{id}','CategoryController@update');
Route::post('categories/del/{id}','CategoryController@destroy');

#Products
Route::get('products','ProductController@index');
Route::get('products/create','ProductController@create');
Route::post('products/create','ProductController@store');
Route::get('products/edit/{id}','ProductController@edit');
Route::post('products/edit/{id}','ProductController@update');
Route::post('products/del/{id}','ProductController@destroy');

#Invoices
Route::get('invoices','InvoiceController@index');
Route::get('invoices/create','InvoiceController@create');
Route::post('invoices/create','InvoiceController@store');
Route::get('invoices/edit/{id}','InvoiceController@edit');
Route::post('invoices/edit/{id}','InvoiceController@update');
Route::post('invoices/del/{id}','InvoiceController@destroy');
Route::get('invoices/del/sub/{id}','InvoiceController@destroy_pro');

#Expenses
Route::get('expenses','ExpensesController@index');
Route::post('expenses','ExpensesController@store');
Route::get('expenses/edit/{id}','ExpensesController@edit');
Route::post('expenses/edit/{id}','ExpensesController@update');
Route::post('expenses/del/{id}','ExpensesController@destroy');

#Balance
Route::get('balance','BalanceController@index');
Route::post('balance','BalanceController@store');
Route::get('balance/edit/{id}','BalanceController@edit');
Route::post('balance/edit/{id}','BalanceController@update');
Route::post('balance/del/{id}','BalanceController@destroy');

#Liabilities
Route::get('liabilities','LiabilitiesController@index');
Route::post('liabilities','LiabilitiesController@store');
Route::get('liabilities/edit/{id}','LiabilitiesController@edit');
Route::post('liabilities/edit/{id}','LiabilitiesController@update');
Route::post('liabilities/del/{id}','LiabilitiesController@destroy');

#Cash
Route::get('sales/cash','CashController@index');
Route::get('sales/cash/create','CashController@create');
Route::post('sales/cash/create','CashController@store');
Route::get('sales/cash/edit/{id}','CashController@edit');
Route::post('sales/cash/edit/{id}','CashController@update');
Route::post('sales/cash/del/{id}','CashController@destroy');

Route::post('sales/cash/search-pro','CashController@search_pro');
Route::post('sales/cash/add-to-cart','CashController@add_to_cart');
Route::post('sales/cash/add-to-cart/edit/{id}','CashController@add_to_cart_update');
Route::post('sales/cash/calc-total-cart','CashController@calc_total_cart');
Route::post('sales/cash/calc-total-cart/edit/{id}','CashController@calc_total_cart_update');

Route::post('sales/cash/remove-cart/{id}/','CashController@remove_from_cart');
Route::post('sales/cash/remove-cart/edit/{id}/{getid}','CashController@remove_from_cart_update');
Route::get('sales/cash/update-qty/{id}/{value}','CashController@update_qty');
Route::get('sales/cash/update-qty/edit/{id}/{value}','CashController@update_qty_update');

Route::get('sales/cash/invoice','CashController@invoice');
Route::get('sales/cash/invoice/{id}','CashController@invoice_id');

#Order
Route::get('sales/order/published','OrderController@index_published');
Route::get('sales/order/unpublished','OrderController@index_unpublished');
Route::get('sales/order/create','OrderController@create');
Route::post('sales/order/create','OrderController@store');
Route::get('sales/order/edit/{id}','OrderController@edit');
Route::post('sales/order/edit/{id}','OrderController@update');
Route::post('sales/order/del/{id}','OrderController@destroy');
Route::post('sales/order/del/unyet/{id}','OrderController@destroy_unyet');

Route::post('sales/order/search-pro','OrderController@search_pro');
Route::post('sales/order/add-to-cart','OrderController@add_to_cart');
Route::post('sales/order/add-to-cart/edit/{id}','OrderController@add_to_cart_update');
Route::post('sales/order/calc-total-cart','OrderController@calc_total_cart');
Route::post('sales/order/calc-total-cart/edit/{id}','OrderController@calc_total_cart_update');

Route::post('sales/order/remove-cart/{id}/','OrderController@remove_from_cart');
Route::post('sales/order/remove-cart/edit/{id}/{getid}','OrderController@remove_from_cart_update');
Route::get('sales/order/update-qty/{id}/{value}','OrderController@update_qty');
Route::get('sales/order/update-qty/edit/{id}/{value}','OrderController@update_qty_update');

Route::get('sales/order/view/{id}','OrderController@view_order');
Route::post('sales/order/pay/remain/{id}','OrderController@order_pay_remain');

Route::get('sales/order/invoice/{id}','OrderController@order_invoice');

#Installment
Route::get('sales/installment/published','InstallmentController@index_published');
Route::get('sales/installment/unpublished','InstallmentController@index_unpublished');
Route::get('sales/installment/create','InstallmentController@create');
Route::post('sales/installment/create','InstallmentController@store');
Route::get('sales/installment/edit/{id}','InstallmentController@edit');
Route::post('sales/installment/edit/{id}','InstallmentController@update');
Route::post('sales/installment/del/{id}','InstallmentController@destroy');
Route::post('sales/installment/del/unyet/{id}','InstallmentController@destroy_unyet');

Route::post('sales/installment/search-pro','InstallmentController@search_pro');
Route::post('sales/installment/add-to-cart','InstallmentController@add_to_cart');
Route::post('sales/installment/add-to-cart/edit/{id}','InstallmentController@add_to_cart_update');
Route::post('sales/installment/calc-total-cart','InstallmentController@calc_total_cart');
Route::post('sales/installment/calc-total-cart/edit/{id}','InstallmentController@calc_total_cart_update');

Route::post('sales/installment/remove-cart/{id}/','InstallmentController@remove_from_cart');
Route::post('sales/installment/remove-cart/edit/{id}/{getid}','InstallmentController@remove_from_cart_update');
Route::get('sales/installment/update-qty/{id}/{value}','InstallmentController@update_qty');
Route::get('sales/installment/update-qty/edit/{id}/{value}','InstallmentController@update_qty_update');

Route::get('sales/installment/view/{id}','InstallmentController@view_order');
Route::get('sales/installment/pay/{id}/{m}','InstallmentController@pay_inst');
Route::post('sales/installment/update/inst/{id}','InstallmentController@updated_inst');

Route::get('sales/installment/invoice/{id}','InstallmentController@invoice');



#Customer
Route::get('customer','CustomerController@index');
Route::get('customer/create','CustomerController@create');
Route::post('customer/create','CustomerController@store');
Route::get('customer/edit/{id}','CustomerController@edit');
Route::post('customer/edit/{id}','CustomerController@update');
Route::post('customer/del/{id}','CustomerController@destory');

Route::get('customer/{id}/new/row','CustomerController@add_row');
Route::post('customer/{id}/update/row','CustomerController@update_row');
Route::get('customer/{id}/delete/row','CustomerController@delete_row');

Route::get('customer/{id}/new/fatora','CustomerController@add_fatora');
Route::post('customer/{id}/update/fatora','CustomerController@update_fatora');
Route::get('customer/{id}/delete/fatora','CustomerController@delete_fatora');

//Route::get('customer/invoice','CustomerController@invoice');
Route::get('customer/invoice/{id}','CustomerController@invoice_id');

#moward
Route::get('moward','MowardController@index');
Route::get('moward/create','MowardController@create');
Route::post('moward/create','MowardController@store');
Route::get('moward/edit/{id}','MowardController@edit');
Route::post('moward/edit/{id}','MowardController@update');
Route::post('moward/del/{id}','MowardController@destory');

Route::get('moward/{id}/new/row','MowardController@add_row');
Route::post('moward/{id}/update/row','MowardController@update_row');
Route::get('moward/{id}/delete/row','MowardController@delete_row');

Route::get('moward/{id}/new/fatora','MowardController@add_fatora');
Route::post('moward/{id}/update/fatora','MowardController@update_fatora');
Route::get('moward/{id}/delete/fatora','MowardController@delete_fatora');

#Reports
Route::get('report/products','ReportController@report_products');
Route::get('report/invoices','ReportController@report_invoices');
Route::get('report/expenses','ReportController@report_expenses');
Route::get('report/balance','ReportController@report_balance');
Route::get('report/cash','ReportController@report_cash');
Route::get('report/cash/check/{id}','ReportController@report_cash_check');
Route::get('report/order','ReportController@report_order');
Route::get('report/order/check/{id}','ReportController@report_order_check');
Route::get('report/installment','ReportController@report_installment');
Route::get('report/installment/check/{id}','ReportController@report_installment_check');
Route::get('report/customer','ReportController@report_customer');

#Reports Dailly
Route::get('reportDaily/products','ReportsDailyController@report_products');
Route::get('reportDaily/invoices','ReportsDailyController@report_invoices');
Route::get('reportDaily/expenses','ReportsDailyController@report_expenses');
Route::get('reportDaily/balance','ReportsDailyController@report_balance');
Route::get('reportDaily/cash','ReportsDailyController@report_cash');
Route::get('reportDaily/cash/check/{id}','ReportsDailyController@report_cash_check');
Route::get('reportDaily/order','ReportsDailyController@report_order');
Route::get('reportDaily/order/check/{id}','ReportsDailyController@report_order_check');
Route::get('reportDaily/installment','ReportsDailyController@report_installment');
Route::get('reportDaily/installment/check/{id}','ReportsDailyController@report_installment_check');
Route::get('reportDaily/customer','ReportsDailyController@report_customer');
