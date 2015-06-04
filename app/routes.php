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
// Route::get('/', function(){
// 	$placeId = 1;
// 	Queue::push(function($job) use ($placeId)
// 	{
// 	    Artisan::call('make:pdf', [$placeId]);
// 	    $job->delete();
// 	});

// });

// Route::get("test", function(){
// 	Artisan::queue('make:pdf', array('message' => 'Hello World'));
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

// class Writefile{
// 	public function fire($job, $data){
// 		$job_id = $job->getJobId(); // Get job id

// 		$ejob = Job::where('job_id',$job_id)->first(); // Find the job in database

// 		$ejob->status = 'running'; //Set job status to running

// 		$ejob->save();

// 		Artisan::call('make:pdf');
// 		// Artisan::call('make:pdf',array('id' => 48));
// 		File::append(storage_path().'/queue.txt',$data['string'].$job_id.PHP_EOL); //Add content to file

// 		$ejob->status = 'finished'; //Set job status to finished

// 		$ejob->save();

// 		return true;
// 		$job->delete();
// 	}
// }

// Route::get('print', function(){
// 	$activity = Activity::find(48);
// 	if(!empty($activity)){
// 		$planner = ActivityPlanner::where('activity_id', $activity->id)->first();
// 		$budgets = ActivityBudget::with('budgettype')
// 				->where('activity_id', $activity->id)
// 				->get();

// 		$nobudgets = ActivityNobudget::with('budgettype')
// 			->where('activity_id', $activity->id)
// 			->get();

// 		$schemes = Scheme::getList($activity->id);

// 		$skuinvolves = array();
// 		foreach ($schemes as $scheme) {
// 			$involves = SchemeHostSku::where('scheme_id',$scheme->id)
// 				->join('pricelists', 'scheme_host_skus.sap_code', '=', 'pricelists.sap_code')
// 				->get();
// 			foreach ($involves as $value) {
// 				$skuinvolves[] = $value;
// 			}

// 			$scheme->allocations = SchemeAllocation::getAllocations($scheme->id);
			
// 		}

// 		$materials = ActivityMaterial::where('activity_id', $activity->id)
// 			->with('source')
// 			->get();

// 		$fdapermit = ActivityFdapermit::where('activity_id', $activity->id)->first();
// 		$networks = ActivityTiming::getTimings($activity->id,true);
// 		$artworks = ActivityArtwork::getList($activity->id);
// 		$pispermit = ActivityFis::where('activity_id', $activity->id)->first();

// 		//Involved Area
// 		$areas = ActivityCustomer::getSelectedAreas($activity->id);
// 		$channels = ActivityChannel::getSelectecdChannels($activity->id);
		
// 		// // Product Information Sheet
// 		$path = '/uploads/'.$activity->cycle_id.'/'.$activity->activity_type_id.'/'.$activity->id;
// 		if(!empty($pispermit)){
// 			try {
// 				$pis = Excel::selectSheets('Output')->load(storage_path().$path."/".$pispermit->hash_name)->get();
// 			} catch (Exception $e) {
// 				// return View::make('shared.invalidpis');
// 			}

// 		}else{
// 			$pis = array();
// 		}

// 		// start of pdf
// 		// create new PDF document
// 		$pdf = new ActivityPDF($orientation='P', $unit='mm', $format='LETTER', $unicode=false, $encoding='ISO-8859-1', $diskcache=false, $pdfa=false);	
// 		// set document information
// 		$pdf->SetMargins(10, 32,10);
// 		$pdf->setListIndentWidth(0);	

// 		$pdf->AddPage();

// 		$pdf->SetFont('helvetica', '', 7);

// 		$header = "";
// 		$header .= View::make('pdf.style')->render();
// 		$header .= View::make('pdf.title',compact('activity'))->render();
// 		$header .= View::make('pdf.activity',compact('activity','schemes','networks','materials', 'budgets','nobudgets', 'skuinvolves', 'areas', 'channels','fdapermit'))->render();
// 		$pdf->writeHTML($header , $ln=true, $fill=false, $reset=false, $cell=false, $align='');

// 		$x = $pdf->getX();
// 		$y = $pdf->getY();

// 		$h = $pdf->getPageHeight();
// 		if($h-$y < 60){
// 			$pdf->AddPage();
// 		}

// 		$artwork = View::make('pdf.artwork')->render();
// 		$pdf->writeHTML($artwork , $ln=true, $fill=false, $reset=false, $cell=false, $align='');

// 		if(count($artworks) > 0){
// 			$x = $pdf->getX();
// 			$y = $pdf->getY();
// 			$cnt = 0;
// 			foreach($artworks as $artwork){
// 				$pdf->SetXY($x, $y);
// 				$cnt++;
// 				$image_file = $path = storage_path().'/uploads/'.$activity->cycle_id.'/'.$activity->activity_type_id.'/'.$activity->id.'/'.$artwork->hash_name;
// 				$pdf->Image($image_file, $x, $y, 60, 60, '', '', '', true, 150, '', false, false, 0, false, false, false);
// 				$x+=65;
// 				if($cnt == 3){
// 					$y+=65;
// 					$x = 10;
// 				}
			
// 			}
// 			$pdf->AddPage();
// 		}
		

// 		$fdapermit_view = View::make('pdf.fdapermit')->render();
// 		$pdf->writeHTML($fdapermit_view, $ln=true, $fill=false, $reset=false, $cell=false, $align='');
// 		if(count($fdapermit) > 0){
// 			$x = $pdf->getX();
// 			$y = $pdf->getY();
// 			$image_file = $path = storage_path().'/uploads/'.$activity->cycle_id.'/'.$activity->activity_type_id.'/'.$activity->id.'/'.$fdapermit->hash_name;
// 			$pdf->Image($image_file, $x, $y, 196, 0, '', '', '', true, 150, '', false, false, 0, false, false, false);
			
// 			$pdf->AddPage();
// 		}
		
// 		$barcodes = View::make('pdf.barcodes',compact('schemes'))->render();
// 		$pdf->writeHTML($barcodes, $ln=true, $fill=false, $reset=false, $cell=false, $align='');
// 		// define barcode style

// 		if(count($schemes) > 0){
// 			$style = array(
// 		    'position' => '',
// 		    'align' => 'C',
// 		    'stretch' => false,
// 		    'fitwidth' => true,
// 		    'cellfitalign' => 'C',
// 		    'border' => false,
// 		    'hpadding' => 'auto',
// 		    'vpadding' => 'auto',
// 		    'fgcolor' => array(0,0,0),
// 		    'bgcolor' => false, //array(255,255,255),
// 		    'text' => true,
// 		    'font' => 'helvetica',
// 		    'fontsize' => 8,
// 		    'stretchtext' => 4
// 			);
// 			$str= "";
// 			$cnt= 1;
// 			// $style['cellfitalign'] = 'C';
// 			foreach ($schemes as $scheme) {
// 				$y = $pdf->GetY();
// 				$casecode[$cnt] = $pdf->serializeTCPDFtagParameters(array($scheme->item_casecode, 'I25', '', '', '', 18, 0.4, $style, '')); 
// 				$barcode[$cnt] = $pdf->serializeTCPDFtagParameters(array($scheme->item_barcode, 'EAN13', '', '', '', 18, 0.4, $style, ''));       
// 				$str .='<tr nobr="true"><td align="center">'.$scheme->name.'<br>
// 					<tcpdf method="write1DBarcode" params="'.$casecode[$cnt] .'" />
// 					</td>';
// 				$str .='<td align="center">'.$scheme->name.'<br>
// 					<tcpdf method="write1DBarcode" params="'.$barcode[$cnt] .'" />
// 					</td></tr>';
// 				$cnt++;
// 			}


// 			$str_table='<table cellspacing="0" cellpadding="2" border="1">            
// 			<tr nobr="true">
// 				<td align="center" style="background-color: #000000;color: #FFFFFF;">Case Code</td>
// 				<td align="center" style="background-color: #000000;color: #FFFFFF;">Bar Code</td>
// 			</tr>';
// 			$str_table .= $str;
// 			$str_table .='</table>';
// 			// echo $str_table;
// 			$pdf->writeHTML($str_table, $ln=true, $fill=false, $reset=false, $cell=false, $align='');
// 		}


// 		$pis_view = "";
// 		$pis_view .= View::make('pdf.style')->render();
// 		$pis_view .= View::make('pdf.pis',compact('activity','pis'))->render();
// 		$pdf->writeHTML($pis_view , $ln=true, $fill=false, $reset=false, $cell=false, $align='');


// 		$pdf->SetFont('helvetica', '', 6);
// 		foreach ($schemes as $scheme) {
// 			$count = count($scheme->allocations);
// 			$loops = (int) ($count / 34);
// 			if($count %34 != 0) {
// 			  $loops = $loops+1;
// 			}
// 			$scheme_count  = count($schemes);
// 			$body ='';
			
// 			$cnt = 0;
// 			for ($i = 0; $i <= $loops; $i++) { 
// 				$allocs = array();
// 				$body ='';
// 				$last_count =  $cnt+34;
// 				for ($x=$cnt; $x <= $last_count; $x++) { 
// 					if($cnt == $count){
// 						break;
// 					}
// 					$num = $x + 1;
// 					$class = '';
// 					if((empty($scheme->allocations[$x]->customer_id)) && (empty($scheme->allocations[$x]->shipto_id))){
// 						$class = 'style="background-color: #d9edf7;"';
// 					}
// 					if((!empty($scheme->allocations[$x]->customer_id)) && (!empty($scheme->allocations[$x]->shipto_id))){
// 						$class = 'style="background-color: #fcf8e3;"';
// 					}

// 					$body .='<tr '.$class.'>
// 							<td style="width:20px;border: 1px solid #000000; text-align:right;">'.$num.'</td>
// 							<td style="width:35px;border: 1px solid #000000;">'.$scheme->allocations[$x]->group.'</td>
// 							<td style="width:85px;border: 1px solid #000000;">'.$scheme->allocations[$x]->area.'</td>
// 							<td style="width:95px;border: 1px solid #000000;">'.$scheme->allocations[$x]->sold_to.'</td>
// 							<td style="width:130px;border: 1px solid #000000;">'.$scheme->allocations[$x]->ship_to.'</td>
// 							<td style="width:50px;border: 1px solid #000000;;">'.$scheme->allocations[$x]->channel.'</td>
// 							<td style="width:140px;border: 1px solid #000000;">'.$scheme->allocations[$x]->outlet.'</td>
// 							<td style="width:40px;border: 1px solid #000000; text-align:right;">'.number_format($scheme->allocations[$x]->in_deals).'</td>
// 							<td style="width:40px;border: 1px solid #000000; text-align:right;">'.number_format($scheme->allocations[$x]->in_cases).'</td>
// 							<td style="width:50px;border: 1px solid #000000; text-align:right;">'.number_format($scheme->allocations[$x]->tts_budget,2).'</td>
// 							<td style="width:50px;border: 1px solid #000000; text-align:right;">'.number_format($scheme->allocations[$x]->pe_budget,2).'</td>
// 						</tr>';
// 					$cnt++;
// 				}
// 				if(!empty($body)){
// 					$x = $i +1;
// 					$table = '<h2>'.$scheme->name.'</h2>
// 					<h2>'.$x.' of '.$loops.'</h2>
// 					<table width="100%" style="padding:2px;">
// 						<thead>
// 							<tr>
// 								<th style="width:20px;border: 1px solid #000000; text-align:center;">#</th>
// 								<th style="width:35px;border: 1px solid #000000; text-align:center;">GROUP</th>
// 								<th style="width:85px;border: 1px solid #000000; text-align:center;">AREA NAME</th>
// 								<th style="width:95px;border: 1px solid #000000; text-align:center;">CUSTOMER SOLD TO</th>
// 								<th style="width:130px;border: 1px solid #000000; text-align:center;">CUSTOMER SHIP TO NAME</th>
// 								<th style="width:50px;border: 1px solid #000000; text-align:center;">CHANNEL</th>
// 								<th style="width:140px;border: 1px solid #000000; text-align:center;">ACCOUNT NAME</th> 
// 								<th style="width:40px;border: 1px solid #000000; text-align:center;">IN DEALS</th>
// 								<th style="width:40px;border: 1px solid #000000; text-align:center;">IN CASES</th>
// 								<th style="width:50px;border: 1px solid #000000; text-align:center;">TTS BUDGET</th>
// 								<th style="width:50px;border: 1px solid #000000; text-align:center;">PE BUDGET</th>
// 							</tr>
// 						</thead>
// 					  	<tbody>'.
// 					  		$body. 
// 					  	'</tbody>
// 					</table> ';
// 					$pdf->AddPage($orientation = 'L',$format = '',$keepmargins = false,$tocpage = false );
// 					$pdf->writeHTML($table, $ln=true, $fill=false, $reset=false, $cell=false, $align='');
// 				}
// 			}
// 		}
// 		$pdf->Output('hello_world.pdf','I');
// 	}
	
// });

Route::get('/','LoginController@index');
Route::get('login','LoginController@index');
Route::get('logout','LoginController@logout');

Route::post('login', 'LoginController@dologin');

Route::group(array('before' => 'auth'), function()
{	
	Route::pattern('id', '[0-9]+');

	Route::get('activity/{id}/timings', 'ActivityController@timings');

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
	Route::put('scheme/updatealloc', 'SchemeController@updateallocation');

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
		Route::get('customers', 'api\CustomerController@index');
		Route::get('cycles', 'CycleController@availableCycle');

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
