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
	$activity = Activity::find(68);
	$scheme_customers = SchemeAllocation::getCustomers($activity->id);
	$schemes = Scheme::where('activity_id', $activity->id)
				->orderBy('created_at', 'desc')
				->get();
	$scheme_allcations = SchemeAllocation::getAllocation($activity->id);


	// create new PDF document
	$pdf = new ActivityPDF($orientation='P', $unit='mm', $format='LETTER', $unicode=true, $encoding='UTF-8', $diskcache=false, $pdfa=false);	
	// set document information
	$pdf->SetMargins(13, 35,13);

	$pdf->AddPage();
	$header = '<div style="border-bottom: .5px solid black;padding-bottom:10px;">
				<table >
					<tr>
						<td style="font-weight: bold;width: 130px;">Circular Reference No.</td>
						<td>: 1185</td>
					</tr>
					<tr>
						<td style="font-weight: bold;width: 130px;">Activity Name</td>
						<td>: 2015-1185-ISB/IWB-HAIR-DOVE</td>
					</tr>
					<tr>
						<td style="font-weight: bold;width: 130px;">Proponent Name</td>
						<td>: Rosarah Reyes</td>
					</tr>
					<tr>
						<td style="font-weight: bold;width: 130px;">Creation Date</td>
						<td>: Feb 24, 2015</td>
					</tr>
				</table>
			</div>';
	$header .= '<div id="activity">
				<table class="bordered">
					<tr>
						<td>Activity Type</td>
						<td>ISB/IWB</td>
					</tr>
					<tr>
						<td>Activity Title</td>
						<td>ISB/IWB: Dove DTC 180ml + SH 90ml</td>
					</tr>
					<tr>
						<td>Background</td>
						<td>Dove Hair continues to get more users into the damage/premium segment of
	the hair category. With the goal of influencing uptrade and increasing
	basket size, Dove will continue to activate in store to get more users and
	influence regimen use.</td>
					</tr>
					<tr>
						<td>Objectives</td>
						<td>Increase offtake</td>
					</tr>
					<tr>
						<td>Budget IO TTS</td>
						<td>FA40321225</td>
					</tr>
					<tr>
						<td>Budget IO PE</td>
						<td>PD40321225</td>
					</tr>
					<tr>
						<td>SKU/s Involved</td>
						<td>
							<table class="sub-table">
								<tr>
									<th>Material Code</th>
									<th>Material Description</th>
								</tr>
								<tr>
									<td>21141274</td>
									<td>DOVE HC HAIR FALL PLUS GRN TOT 24X180ML</td>
								</tr>
								<tr>
									<td>21141222</td>
									<td>DOVE HC INTENSE REPAIR BLUE TOT 24X180ML</td>
								</tr>
								<tr>
									<td>21141199</td>
									<td>DOVE HC NRSHNG OIL CARE GLD TOT 24X180ML</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td>Channel/s Involved</td>
						<td>
							<ul>
								<li>MAG EC</li>
								<li>MAG RTM</li>
								<li>DT-MAG</li>
								<li>DRUG BIG 10</li>
								<li>MT GOLD - SM GROUP</li>
								<li>MT GOLD - PUREGOLD</li>
								<li>MT GOLD - RSC</li>
								<li>MT GOLD - SHOPWISE / RUSTANS</li>
								<li>MT GOLD - MERCURY DRUG</li>
								<li>MT GOLD - WATSONS</li>
							</ul>
						</td>
					</tr>
					<tr>
						<td>Schemes</td>
						<td>
							<table class="sub-table">
								<tr>
									<th>Scheme Desc.</th>
									<th>Item Code</th>
									<th>Cost per Deal</th>
									<th>Cost of Premium</th>
									<th>Shopper Purchase Reuirement</th>
								</tr>
								<tr>
									<td>Buy Dove DTC Intense Repair 180ml, Get FREE Dove Intense Repair Sh 90ml</td>
									<td>N/A</td>
									<td>70</td>
									<td>60.50</td>
									<td>108.90</td>
								</tr>
								<tr>
									<td>Buy Dove DTC Nourishing Oil Care 180ml, Get FREE Dove Nourishing Oil Care Sh 90ml</td>
									<td>N/A</td>
									<td>70</td>
									<td>60.50</td>
									<td>108.90</td>
								</tr>
								<tr>
									<td>Buy Dove DTC Hairfall Rescue 180ml, Get FREE Dove Hairfall Rescue Sh 90ml</td>
									<td>N/A</td>
									<td>70</td>
									<td>60.50</td>
									<td>108.90</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td>Timings</td>
						<td>
							<table class="sub-table timing">
								<tr>
									<th>Activity</th>
									<th>Start Date</th>
									<th>End Date</th>
								</tr>
								<tr>
									<td>Implementation Start Date</td>
									<td>Apr 13, 2015</td>
									<td>Apr 13, 2015</td>
								</tr>
								<tr>
									<td>Implementation Start Date</td>
									<td>Apr 13, 2015</td>
									<td>Apr 13, 2015</td>
								</tr>
								<tr>
									<td>Implementation Start Date</td>
									<td>Apr 13, 2015</td>
									<td>Apr 13, 2015</td>
								</tr>
								<tr>
									<td>Implementation Start Date</td>
									<td>Apr 13, 2015</td>
									<td>Apr 13, 2015</td>
								</tr>
								<tr>
									<td>Implementation Start Date</td>
									<td>Apr 13, 2015</td>
									<td>Apr 13, 2015</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td>Material Sourcing</td>
						<td>
							<table class="sub-table source">
								<tr>
									<th>Source</th>
									<th>Materials</th>
								</tr>
								<tr>
									<td>Ex-ULP</td>
									<td>Stickers</td>
								</tr>
								<tr>
									<td>Ex-ULP</td>
									<td>Stickers</td>
								</tr>
								<tr>
									<td>Ex-ULP</td>
									<td>Stickers</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td>FDA Permit No.</td>
						<td>DOH-FDA-CCRR Permit No. 665 s. 2014</td>
					</tr>
					<tr>
						<td>Billing Deadline</td>
						<td>Jun 15, 2015</td>
					</tr>
					<tr>
						<td>Billing Requirements</td>
						<td>AAA should submit the following:
							<ol>
								<li>Banders accomplishment report</li>
								<li>Number of deals banded</li>
								<li>Manpower rate breakdown</li>
							</ol>
						</td>
					</tr>
					<tr>
						<td>Special Instructions</td>
						<td>
							<ol>
								<li> Accounts and Distributors to follow TTS budget allocation. If there will
	be savings on TTS, please declare to CMD and PMOG.</li>
	<li>Please band only with Dove DTC Blue, Gold and Hair Fall. Same variant
	banding please (Blue to Blue, Gold to Gold and HF to HF)</li>
	<li>(For MAG Accounts) Place at grab level.</li>
	<li>(For MAG accounts) Place at promotional tac bins.</li>
							</ol>
						</td>
					</tr>
				</table>
			</div>';

	
	$pdf->SetFont('helvetica', '', 10);
	$pdf->writeHTML($header, $ln=true, $fill=false, $reset=false, $cell=false, $align='');

	$pdf->SetFont('helvetica', '', 8);
	
	$count = count($scheme_customers);
	$loops = (int) ($count / 29);
	$scheme_count  = count($schemes);
	$scheme_loops = (int) ($scheme_count / 3);
	//echo $scheme_loops;
	$body ='';
	

	$cnt = 0;
	for($i = 0; $i <= $loops; $i++){
		$allocs = array();
		$pdf->AddPage($orientation = 'L',$format = '',$keepmargins = false,$tocpage = false );
		$body ='';
		$last_count =  $cnt+29;
		for ($x=$cnt; $x <= $last_count; $x++) { 
			if($cnt == $count){
				break;
			}
			$allocs[] = md5($scheme_customers[$x]->group.'.'.$scheme_customers[$x]->area.'.'.$scheme_customers[$x]->sold_to.'.'.$scheme_customers[$x]->ship_to.'.'.$scheme_customers[$x]->channel.'.'.$scheme_customers[$x]->outlet);
			$body .='<tr style="background-color:#F00;">
				<td style="width:40px;border: 1px solid #000000">'.$scheme_customers[$x]->group.'</td>
				<td style="width:120px;border: 1px solid #000000">'.$scheme_customers[$x]->area.'</td>
				<td style="width:150px;border: 1px solid #000000">'.$scheme_customers[$x]->sold_to.'</td>
				<td style="width:150px;border: 1px solid #000000">'.$scheme_customers[$x]->ship_to.'</td>
				<td style="width:60px;border: 1px solid #000000">'.$scheme_customers[$x]->channel.'</td>
				<td style="width:200px;border: 1px solid #000000">'.$scheme_customers[$x]->outlet.'</td>
			</tr>';
			$cnt++;
		}

		$alloc = '<table width="100%" style="padding:2px;">
					<thead>
						<tr>
							<th style="width:720px;border: 1px solid #000000" colspan="6">Customers</th>
						</tr>
						<tr>
							<th style="width:40px;border: 1px solid #000000">Group</th>
							<th style="width:120px;border: 1px solid #000000">Area</th>
							<th style="width:150px;border: 1px solid #000000">Sold To</th>
							<th style="width:150px;border: 1px solid #000000">Ship To</th>
							<th style="width:60px;border: 1px solid #000000">Channel</th>
							<th style="width:200px;border: 1px solid #000000">Outlet</th> 
						</tr>
					</thead>
				  	<tbody>'.$body.'
				  	</tbody>
				</table> ';

		$pdf->writeHTML($alloc, $ln=true, $fill=false, $reset=false, $cell=false, $align='');

		// print_r($allocs);
		
		$a_count = 0;
		for($s = 0; $s <= $scheme_loops; $s++){
			$pdf->AddPage($orientation = 'L',$format = '',$keepmargins = false,$tocpage = false );
			$scheme_head ='';
			$scheme_body ='';
			$scheme_alloc ='';
			$last_acount =  $a_count+3;
			$scheme_alloc ='';

			for($a = $a_count; $a < $last_acount; $a++){
				if($a_count == $scheme_count){
					break;
				}
				$scheme_head .= '<th style="width:240px;border: 1px solid #000000" colspan="4">'.$schemes[$a]->name.'</th>';


				$scheme_body .= '<th style="width:40px;border: 1px solid #000000">Deals</th>
							<th style="width:40px;border: 1px solid #000000">Cases</th>
							<th style="width:80px;border: 1px solid #000000">TTS Budget</th>
							<th style="width:80px;border: 1px solid #000000">PE Budget</th>';
				
				// $scheme_allcations[$schemes[$a]->id]['2ce7db6de7c353f8975ef7b1922ad106'];

			
				// $scheme_alloc .= '<td style="width:40px;border: 1px solid #000000">'.(isset( $scheme_allcations[$schemes[$a]->id]['2ce7db6de7c353f8975ef7b1922ad106']) ?  $scheme_allcations[$schemes[$a]->id]['2ce7db6de7c353f8975ef7b1922ad106']: '?').'</td>
				// <td style="width:40px;border: 1px solid #000000">'.(isset( $scheme_allcations[$schemes[$a]->id]['2ce7db6de7c353f8975ef7b1922ad106']) ?  $scheme_allcations[$schemes[$a]->id]['2ce7db6de7c353f8975ef7b1922ad106']: '?').'</td>
				// <td style="width:80px;border: 1px solid #000000">'.(isset( $scheme_allcations[$schemes[$a]->id]['2ce7db6de7c353f8975ef7b1922ad106']) ?  $scheme_allcations[$schemes[$a]->id]['2ce7db6de7c353f8975ef7b1922ad106']: '?').'</td>
				// <td style="width:80px;border: 1px solid #000000">'.(isset( $scheme_allcations[$schemes[$a]->id]['2ce7db6de7c353f8975ef7b1922ad106']) ?  $scheme_allcations[$schemes[$a]->id]['2ce7db6de7c353f8975ef7b1922ad106']: '?').'</td>';
				// $a_count++;
			}

			$scheme_allochead ='';
			// foreach ($allocs as $value) {
			// 		// echo $scheme_allcations[$schemes[$a]->id][$value];
			// 		$scheme_allochead .= '<tr>'.$scheme_alloc.'</tr>';
			// 	}

			$_scheme = '<table width="100%" style="padding:2px;">
					<thead>
						<tr>'.$scheme_head.'
							
						</tr>
						<tr>
							'.$scheme_body.'
						</tr>
					</thead>
				  	<tbody>'.$scheme_allochead.'
				  	</tbody>
				</table> ';


			$pdf->writeHTML($_scheme, $ln=true, $fill=false, $reset=false, $cell=false, $align='');
			
		}
		
	}

	

	$pdf->lastPage();
	$pdf->Output('hello_world.pdf','I');

	// return View::make('pdf.table');
});

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
	Route::delete('activity/{id}/artworkdelete', 'ActivityController@artworkdelete');
	Route::get('activity/{id}/artworkdownload', 'ActivityController@artworkdownload');

	Route::post('activity/{id}/backgroundupload', 'ActivityController@backgroundupload');
	Route::delete('activity/{id}/backgrounddelete', 'ActivityController@backgrounddelete');
	Route::get('activity/{id}/backgrounddownload', 'ActivityController@backgrounddownload');

	Route::post('activity/{id}/bandingupload', 'ActivityController@bandingupload');
	Route::delete('activity/{id}/bandingdelete', 'ActivityController@bandingdelete');
	Route::get('activity/{id}/bandingdownload', 'ActivityController@bandingdownload');

	Route::resource('activity', 'ActivityController');
	

	Route::get('scheme/{id}/allocation', 'SchemeController@allocation');
	Route::get('scheme/{id}', 'SchemeController@show');
	Route::get('scheme/{id}/edit', 'SchemeController@edit');
	Route::delete('scheme/{id}', 'SchemeController@destroy');
	Route::put('scheme/{id}', 'SchemeController@update');
	Route::put('scheme/updatealloc', 'SchemeController@updateallocation');

	Route::get('downloadedactivity/{id}/preview', 'DownloadedActivityController@preview');
	Route::post('downloadedactivity/{id}/submittogcm', 'DownloadedActivityController@submittogcm');
	
	Route::resource('downloadedactivity', 'DownloadedActivityController');

	Route::post('submittedactivity/{id}/updateactivity', 'SubmittedActivityController@updateactivity');
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

	Route::get('reports/activities', 'ReportController@activities');
	Route::get('reports/{id}/preview', 'ReportController@preview');

	Route::resource('activitytype', 'ActivityTypeController');

	Route::get('images/{folder}/{image}', function($folder = null,$image = null)
	{
	    $path = storage_path().'/uploads/'.$folder.'/' . $image;
	    // echo $path;
	    if (file_exists($path)) { 
	    	$img = Image::make($path)->resize(300, 200);
	    	return $img->response();
	        // return Response::download($path);
	    }
	});

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
