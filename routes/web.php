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

// Route::get('/', function () {
//     return view('welcome');
// });

Auth::routes();

Route::get('/home', 'HomeController@index');

// Route::group( [ 'middleware' => 'auth' ], function () {
	//checkout
	Route::get( '/','SubscriptionController@index');
	Route::get( '/checkout', array('as' => 'maincheckout','uses' => 'SubscriptionController@index' ));
	Route::post( '/checkout',array('as' => 'addmoney.braintree', 'uses' => 'SubscriptionController@checkout'));

	// paypal
	Route::post('paypal', array('as' => 'addmoney.paypal','uses' => 'PaypalController@postPaymentWithpaypal',));
	Route::get('paypal', array('as' => 'payment.status','uses' => 'PaypalController@getPaymentStatus',));
	// 2checkout
	Route::post('2checkout', array('as' => 'addmoney.2checkout','uses' => 'SubscriptionController@postPaymentWith2checkout',));
	// receipt
	Route::get('receipt', array('as' => 'payment.receipt','uses' => 'SubscriptionController@showReceipt',));

// });
