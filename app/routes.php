<?php

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

Route::get('/', function()
{
	// return View::make('hello');
	return View::make('login.index');
});



Route::get('/customer', function(){
	$channels = DB::table('channels')->get();
	$data = DB::table('groups')->get();
	foreach ($data as $key => $value) {
		$areas = DB::table('areas')->where('group_code',$value->group_code)->orderBy('id')->get();
		foreach ($areas as $key2 => $_area) {
			$sold_tos = DB::table('customers')->where('area_code',$_area->area_code )->get();
			foreach ($sold_tos as $key3 => $shipto) {
				$ship_tos =  DB::table('ship_tos')->where('customer_code',$shipto->customer_code )->get();
				foreach ($ship_tos as $key4 => $outlet) {
					if($outlet->ship_to_code != ''){

						foreach ($channels as $channel) {
							$accounts = DB::table('accounts')
								->where('ship_to_code',$outlet->ship_to_code )
								->where('area_code',$_area->area_code)
								->where('channel_code',$channel->channel_code)
								->get();
								
							if(count($accounts)>0){
								$ship_tos[$key4]->channel = array('channel' => $channel->channel_name);
								$ship_tos[$key4]->accounts = $accounts;
							}
						}
					}
				}
				$sold_tos[$key3]->shiptos = $ship_tos;
			}
			$areas[$key2]->sold_tos = $sold_tos;

		}

		$data[$key]->areas = $areas;
	}
	return View::make('customer',compact('data'));
});

Route::get('login',function(){
	return View::make('login.index');
});

Route::post('login', 'LoginController@dologin');

Route::group(array('before' => 'auth'), function()
{	
	Route::get('activity/{id}/scheme', 'SchemeController@index');
	Route::get('activity/{id}/scheme/create', 'SchemeController@create');
	Route::resource('scheme', 'SchemeController');
	Route::resource('activity', 'ActivityController');
	Route::resource('group', 'GroupController');
	Route::resource('dashboard', 'DashboardController');

	Route::group(array('prefix' => 'api'), function()
	{
		Route::get('customers', 'api\CustomerController@index');
		Route::post('category', 'api\SkuController@category');
		Route::post('brand', 'api\SkuController@brand');
	});//

	// Confide routes
	Route::get('users', 'UsersController@index');
	Route::get('users/create', 'UsersController@create');
	Route::post('users', 'UsersController@store');
	Route::get('users/login', 'UsersController@login');
	Route::post('users/login', 'UsersController@doLogin');
	Route::get('users/confirm/{code}', 'UsersController@confirm');
	Route::get('users/forgot_password', 'UsersController@forgotPassword');
	Route::post('users/forgot_password', 'UsersController@doForgotPassword');
	Route::get('users/reset_password/{token}', 'UsersController@resetPassword');
	Route::post('users/reset_password', 'UsersController@doResetPassword');
	Route::get('users/logout', 'UsersController@logout');
});
