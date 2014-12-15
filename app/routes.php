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
	return View::make('hello');
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

Route::resource('scheme', 'SchemeController');
Route::resource('activity', 'ActivityController');

Route::get('api/customers', 'api\CustomerController@index');