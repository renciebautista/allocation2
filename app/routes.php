<?php
use Imagecow\Image;
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


Route::get("testmail", function(){
	$user = User::find(1);
	$cycles = Cycle::getBySubmissionDeadline();
	$cycle_ids = array();
	foreach ($cycles as $value) {
		$cycle_ids[] = $value->id;
	}
	$data['cycles'] = $cycles;
	$data['user'] = $user->getFullname();
	$data['email'] = $user->email;
	$data['fullname'] = $user->getFullname();
	$data['cycle_ids'] = $cycle_ids;
	$data['activities'] = Activity::ProponentActivitiesForApproval($user->id,$cycle_ids);
	// Mail::send('emails.mail1', $data, function($message) use ($data){
	// 	$message->to("rbautista@chasetech.com", $data['fullname'])->subject('TOP ACTIVITY STATUS');
	// });

	return View::make('emails.mail1',$data);
});

Route::get("test", function(){
	if($_ENV['MAIL_TEST']){
		echo "test mail";
	}else{
		echo "live mail";
	}
});


Route::get("mail1", function(){
	$users = User::GetPlanners(['PROPONENT' ,'PMOG']);
	$cycles = Cycle::getBySubmissionDeadline();
	$cycle_ids = array();
	foreach ($cycles as $value) {
		$cycle_ids[] = $value->id;
	}

	foreach ($users as $user) {
		$data = array();
		$data['user_id'] = $user->id;
		$data['cycles'] = $cycles;
		$data['user'] = $user->getFullname();
		$data['email'] = $user->email;
		$data['fullname'] = $user->getFullname();
		$data['cycle_ids'] = $cycle_ids;
		if($user->role_id == 2){
			$data['activities'] = Activity::ProponentActivitiesForApproval($user->user_id,$cycle_ids);
		}
		if($user->role_id == 3){
			$data['activities'] = Activity::PmogActivitiesForApproval($user->user_id,$cycle_ids);
		}

		// if(count($data['activities']) > 0){
		// 	if($_ENV['MAIL_TEST']){
		// 		Mail::send('emails.mail1', $data, function($message) use ($data){
		// 			$message->to("rbautista@chasetech.com", $data['fullname'])->subject('TOP ACTIVITY STATUS');
		// 		});
		// 	}else{
		// 		// Mail::send('emails.mail1', $data, function($message) use ($data){
		// 		// 	$message->to($data['email'], $data['fullname'])->subject('TOP ACTIVITY STATUS');
		// 		// });
		// 	}
			
		// }

		Helper::print_r($data);
		
	}

});

Route::get("mail2", function(){
	$users = User::GetPlanners(['GCOM APPROVER','CD OPS APPROVER','CMD DIRECTOR']);
	$cycles = Cycle::getByApprovalDeadline();
	$cycle_ids = array();
	foreach ($cycles as $value) {
		$cycle_ids[] = $value->id;
	}

	foreach ($users as $user) {
		$data['cycles'] = $cycles;
		$data['user'] = $user->getFullname();
		$data['email'] = $user->email;
		$data['fullname'] = $user->getFullname();
		$data['cycle_ids'] = $cycle_ids;
		$data['activities'] = Activity::ApproverActivitiesForApproval($user->id,$cycle_ids);

		if(count($data['activities']) > 0){
			if($_ENV['MAIL_TEST']){
				Mail::send('emails.mail2', $data, function($message) use ($data){
					$message->to("rbautista@chasetech.com", $data['fullname'])->subject('TOP ACTIVITY STATUS');
				});
			}else{
				// Mail::send('emails.mail2', $data, function($message) use ($data){
				// 	$message->to($data['email'], $data['fullname'])->subject('FOR APPROVAL: TOP ACTIVITIES');
				// });
			}
			
		}
		
	}
});


Route::get("mail3", function(){
	$users = User::GetPlanners(['PROPONENT' ,'PMOG','GCOM APPROVER','CD OPS APPROVER','CMD DIRECTOR']);
	$cycles = Cycle::getByApprovalDeadlinePassed();
	$cycle_ids = array();
	foreach ($cycles as $value) {
		$cycle_ids[] = $value->id;
	}

	foreach ($users as $user) {
		$data['cycles'] = $cycles;
		$data['user'] = $user->getFullname();
		$data['email'] = $user->email;
		$data['fullname'] = $user->getFullname();
		$data['cycle_ids'] = $cycle_ids;

		if($user->role_id == 2){
			$data['activities'] = Activity::ProponentActivitiesForApproval($user->id,$cycle_ids);
		}
		if($user->role_id == 3){
			$data['activities'] = Activity::PmogActivitiesForApproval($user->id,$cycle_ids);
		}
		if($user->role_id  > 3){
			$data['activities'] = Activity::ApproverActivities($user->id,$cycle_ids);
		}

		if(count($data['activities']) > 0){
			if($_ENV['MAIL_TEST']){
				Mail::send('emails.mail3', $data, function($message) use ($data){
					$message->to("rbautista@chasetech.com", $data['fullname'])->subject('TOP ACTIVITY STATUS');
				});
			}else{
				// Mail::send('emails.mail3', $data, function($message) use ($data){
				// 	$message->to($data['email'], $data['fullname'])->subject('TOP ACTIVITY STATUS');
				// });
			}
			
		}
		
	}
});


Route::get("mail4", function(){
	$users = User::GetPlanners(['PROPONENT' ,'PMOG','GCOM APPROVER','CD OPS APPROVER','CMD DIRECTOR','FIELD SALES']);
	$cycles = Cycle::getByReleaseDate();
	$cycle_ids = array();
	$cycle_names = "";
	foreach ($cycles as $value) {
		$cycle_ids[] = $value->id;
		$cycle_names .= $value->cycle_name ." - ";
	}

	foreach ($users as $user) {
		$data['cycles'] = $cycles;
		$data['user'] = $user->getFullname();
		$data['email'] = $user->email;
		$data['fullname'] = $user->getFullname();
		$data['cycle_ids'] = $cycle_ids;
		$data['activities'] = Activity::Released($cycle_ids);

		if(count($data['activities']) > 0){
			if($_ENV['MAIL_TEST']){
				Mail::send('emails.mail4', $data, function($message) use ($data){
					$message->to("rbautista@chasetech.com", $data['fullname'])->subject('TOP ACTIVITY STATUS');
				});
			}else{
				// Mail::send('emails.mail4', $data, function($message) use ($data){
				// 	$message->to($data['email'], $data['fullname'])->subject('TOP ACTIVITIES FOR: ('.$cycle_names.')');
				// });
			}
		}
		
	}
});


// Route::post('queue/mail1', function()
// {
// 	return Queue::marshal();
// });

// Route::post('queue/mail2', function()
// {
// 	return Queue::marshal();
// });

// Route::post('queue/mail3', function()
// {
// 	return Queue::marshal();
// });

// Route::post('queue/mail4', function()
// {
// 	return Queue::marshal();
// });



Route::get('queue/send', function(){
	$job_id = Queue::push('Scheduler', array('string' => 'Hello world'));
	Job::create(array('job_id' => $job_id));
	return $job_id;
});

Route::post('queue/push', function()
{
	return Queue::marshal();
});



Route::get('/','LoginController@index');
Route::get('login','LoginController@index');
Route::get('logout','LoginController@logout');

Route::post('login', 'LoginController@dologin');

Route::group(array('before' => 'auth'), function()
{	
	Route::pattern('id', '[0-9]+');

	Route::get('activity/{id}/timings', 'ActivityController@timings');

	Route::post('activity/{id}/updatetimings', 'ActivityController@updatetimings');
	
	Route::post('activity/{id}/updateactivity', 'ActivityController@updateactivity');
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
	

	Route::get('scheme/{id}/export', 'SchemeController@export');
	Route::get('scheme/{id}/allocation', 'SchemeController@allocation');
	Route::get('scheme/{id}', 'SchemeController@show');
	Route::get('scheme/{id}/edit', 'SchemeController@edit');
	Route::delete('scheme/{id}', 'SchemeController@destroy');
	Route::put('scheme/{id}', 'SchemeController@update');
	Route::post('scheme/{id}/duplicate','SchemeController@duplicate');
	Route::put('scheme/updatealloc', 'SchemeController@updateallocation');
	Route::post('scheme/{id}/duplicatescheme', 'SchemeController@duplicatescheme');

	// Route::post('downloadedactivity/{id}/submittogcm', 'DownloadedActivityController@submittogcm');
	// Route::resource('downloadedactivity', 'DownloadedActivityController');


	Route::post('submittedactivity/{id}/updateactivity', 'SubmittedActivityController@updateactivity');
	Route::resource('submittedactivity', 'SubmittedActivityController');

	Route::get('downloads/cycles', 'DownloadsController@cycles');
	Route::get('downloads/{id}/download', 'DownloadsController@download');

	Route::resource('group', 'GroupController');

	Route::get('dashboard', 'DashboardController@index');
	Route::get('dashboard/filters', 'DashboardController@filters');
	Route::post('dashboard/filters', 'DashboardController@savefilters');

	Route::get('dashboard/categoryselected', 'DashboardController@categoryselected');
	Route::get('dashboard/brandselected', 'DashboardController@brandselected');
	Route::get('dashboard/customerselected', 'DashboardController@customerselected');

	Route::get('profile','ProfileController@index');

	// Confide routes
	// Route::get('users', 'UsersController@index');
	// Route::get('users/create', 'UsersController@create');
	// Route::get('users/{id}/edit', 'UsersController@edit');
	// Route::put('users/{id}', 'UsersController@update');
	// Route::post('users', 'UsersController@store');

	Route::get('users/login', 'UsersController@login');
	Route::post('users/login', 'UsersController@doLogin');
	Route::get('users/confirm/{code}', 'UsersController@confirm');
	Route::get('users/forgot_password', 'UsersController@forgotPassword');
	Route::post('users/forgot_password', 'UsersController@doForgotPassword');
	Route::get('users/reset_password/{token}', 'UsersController@resetPassword');
	Route::post('users/reset_password', 'UsersController@doResetPassword');
	Route::get('users/logout', 'UsersController@logout');
	Route::resource('users', 'UsersController');
	

	Route::get('cycle/calendar', 'CycleController@calendar');
	Route::resource('cycle', 'CycleController');

	Route::get('activitytype/{id}/network', 'NetworkController@index');
	Route::get('activitytype/{id}/network/list', 'NetworkController@show');
	Route::get('activitytype/{id}/network/dependon', 'NetworkController@dependOn');
	Route::get('activitytype/{id}/network/totalduration', 'NetworkController@totalduration');
	Route::post('activitytype/{id}/network/create', 'NetworkController@store');
	Route::post('network/delete', 'NetworkController@destroy');
	Route::get('network/edit', 'NetworkController@edit');
	Route::post('network/update', 'NetworkController@update');



	Route::get('reports/activities', 'ReportController@activities');
	Route::get('reports/{id}/preview', 'ReportController@preview');
	Route::get('reports/{id}/download', 'ReportController@download');

	Route::resource('activitytype', 'ActivityTypeController');

	Route::get('holidays/getlist', 'HolidaysController@getlist');
	Route::resource('holidays', 'HolidaysController');

	Route::resource('job','JobController');

	Route::get('images/{cycle_id}/{type_id}/{activity_id}/{name}', function($cycle_id = null,$type_id = null,$activity_id = null,$name = null)
	{
		
		$path = storage_path().'/uploads/'.$cycle_id.'/'. $type_id.'/'. $activity_id.'/'. $name;
		// echo $path;
		if (file_exists($path)) { 

			$image = Image::create($path);
			$image->resize(300, 200, 1);
			return $image->show();
		}
	});

	Route::get('fdapermit/{cycle_id}/{type_id}/{activity_id}/{name}', function($cycle_id = null,$type_id = null,$activity_id = null,$name = null)
	{
		
		$path = storage_path().'/uploads/'.$cycle_id.'/'. $type_id.'/'. $activity_id.'/'. $name;
		// echo $path;
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
		Route::get('customers', 'api\CustomerController@index');
		Route::get('cycles', 'CycleController@availableCycle');

		Route::get('channelselected', 'api\ChannelController@channelselected');
		Route::get('channels', 'api\ChannelController@index');

		Route::post('category/getselected', 'api\SkuController@categoryselected');
		Route::post('category', 'api\SkuController@category');
		Route::post('categories', 'api\SkuController@categories');
		Route::post('brand', 'api\SkuController@brand');
		Route::post('brand/getselected', 'api\SkuController@brandselected');
		Route::resource('network', 'api\NetworkController');
		Route::get('budgettype', 'api\BudgetTypeController@gettype');
		Route::get('materialsource', 'api\MaterialController@getsource');
	});//

});
