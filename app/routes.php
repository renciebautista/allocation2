<?php
use Imagecow\Image;


use Box\Spout\Writer\WriterFactory;
use Box\Spout\Common\Type;


Queue::getIron()->ssl_verifypeer = false;
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


Route::get('test', function(){
	// // $activity = Activity::find(699);
	// // $tradedeal = Tradedeal::getActivityTradeDeal($activity);
	// // if($tradedeal != null){
	// // 	$tradedealschemes = TradedealScheme::getScheme($tradedeal->id);
	// // }

	// // return View::make('pdf.tradedeal_view',compact('tradedealschemes'));

	// $data = \Customer::getChannelCustomerList();

	// Helper::debug($data);
	$data = '[{
			"id":1,
			"name":"C",
			"size":"",
			"date":"02/19/2010",
			"children":[{
				"id":2,
				"name":"Program Files",
				"size":"120 MB",
				"date":"03/20/2010",
				"children":[{
					"id":21,
					"name":"Java",
					"size":"",
					"date":"01/13/2010",
					"state":"closed",
					"children":[{
						"id":211,
						"name":"java.exe",
						"size":"142 KB",
						"date":"01/13/2010"
					},{
						"id":212,
						"name":"jawt.dll",
						"size":"5 KB",
						"date":"01/13/2010"
					}]
				},{
					"id":22,
					"name":"MySQL",
					"size":"",
					"date":"01/13/2010",
					"state":"closed",
					"children":[{
						"id":221,
						"name":"my.ini",
						"size":"10 KB",
						"date":"02/26/2009"
					},{
						"id":222,
						"name":"my-huge.ini",
						"size":"5 KB",
						"date":"02/26/2009"
					},{
						"id":223,
						"name":"my-large.ini",
						"size":"5 KB",
						"date":"02/26/2009"
					}]
				}]
			},{
				"id":3,
				"name":"eclipse",
				"size":"",
				"date":"01/20/2010",
				"children":[{
					"id":31,
					"name":"eclipse.exe",
					"size":"56 KB",
					"date":"05/19/2009"
				},{
					"id":32,
					"name":"eclipse.ini",
					"size":"1 KB",
					"date":"04/20/2010"
				},{
					"id":33,
					"name":"notice.html",
					"size":"7 KB",
					"date":"03/17/2005"
				}]
			}]
		}]';

		echo $data;
});
//---------------------------------------------------
Route::post('queue/massmail', function()
{
	return Queue::marshal();
});

Route::post('queue/sob', function()
{
	return Queue::marshal();
});

Route::post('queue/push', function()
{
	return Queue::marshal();
});


Route::post('queue/pdf', function()
{
	return Queue::marshal();
});

Route::post('queue/word', function()
{
	return Queue::marshal();
});


Route::post('queue/allocreport', function()
{
	return Queue::marshal();
});

Route::post('queue/scheme', function()
{
	return Queue::marshal();
});

Route::post('signup', ['as' => 'signup', 'uses' => 'LoginController@signup']);

Route::get('/','LoginController@index');
Route::get('login','LoginController@index');
Route::get('logout','LoginController@logout');
Route::post('login', 'LoginController@dologin');
Route::get('confirm/{code}', 'LoginController@confirm');


Route::get('forgotpassword','LoginController@forgotpassword');
Route::post('forgotpassword','LoginController@doforgotpassword');
Route::get('reset_password/{token}','LoginController@resetpassword');
Route::post('reset_password', 'LoginController@doResetPassword');

Route::get('downloadcycle/{id}', 'DownloadsController@downloadcycle');
Route::get('ar/{token}','AllocationReportController@download');

Route::get('check-session', 'LoginController@checkSession');

Route::group(array('before' => 'auth'), function()
{	
	// Route::pattern('id', '[0-9]+');

	Route::get('cycle/calendar', 'CycleController@calendar');

	Route::get('help', 'HelpController@index');

	Route::get('activity/{id}/active', 'ActivityController@active');
	Route::put('activity/{id}/setactive', 'ActivityController@setactive');

	Route::get('activity/{id}/timings', 'ActivityController@timings');
	Route::get('activity/{id}/activityroles', 'ActivityController@activityroles');

	Route::post('activity/{id}/updatetimings', 'ActivityController@updatetimings');
	
	Route::post('activity/{id}/updateactivity', 'ActivityController@updateactivity');
	Route::get('activity/{id}/recall', 'ActivityController@recall');

	Route::post('activity/{id}/addbudget', 'ActivityController@addbudget');
	Route::delete('activity/deletebudget', 'ActivityController@deletebudget');
	Route::put('activity/updatebudget', 'ActivityController@updatebudget');

	Route::get('activity/{id}/getpartskus', 'ActivityController@getpartskus');
	Route::get('activity/{id}/getpartskustable', 'ActivityController@getpartskustable');
	Route::get('activity/{id}/partsku', 'ActivityController@partsku');
	Route::post('activity/{id}/addpartskus', 'ActivityController@addpartskus');
	Route::post('activity/deletepartskus', 'ActivityController@deletepartskus');
	Route::post('activity/updatepartskus', 'ActivityController@updatepartskus');


	// Route::get('activity/{id}/tdchannels', 'ActivityController@tdchannels');
	Route::put('activity/{id}/updatetradedeal', 'ActivityController@updatetradedeal');
	Route::get('activity/{id}/exporttradedeal', 'ActivityController@exporttradedeal');
	Route::get('activity/{id}/exporttddetails', 'ActivityController@exporttddetails');
	Route::post('activity/deletetradedealscheme', 'ActivityController@deletetradedealscheme');

	Route::get('activity/{id}/createtradealscheme',['as' => 'activity.createtradealscheme', 'uses' => 'ActivityController@createtradealscheme']);
	Route::post('activity/{id}/storetradealscheme', ['as' => 'activity.storetradealscheme', 'uses' => 'ActivityController@storetradealscheme']);


	Route::get('tradedealscheme/{id}/exportle',['as' => 'tradedealscheme.exportle', 'uses' => 'TradealSchemeController@exportle']);
	Route::get('tradedealscheme/{id}/edit',['as' => 'tradedealscheme.edit', 'uses' => 'TradealSchemeController@edit']);
	Route::get('tradedealscheme/{id}',['as' => 'tradedealscheme.show', 'uses' => 'TradealSchemeController@show']);
	Route::put('tradedealscheme/{id}', ['as' => 'tradedealscheme.update', 'uses' => 'TradealSchemeController@update']);

	Route::post('activity/{id}/addnobudget', 'ActivityController@addnobudget');
	Route::delete('activity/deletenobudget', 'ActivityController@deletenobudget');
	Route::put('activity/updatenobudget', 'ActivityController@updatenobudget');

	Route::post('activity/{id}/addmaterial', 'ActivityController@addmaterial');
	Route::delete('activity/deletematerial', 'ActivityController@deletematerial');
	Route::put('activity/updatematerial', 'ActivityController@updatematerial');

	Route::put('activity/{id}/updatecustomer', 'ActivityController@updatecustomer');
	Route::put('activity/{id}/updatebilling', 'ActivityController@updatebilling');
	Route::put('activity/updateforcealloc', 'ActivityController@updateforcealloc');

	Route::get('activity/{id}/scheme', 'SchemeController@index');
	Route::get('activity/{id}/scheme/create', 'SchemeController@create');
	Route::post('activity/{id}/scheme', 'SchemeController@store');

	Route::post('activity/{id}/fdaupload', 'ActivityController@fdaupload');
	Route::delete('activity/{id}/fdadelete', 'ActivityController@fdadelete');
	Route::get('activity/{id}/fdadownload', 'ActivityController@fdadownload');

	Route::post('activity/{id}/fisupload', 'ActivityController@fisupload');
	Route::delete('activity/{id}/fisdelete', 'ActivityController@fisdelete');
	Route::get('activity/{id}/fisdownload', 'ActivityController@fisdownload');
	
	Route::post('activity/{id}/artworkupload', 'ActivityController@artworkupload');
	Route::post('activity/artworkdelete', 'ActivityController@artworkdelete');
	Route::get('activity/{id}/artworkdownload', 'ActivityController@artworkdownload');

	Route::post('activity/{id}/backgroundupload', 'ActivityController@backgroundupload');
	Route::post('activity/backgrounddelete', 'ActivityController@backgrounddelete');
	Route::get('activity/{id}/backgrounddownload', 'ActivityController@backgrounddownload');

	Route::post('activity/{id}/bandingupload', 'ActivityController@bandingupload');
	Route::post('activity/bandingdelete', 'ActivityController@bandingdelete');
	Route::get('activity/{id}/bandingdownload', 'ActivityController@bandingdownload');

	Route::get('activity/{id}/channels', 'ActivityController@channels');

	Route::post('activity/{id}/submittogcm', 'ActivityController@submittogcm');
	
	Route::get('activity/{id}/allocsummary', 'ActivityController@allocsummary');
	Route::get('activity/pistemplate', 'ActivityController@pistemplate');

	Route::post('activity/{id}/duplicate','ActivityController@duplicate');
	Route::get('activity/{id}/summary','ActivityController@summary');
	
	Route::resource('activity', 'ActivityController');
	
	
	Route::put('scheme/updatealloc', 'SchemeController@updateallocation');
	Route::get('scheme/{id}/gettemplate', 'SchemeController@gettemplate');
	Route::get('scheme/{id}/export', 'SchemeController@export');
	Route::get('scheme/{id}/exportsum', 'SchemeController@exportsum');
	Route::get('scheme/{id}/allocation', 'SchemeController@allocation');
	Route::get('scheme/{id}/edit', 'SchemeController@edit');
	Route::get('scheme/{id}/exportsob', 'SchemeController@exportsob');
	Route::get('scheme/{id}', 'SchemeController@show');
	Route::delete('scheme/{id}', 'SchemeController@destroy');
	Route::put('scheme/{id}', 'SchemeController@update');
	Route::post('scheme/{id}/updatesob', 'SchemeController@updatesob');
	Route::post('scheme/{id}/duplicate','SchemeController@duplicate');
	Route::post('scheme/{id}/duplicatescheme', 'SchemeController@duplicatescheme');


	// Route::post('downloadedactivity/{id}/submittogcm', 'DownloadedActivityController@submittogcm');
	// Route::resource('downloadedactivity', 'DownloadedActivityController');


	Route::post('submittedactivity/{id}/updateactivity', 'SubmittedActivityController@updateactivity');
	Route::get('submittedactivity/{id}/edit', 'SubmittedActivityController@edit');
	Route::get('submittedactivity', array('as' => 'submittedactivity.index', 'uses' => 'SubmittedActivityController@index'));
	// Route::get('submittedactivity', 'SubmittedActivityController@index');
	// Route::resource('submittedactivity', 'SubmittedActivityController');

	Route::get('downloads/letemplates', 'DownloadsController@letemplates');
	Route::get('downloads/{id}/downloadletemplates', 'DownloadsController@downloadletemplates');

	Route::get('downloads/cycles', 'DownloadsController@cycles');
	Route::get('downloads/{id}/released', 'DownloadsController@released');

	Route::get('dashboard', 'DashboardController@index');
	Route::get('dashboard/filters', 'DashboardController@filters');
	Route::post('dashboard/filters', 'DashboardController@savefilters');
	Route::get('dashboard/categoryselected', 'DashboardController@categoryselected');
	Route::get('dashboard/brandselected', 'DashboardController@brandselected');
	Route::get('dashboard/customerselected', 'DashboardController@customerselected');

	Route::get('profile','ProfileController@index');
	Route::post('profile','ProfileController@update');

	Route::get('changepassword', 'ProfileController@changepassword');
	Route::post('updatepassword', 'ProfileController@updatepassword');
	
	Route::get('reports/customer', 'AllocationReportController@customer');
	Route::get('reports/customerselected', 'AllocationReportController@customerselected');
	Route::get('reports/outletsselected', 'AllocationReportController@outletsselected');
	Route::get('reports/outlets', 'AllocationReportController@outlets');
	Route::get('reports/channels', 'AllocationReportController@channels');
	Route::get('reports/channelsselected', 'AllocationReportController@channelsselected');

	Route::get('reports/allocation', 'AllocationReportController@index');
	Route::get('reports/allocation/create', 'AllocationReportController@create');
	Route::post('reports/allocation/create', 'AllocationReportController@store');
	Route::get('reports/allocation/{id}/history', 'AllocationReportController@history');
	Route::get('reports/allocation/{id}/generate', 'AllocationReportController@show');
	Route::post('reports/allocation/{id}/generate', 'AllocationReportController@generate');
	Route::get('reports/allocation/{id}', 'AllocationReportController@edit');
	Route::delete('reports/allocation/{id}', 'AllocationReportController@destroy');
	Route::put('reports/allocation/{id}', 'AllocationReportController@update');
	Route::post('reports/allocation/{id}/duplicate', 'AllocationReportController@duplicate');

	Route::get('reports/activities', ['as' => 'reports.activities', 'uses' => 'ReportController@activities']);
	Route::get('reports/{id}/preview', 'ReportController@preview');
	Route::get('reports/{id}/download', 'ReportController@download');
	Route::get('reports/{id}/document', 'ReportController@document');

	Route::get('sob', ['as' => 'sob.index', 'uses' => 'SobController@index']);
	Route::post('sob/generate', ['as' => 'sob.generate', 'uses' => 'SobController@generate']);

	Route::get('sob/download', ['as' => 'sob.download', 'uses' => 'SobController@download']);
	Route::post('sob/downloadreport', ['as' => 'sob.downloadreport', 'uses' => 'SobController@downloadreport']);
	

	// Route::get('sob/booking', ['as' => 'sob.booking', 'uses' => 'SobController@booking']);
	// Route::post('sob/booking', ['as' => 'sob.filterbooking', 'uses' => 'SobController@filterbooking']);
	// Route::get('sob/booking/{week}/{year}/{brand_code}/{type}', ['as' => 'sob.showbooking', 'uses' => 'SobController@showbooking']);
	// Route::get('sob/downloadbooking', ['as' => 'sob.downloadbooking', 'uses' => 'SobController@downloadbooking']);

	Route::get('activitytype/{id}/network/totalduration', 'NetworkController@totalduration');
	

	Route::resource('faq', 'FaqController');

	
	Route::get('images/{cycle_id}/{type_id}/{activity_id}/{name}', function($cycle_id = null,$type_id = null,$activity_id = null,$name = null)
	{
		$path = storage_path().'/uploads/'.$cycle_id.'/'. $type_id.'/'. $activity_id.'/'. $name;
		if (file_exists($path)) { 

			$image = Image::create($path);
			$image->resize(300, 200, 1);
			return $image->show();
		}
	});

	Route::get('fdapermit/{cycle_id}/{type_id}/{activity_id}/{name}', function($cycle_id = null,$type_id = null,$activity_id = null,$name = null)
	{
		$path = storage_path().'/uploads/'.$cycle_id.'/'. $type_id.'/'. $activity_id.'/'. $name;
		if (file_exists($path)) { 
			$image = Image::create($path);
			$image->resize(1000);
			return $image->show();
		}
	});

	Route::group(array('prefix' => 'api'), function()
	{

		Route::get('customerselected', 'api\CustomerController@customerselected');
		Route::get('getcustomers', 'api\CustomerController@getselectedcustomer');

		Route::get('getpostedcustomers', 'api\CustomerController@getpostedcustomers');
		Route::get('customers_old', 'api\CustomerController@index');
		Route::get('customers', 'api\CustomerController@getChannelCustomer');

		Route::get('cycles', 'CycleController@availableCycle');

		Route::get('getpostedchannels', 'api\ChannelController@getpostedchannels');
		Route::get('channelselected', 'api\ChannelController@channelselected');
		Route::get('channels', 'api\ChannelController@index');

		Route::post('category/getselected', 'api\SkuController@categoryselected');
		Route::post('category', 'api\SkuController@category');
		Route::post('sobcategory', 'api\SkuController@sobcategory');
		Route::post('sobbrand', 'api\SkuController@sobbrand');
		Route::post('categories', 'api\SkuController@categories');
		Route::post('brand', 'api\SkuController@brand');
		
		Route::post('brand/getselected', 'api\SkuController@brandselected');

		Route::post('skusinvolve', 'api\PriceListController@involve');
		Route::post('sku/skuselected', 'api\PriceListController@skuselected');

		Route::resource('network', 'api\NetworkController');
		Route::get('budgettype', 'api\BudgetTypeController@gettype');
		Route::get('materialsource', 'api\MaterialController@getsource');

		Route::get('hostsku', 'api\PriceListController@getHostSku');
		Route::get('refrencesku', 'api\SkuController@getReferenceSku');
		Route::get('premiumsku', 'api\PriceListController@getPremiumSku');

		Route::post('sobyears', 'api\SobController@years');
		Route::post('sobweeks', 'api\SobController@weeks');
		Route::post('sobweekactivitytype', 'api\SobController@weekactivitytype');
		Route::post('weekbrand', 'api\SobController@weekbrand');

		Route::get('pricelistsku', 'api\PriceListController@getSku');
		Route::get('tdpricelistsku', 'api\PriceListController@tdpricelistsku');
		Route::get('tdprepricelistsku', 'api\PriceListController@tdprepricelistsku');

		Route::get('tdchannels','api\TradeChannelController@index');
		Route::get('selectedtdchannels','api\TradeChannelController@selectedtdchannels');

		Route::get('tdpostedchannels','api\CustomerController@getpostedchannelcustomer');
	});//


	// admin only
	Route::group(array('before' => 'admin'), function(){
		Route::get('launchskus/upload','LaunchSkuController@upload');
		Route::post('launchskus/upload','LaunchSkuController@doupload');
		Route::post('launchskus/assignaccess','LaunchSkuController@assignaccess');
		Route::post('launchskus/removeaccess','LaunchSkuController@removeaccess');
		Route::post('launchskus/removeskus','LaunchSkuController@removeskus');
		Route::get('launchskus/export','LaunchSkuController@export');
		Route::resource('launchskus','LaunchSkuController');

		Route::resource('group', 'GroupController');

		Route::get('customerremap/export', 'CustomerRemapController@export');
		Route::get('customerremap/import', 'CustomerRemapController@import');
		Route::post('customerremap/upload', 'CustomerRemapController@upload');
		Route::resource('customerremap', 'CustomerRemapController');

		Route::get('customermaster', ['as' => 'customermaster.index', 'uses' => 'CustomerMasterController@index']);
		Route::post('customermaster', ['as' => 'customermaster.export', 'uses' => 'CustomerMasterController@export']);
		Route::get('customermaster/exportall', ['as' => 'customermaster.exportall', 'uses' => 'CustomerMasterController@exportall']);
		Route::get('customermaster/{id}/download', ['as' => 'customermaster.download', 'uses' => 'CustomerMasterController@download']);

		Route::get('sobfilter/export', 'SobfilterController@export');
		Route::get('sobfilter/import', 'SobfilterController@import');
		Route::post('sobfilter/upload', 'SobfilterController@upload');
		Route::resource('sobfilter', 'SobfilterController');


		Route::resource('sobgroup', 'SobGroupController');

		Route::get('shipto/export', 'ShiptoController@export');
		Route::get('shipto/import', 'ShiptoController@import');
		Route::post('shipto/upload', 'ShiptoController@upload');
		Route::resource('shipto', 'ShiptoController');

		Route::get('shiptoplantcode/export', 'ShiptoPlantCodeController@export');
		Route::get('shiptoplantcode/import', 'ShiptoPlantCodeController@import');
		Route::post('shiptoplantcode/upload', 'ShiptoPlantCodeController@upload');
		Route::resource('shiptoplantcode', 'ShiptoPlantCodeController');

		Route::get('customer/export', 'CustomerController@export');
		Route::get('customer/import', 'CustomerController@import');
		Route::post('customer/upload', 'CustomerController@upload');
		Route::resource('customer', 'CustomerController');

		Route::get('brand/export', 'BrandController@export');
		Route::get('brand/import', 'BrandController@import');
		Route::post('brand/upload', 'BrandController@upload');
		Route::resource('brand', 'BrandController');

		Route::get('topsku/export', 'TopskuController@export');
		Route::get('topsku/import', 'TopskuController@import');
		Route::post('topsku/upload', 'TopskuController@upload');
		Route::resource('topsku', 'TopskuController');

		Route::get('pricelist/export', 'PricelistController@export');
		Route::get('pricelist/import', 'PricelistController@import');
		Route::post('pricelist/upload', 'PricelistController@upload');
		Route::resource('pricelist', 'PricelistController');

		Route::get('area/export', 'AreaController@export');
		Route::get('area/import', 'AreaController@import');
		Route::post('area/upload', 'AreaController@upload');
		Route::resource('area', 'AreaController');

		Route::get('account/export', 'AccountController@export');
		Route::get('account/import', 'AccountController@import');
		Route::post('account/upload', 'AccountController@upload');
		Route::resource('account', 'AccountController');

		Route::get('motherchildsku/export', 'MotherchildskuController@export');
		Route::get('motherchildsku/import', 'MotherchildskuController@import');
		Route::post('motherchildsku/upload', 'MotherchildskuController@upload');
		Route::resource('motherchildsku', 'MotherchildskuController');

		Route::get('channel/export', 'ChannelController@export');
		Route::get('channel/import', 'ChannelController@import');
		Route::post('channel/upload', 'ChannelController@upload');
		Route::resource('channel', 'ChannelController');

		// Route::get('subchannel/export', 'SubchannelController@export');
		// Route::get('subchannel/import', 'SubchannelController@import');
		// Route::post('subchannel/upload', 'SubchannelController@upload');
		// Route::resource('subchannel', 'SubchannelController');

		Route::get('holidays/getlist', 'HolidaysController@getlist');
		Route::resource('holidays', 'HolidaysController');

		Route::get('users/exportuser', 'UsersController@exportuser');
		Route::get('users/forapproval', 'UsersController@forapproval');
		Route::get('users/{id}/approve', 'UsersController@approve');
		Route::post('users/{id}/setapprove', 'UsersController@setapprove');
		Route::post('users/{id}/deny', 'UsersController@deny');
		Route::resource('users', 'UsersController');
		
		Route::post('cycle/rerun', 'CycleController@rerun');

		Route::resource('cycle', 'CycleController');


		Route::get('activitytype/{id}/network', 'NetworkController@index');
		Route::get('activitytype/{id}/network/list', 'NetworkController@show');
		Route::get('activitytype/{id}/network/dependon', 'NetworkController@dependOn');
		Route::post('activitytype/{id}/network/create', 'NetworkController@store');
		Route::post('network/delete', 'NetworkController@destroy');
		Route::get('network/edit', 'NetworkController@edit');
		Route::post('network/update', 'NetworkController@update');
		
		Route::post('activitytype/{id}/duplicate', 'ActivityTypeController@duplicate');
		Route::resource('activitytype', 'ActivityTypeController');

		Route::get('downloads/{id}/all', 'DownloadsController@downloadall');
		Route::get('downloads/{id}/approved', 'DownloadsController@download');

		Route::get('settings', ['as' => 'settings.index', 'uses' => 'SettingsController@index']);
		Route::post('settings', ['as' => 'settings.update', 'uses' => 'SettingsController@update']);

		Route::resource('sobholiday', 'SobholidaysController');

		Route::get('reports/{id}/review', ['as' => 'reports.review', 'uses' => 'ReportController@review']);
		Route::get('reports/{id}/scheme/', ['as' => 'reports.scheme', 'uses' => 'ReportController@scheme']);


		Route::get('accountgroup/export', 'AccountGroupController@export');
		Route::get('accountgroup/import', 'AccountGroupController@import');
		Route::post('accountgroup/upload', 'AccountGroupController@upload');
		Route::resource('accountgroup', 'AccountGroupController');
	});

});
//

// Confide routes
// Route::get('users/create', 'Employee@create');
// Route::post('users', 'Employee@store');
// Route::get('users/login', 'Employee@login');
// Route::post('users/login', 'Employee@doLogin');
// Route::get('users/confirm/{code}', 'Employee@confirm');
// Route::get('users/forgot_password', 'Employee@forgotPassword');
// Route::post('users/forgot_password', 'Employee@doForgotPassword');
// Route::get('users/reset_password/{token}', 'Employee@resetPassword');
// Route::post('users/reset_password', 'Employee@doResetPassword');
// Route::get('users/logout', 'Employee@logout');
