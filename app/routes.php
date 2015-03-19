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

// Route::get('/customer', function(){
// 	$channels = DB::table('channels')->get();
// 	$data = DB::table('groups')->get();
// 	foreach ($data as $key => $value) {
// 		$areas = DB::table('areas')->where('group_code',$value->group_code)->orderBy('id')->get();
// 		foreach ($areas as $key2 => $_area) {
// 			$sold_tos = DB::table('customers')->where('area_code',$_area->area_code )->get();
// 			foreach ($sold_tos as $key3 => $shipto) {
// 				$ship_tos =  DB::table('ship_tos')->where('customer_code',$shipto->customer_code )->get();
// 				foreach ($ship_tos as $key4 => $outlet) {
// 					if($outlet->ship_to_code != ''){

// 						foreach ($channels as $channel) {
// 							$accounts = DB::table('accounts')
// 								->where('ship_to_code',$outlet->ship_to_code )
// 								->where('area_code',$_area->area_code)
// 								->where('channel_code',$channel->channel_code)
// 								->get();
								
// 							if(count($accounts)>0){
// 								$ship_tos[$key4]->channel = array('channel' => $channel->channel_name);
// 								$ship_tos[$key4]->accounts = $accounts;
// 							}
// 						}
// 					}
// 				}
// 				$sold_tos[$key3]->shiptos = $ship_tos;
// 			}
// 			$areas[$key2]->sold_tos = $sold_tos;

// 		}

// 		$data[$key]->areas = $areas;
// 	}
// 	return View::make('customer',compact('data'));
// });


Route::get('print', function (){
	// echo SchemeAllocation::finalallocation(35);
	 $scheme = Scheme::find(35);
	 echo $scheme->activity->activitytype->uom;
	// $activity = Activity::find(68);
	// $scheme_customers = SchemeAllocation::getCustomers($activity->id);
	// $schemes = Scheme::where('activity_id', $activity->id)
	// 			->orderBy('created_at', 'desc')
	// 			->get();
	// $scheme_allcations = SchemeAllocation::getAllocation($activity->id);

	// return View::make('pdf.test2',compact('activity', 'scheme_customers', 'schemes', 'scheme_allcations'));
});

Route::get('/','LoginController@index');
Route::get('login','LoginController@index');
Route::get('logout','LoginController@logout');

Route::post('login', 'LoginController@dologin');

Route::group(array('before' => 'auth'), function()
{	
	Route::pattern('id', '[0-9]+');

	Route::get('activity/{id}/timings', 'ActivityController@timings');

	Route::get('activity/{id}/download', 'ActivityController@download');
	Route::get('activity/{id}/recall', 'ActivityController@recall');

	Route::post('activity/{id}/addbudget', 'ActivityController@addbudget');
	Route::delete('activity/deletebudget', 'ActivityController@deletebudget');
	Route::put('activity/updatebudget', 'ActivityController@updatebudget');

	Route::post('activity/{id}/addnobudget', 'ActivityController@addnobudget');
	Route::delete('activity/deletenobudget', 'ActivityController@deletenobudget');
	Route::put('activity/updatenobudget', 'ActivityController@updatenobudget');

	Route::post('activity/{id}/addmaterial', 'ActivityController@addmaterial');
	Route::delete('activity/deletematerial', 'ActivityController@deletematerial');
	Route::put('activity/updatematerial', 'ActivityController@updatematerial');

	Route::put('activity/{id}/updatecustomer', 'ActivityController@updatecustomer');
	Route::put('activity/{id}/updatebilling', 'ActivityController@updatebilling');

	Route::get('activity/{id}/scheme', 'SchemeController@index');
	Route::get('activity/{id}/scheme/create', 'SchemeController@create');
	Route::post('activity/{id}/scheme', 'SchemeController@store');
	Route::resource('activity', 'ActivityController');
	

	Route::get('scheme/{id}/allocation', 'SchemeController@allocation');
	Route::get('scheme/{id}', 'SchemeController@show');
	Route::get('scheme/{id}/edit', 'SchemeController@edit');
	Route::delete('scheme/{id}', 'SchemeController@destroy');
	Route::put('scheme/{id}', 'SchemeController@update');
	Route::put('scheme/updatealloc', 'SchemeController@updateallocation');


	Route::post('downloadedactivity/{id}/upload', 'DownloadedActivityController@doupload');
	Route::get('downloadedactivity/{id}/downloadfile', 'DownloadedActivityController@downloadfile');
	Route::get('downloadedactivity/nobudget', 'DownloadedActivityController@nobudget');


	Route::resource('downloadedactivity', 'DownloadedActivityController');
	Route::resource('submittedactivity', 'SubmittedActivityController');
	
	Route::resource('group', 'GroupController');
	Route::resource('dashboard', 'DashboardController');
	Route::get('profile','ProfileController@index');

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

	Route::resource('cycle', 'CycleController');

	Route::get('activitytype/{id}/network', 'NetworkController@index');
	Route::get('activitytype/{id}/network/list', 'NetworkController@show');
	Route::get('activitytype/{id}/network/dependon', 'NetworkController@dependOn');
	Route::get('activitytype/{id}/network/totalduration', 'NetworkController@totalduration');
	Route::post('activitytype/{id}/network/create', 'NetworkController@store');


	Route::resource('activitytype', 'ActivityTypeController');


	Route::group(array('prefix' => 'api'), function()
	{
		Route::get('customerselected', 'api\CustomerController@customerselected');
		Route::get('customers', 'api\CustomerController@index');
		

		Route::post('category/getselected', 'api\SkuController@categoryselected');
		Route::post('category', 'api\SkuController@category');
		Route::post('brand', 'api\SkuController@brand');
		Route::post('brand/getselected', 'api\SkuController@brandselected');
		Route::resource('network', 'api\NetworkController');
		Route::get('budgettype', 'api\BudgetTypeController@gettype');
		Route::get('materialsource', 'api\MaterialController@getsource');
	});//

});
