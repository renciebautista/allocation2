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



// Route::get('testreport', function(){
// 	$scheme_id = 111;
// 	$groups = SchemeAllocation::select('group','group_code')
// 			// ->where('scheme_id',$scheme_id)
// 			->groupBy('group_code')
// 			->orderBy('id')
// 			->get();

// 	foreach ($groups as $group) {
// 		$areas = SchemeAllocation::select('area','area_code')
// 			// ->where('scheme_id',$scheme_id)
// 			->where('group_code',$group->group_code)
// 			->groupBy('area_code')
// 			->orderBy('id')
// 			->get();
// 		echo $group->group.'</br>';
// 		foreach ($areas as $area) {
// 			$soldtos = SchemeAllocation::select('sold_to','sold_to_code')
// 				// ->where('scheme_id',$scheme_id)
// 				->where('area_code',$area->area_code)
// 				->groupBy('sold_to_code')
// 				->orderBy('id')
// 				->get();
// 			echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$area->area.'</br>';
// 			foreach ($soldtos as $soldto) {
// 				$shiptos = SchemeAllocation::select('ship_to','ship_to_code')
// 					// ->where('scheme_id',$scheme_id)
// 					->where('sold_to_code',$soldto->sold_to_code)
// 					->whereNotNull('ship_to_code')
// 					->groupBy('ship_to_code')
// 					->orderBy('id')
// 					->get();
// 				echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$soldto->sold_to.'</br>';
// 				foreach ($shiptos as $shipto) {
// 					if($shipto->ship_to_code != ''){
// 						$outlets = SchemeAllocation::select('outlet')
// 							// ->where('scheme_id',$scheme_id)
// 							->where('area_code',$area->area_code)
// 							->where('sold_to_code',$soldto->sold_to_code)
// 							->where('ship_to_code',$shipto->ship_to_code)
// 							->whereNotNull('outlet')
// 							->groupBy('outlet')
// 							->orderBy('id')
// 							->get();
// 						echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
// 							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$shipto->ship_to.'</br>';
// 						foreach ($outlets as $outlet) {
// 							echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
// 							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$outlet->outlet.'</br>';
// 						}
// 					}
// 				}
// 			}

// 		}
// 	}
// });

Route::get('allocreport', function(){
	$template = AllocationReportTemplate::findOrFail(21);
	$headers = AllocSchemeField::getFields($template->id);
	$data['cycles'] = array(7);
	$data['status'] = AllocationReportFilter::getList($template->id,1);
	$data['scopes'] = AllocationReportFilter::getList($template->id,2);
	$data['proponents'] = AllocationReportFilter::getList($template->id,3);
	$data['planners'] = AllocationReportFilter::getList($template->id,4);
	$data['approvers'] = AllocationReportFilter::getList($template->id,5);
	$data['activitytypes'] = AllocationReportFilter::getList($template->id,6);
	$data['divisions'] = AllocationReportFilter::getList($template->id,7);
	$data['categories'] = AllocationReportFilter::getList($template->id,8);
	$data['brands'] = AllocationReportFilter::getList($template->id,9);
	$data['customers'] = AllocationReportFilter::getList($template->id,10);
	$data['outlets'] = AllocationReportFilter::getList($template->id,11);
	$data['channels'] = AllocationReportFilter::getList($template->id,12);
	$data['fields'] = $headers;
	$take = 1000; // adjust this however you choose
	$counter = 0;
	$user = User::find(1);
	// var_dump($user->roles[0]->name);
	$token = md5(uniqid(mt_rand(), true));
		
		$timeFirst  = strtotime(date('Y-m-d H:i:s'));
		$filePath = storage_path('exports/'.$token.'.xlsx');
		$writer = WriterFactory::create(Type::XLSX);
		$writer->setShouldCreateNewSheetsAutomatically(true); // default value
		$writer->openToFile($filePath); // write data to a file or to a PHP stream
		$take = 1000; // adjust this however you choose
		$counter = 0; // used to skip over the ones you've already processed

		
		$header = array();
		foreach ($headers as $value) {
			$header[] = $value->desc_name;
		}
		
		$writer->addRow($header); // add multiple rows at a time

		while($rows = AllocationReport::getReport($data,$take,$counter,$user))
		{
			if(count($rows) == 0){
				break;
			}
			$counter += $take;
			foreach($rows as $key => $value)
			{
				$rows[$key] = (array) $value;
			} 
			$export_data = $rows;

			$writer->addRows($export_data); // add multiple rows at a time
		}
		$writer->close();
		$timeSecond = strtotime(date('Y-m-d H:i:s'));
		$differenceInSeconds = $timeSecond - $timeFirst;
	

		$data['template'] = $template;
		$data['token'] = $token;
		$data['user'] = $user;
		$name = $template->name;
		// $grayfield = array('HOST SKU CODE','HOST SKU DESC','PREMIUM SKU CODE','PREMIUM SKU DESC','NON-ULP PREMIUM'); 
		// $greenfield = array('ITEM CODE','BARCODE','CASECODE'); 
		// $yellowfield = array('GROUP','AREA','SOLD TO','SHIP TO CODE','CUSTOMER SHIP TO NAME','CHANNEL','ACCOUNT NAME');
		// $blackfield = array('UOM',)

		$excel2 = PHPExcel_IOFactory::createReader('Excel2007');
		$excel2 = $excel2->load($filePath); // Empty Sheet
		$excel2->setActiveSheetIndex(0);
		$excel2->getActiveSheet()
			->getStyle('A1:B1')->getFill()
			->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
			->getStartColor()->setARGB('FFE8E5E5');


		$objWriter = PHPExcel_IOFactory::createWriter($excel2, 'Excel2007');
		$objWriter->save(storage_path('exports/'.$token.'_2.xlsx'));
});

// Route::get('testpdf',function(){
// 	$activity = Activity::find(34);
// 	$schemes = Scheme::getList($activity->id);
// 	if(count($schemes) > 0){
// 	$w_codes = false;
// 	foreach ($schemes as $scheme) {
// 		if($scheme->item_barcode !== ""){
// 			$w_codes = true;
// 		}
// 		if($scheme->item_casecode !== ""){
// 			$w_codes = true;
// 		}
// 	}
// 	if($w_codes){
		
// 		$style = array(
// 	    'position' => '',
// 	    'align' => 'C',
// 	    'stretch' => false,
// 	    'fitwidth' => true,
// 	    'cellfitalign' => 'C',
// 	    'border' => false,
// 	    'hpadding' => 'auto',
// 	    'vpadding' => 'auto',
// 	    'fgcolor' => array(0,0,0),
// 	    'bgcolor' => false, //array(255,255,255),
// 	    'text' => true,
// 	    'font' => 'helvetica',
// 	    'fontsize' => 8,
// 	    'stretchtext' => 4
// 		);
// 		$str= "";
// 		$cnt= 1;
// 		// $style['cellfitalign'] = 'C';
// 		foreach ($schemes as $scheme) {
			
// 			if(($scheme->item_barcode  !== "") || ($scheme->item_casecode !== "")){
// 				if($scheme->item_barcode  !== ""){
// 					$barcode[$cnt] = $scheme->item_barcode;       
// 				}

// 				if($scheme->item_casecode !== ""){
// 					$casecode[$cnt] = $scheme->item_casecode; 
					
// 				}
				

// 				if($scheme->item_barcode !== ""){
// 					$str .='<tr nobr="true"><td align="center">'.$scheme->name.'<br>
// 					<tcpdf method="write1DBarcode" params="'.$barcode[$cnt] .'" />
// 					</td>';
// 				}else{
// 					$str .='<tr nobr="true"><td align="center"></td>';
// 				}

// 				if($scheme->item_casecode !== ""){
// 					$str .='<td align="center">'.$scheme->name.'<br>
// 					<tcpdf method="write1DBarcode" params="'.$casecode[$cnt] .'" />
// 					</td></tr>';
// 				}else{
// 					$str .='<td align="center"></td></tr>';
// 				}
// 			}
// 			$cnt++;
// 		}


// 		$str_table='<table cellspacing="0" cellpadding="2" border=".1px;">            
// 		<tr nobr="true">
// 			<td align="center" style="background-color: #000000;color: #FFFFFF;">Barcode</td>
// 			<td align="center" style="background-color: #000000;color: #FFFFFF;">Case Code</td>
// 		</tr>';
// 		$str_table .= $str;
// 		$str_table .='</table>';
// 		echo $str_table;

// 	}}			
// });

// Route::get('testword',function(){
// 	set_time_limit(0);
// 	$activity = Activity::find(53);
// 	$worddoc = new WordDoc($activity->id);
// 	$worddoc->download("Rencie.docx");					
// });

// Route::get('testrole', function(){
// 	// $filename = preg_replace('/[^A-Za-z0-9 _ .-]/', '_', "SNOWBALL 2015 PREBANDED PACKS 470ML/700ML SCHEMES");
// 	// echo strtoupper(Helper::sanitize("SNOWBALL-2015-LADY’S-CHOICE-CATEGORY-EXPERTS"));
// 	$user = User::find(2);
// 	$cycles = Cycle::getByReleaseDate();
// 	$cycle_ids = array();
// 	$cycle_names = "";
// 	foreach ($cycles as $value) {
// 		$cycle_ids[] = $value->id;
// 		// $cycle_names .= $value->cycle_name ." - ";
// 	}
// 	// $data['cycles'] = $cycles;
// 	$data['user'] = $user->first_name;
// 	$data['email'] = $user->email;
// 	$data['fullname'] = $user->getFullname();
// 	$data['cycle_ids'] = $cycle_ids;
// 	// $data['cycle_names'] = $cycle_names;
	
// 	$data['activities'] = Activity::Released($cycle_ids);

// 	$data['cycles'] = Activity::ReleasedCyles($cycle_ids);
// 	foreach ($data['cycles'] as $value) {
// 		$cycle_names .= $value->cycle_name ." - ";
// 	}

// 	$data['cycle_names'] = $cycle_names;

// 	return View::make('emails.mail4', $data);
// });

Route::get('mail4', function(){
	$cycles = Cycle::getByReleaseDate();
	$cycle_ids = array();
	$cycle_names = "";
	foreach ($cycles as $value) {
		$cycle_ids[] = $value->id;
	}
	
	$user = User::find(1);

	$data['user'] = $user->first_name;
	$data['email'] = $user->email;
	$data['fullname'] = $user->getFullname();
	$data['cycle_ids'] = $cycle_ids;

	$data['activities'] = Activity::Released($cycle_ids);

	$data['cycles'] = Activity::ReleasedCyles($cycle_ids);


	foreach ($data['cycles'] as $value) {

		$cycle_names .= $value->cycle_name ." - ";
	}

	$data['cycle_names'] = $cycle_names;

	dd($cycle_names);
});

Route::get('sob', function(){
	$scheme = Scheme::find(192);
	echo idate('W', strtotime($scheme->sob_start_date));
	dd($scheme->sob_start_date);
});


//---------------------------------------------------

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

Route::get('/','LoginController@index');
Route::get('login','LoginController@index');
Route::get('logout','LoginController@logout');
Route::post('login', 'LoginController@dologin');

Route::get('forgotpassword','LoginController@forgotpassword');
Route::post('forgotpassword','LoginController@doforgotpassword');
Route::get('reset_password/{token}','LoginController@resetpassword');
Route::post('reset_password', 'LoginController@doResetPassword');

Route::get('downloadcycle/{id}', 'DownloadsController@downloadcycle');
Route::get('ar/{token}','AllocationReportController@download');


Route::group(array('before' => 'auth'), function()
{	
	// Route::pattern('id', '[0-9]+');

	

	Route::get('launchskus/upload','LaunchSkuController@upload');
	Route::post('launchskus/upload','LaunchSkuController@doupload');
	Route::post('launchskus/assignaccess','LaunchSkuController@assignaccess');
	Route::post('launchskus/removeaccess','LaunchSkuController@removeaccess');
	Route::resource('launchskus','LaunchSkuController');

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
	Route::get('scheme/{id}/allocation', 'SchemeController@allocation');
	Route::get('scheme/{id}/edit', 'SchemeController@edit');
	Route::get('scheme/{id}', 'SchemeController@show');
	Route::delete('scheme/{id}', 'SchemeController@destroy');
	Route::put('scheme/{id}', 'SchemeController@update');
	Route::put('scheme/{id}/sob', 'SchemeController@updatesob');
	Route::post('scheme/{id}/duplicate','SchemeController@duplicate');
	Route::post('scheme/{id}/duplicatescheme', 'SchemeController@duplicatescheme');

	// Route::post('downloadedactivity/{id}/submittogcm', 'DownloadedActivityController@submittogcm');
	// Route::resource('downloadedactivity', 'DownloadedActivityController');


	Route::post('submittedactivity/{id}/updateactivity', 'SubmittedActivityController@updateactivity');
	Route::get('submittedactivity/{id}/edit', 'SubmittedActivityController@edit');
	Route::get('submittedactivity', array('as' => 'submittedactivity.index', 'uses' => 'SubmittedActivityController@index'));
	// Route::get('submittedactivity', 'SubmittedActivityController@index');
	// Route::resource('submittedactivity', 'SubmittedActivityController');

	Route::get('downloads/cycles', 'DownloadsController@cycles');
	Route::get('downloads/{id}/all', 'DownloadsController@downloadall');
	Route::get('downloads/{id}/approved', 'DownloadsController@download');

	Route::get('downloads/{id}/released', 'DownloadsController@released');

	Route::resource('group', 'GroupController');

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

	Route::get('users/exportuser', 'UsersController@exportuser');
	Route::resource('users', 'UsersController');
	
	Route::post('cycle/release', 'CycleController@release');
	Route::post('cycle/{id}/rerun', 'CycleController@rerun');
	Route::post('cycle/{id}/rerundoc', 'CycleController@rerundoc');
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



	
	Route::resource('activitytype', 'ActivityTypeController');

	Route::get('holidays/getlist', 'HolidaysController@getlist');
	Route::resource('holidays', 'HolidaysController');

	// Route::resource('job','JobController');	
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

	Route::get('reports/activities', 'ReportController@activities');
	Route::get('reports/{id}/preview', 'ReportController@preview');
	Route::get('reports/{id}/download', 'ReportController@download');
	Route::get('reports/{id}/document', 'ReportController@document');



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
		Route::get('customers', 'api\CustomerController@index');
		Route::get('cycles', 'CycleController@availableCycle');

		Route::get('channelselected', 'api\ChannelController@channelselected');
		Route::get('channels', 'api\ChannelController@index');

		Route::post('category/getselected', 'api\SkuController@categoryselected');
		Route::post('category', 'api\SkuController@category');
		Route::post('categories', 'api\SkuController@categories');
		Route::post('brand', 'api\SkuController@brand');
		Route::post('brand/getselected', 'api\SkuController@brandselected');

		Route::post('skusinvolve', 'api\PriceListController@involve');
		Route::post('sku/skuselected', 'api\PriceListController@skuselected');

		Route::resource('network', 'api\NetworkController');
		Route::get('budgettype', 'api\BudgetTypeController@gettype');
		Route::get('materialsource', 'api\MaterialController@getsource');
	});//

});
