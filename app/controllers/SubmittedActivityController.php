<?php

class SubmittedActivityController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /submittedactivity
	 *
	 * @return Response
	 */
	public function index()
	{
		if(Auth::user()->inRoles(['GCOM APPROVER','CD OPS APPROVER','CMD DIRECTOR'])){
			Input::flash();
			$statuses = ActivityStatus::availableStatus(1);
			$cycles = Cycle::getLists();
			$scopes = ScopeType::getLists();
			$types = ActivityType::getLists();
			$proponents = User::getApprovers(['PROPONENT']);
			$s = Input::get('st');
			$activities = Activity::searchSubmitted(Input::get('pr'),Input::get('st'),Input::get('cy'),Input::get('sc'),
				Input::get('ty'),Input::get('title'));
			return View::make('submittedactivity.index',compact('statuses', 'activities', 'cycles', 'scopes', 'types', 'proponents','s'));
		}else{
			return Redirect::route('activity.index');
		}
		
	}

	/**
	 * Show the form for editing the specified resource.
	 * GET /submittedactivity/{id}/edit
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$status = Input::get('s');

		$activityIdList = Activity::getIdList($status);		

		$id_index = array_search($id, $activityIdList);

		// dd($id_index);

		$activity = Activity::findOrFail($id);
		if(!ActivityApprover::myActivity($activity->id)){
			return Response::make(View::make('shared/404'), 404);
		}

		
		$valid = false;
		if(Auth::user()->hasRole("GCOM APPROVER")){
			if($activity->status_id == 5){
				$valid = true;
			}
		}
		if(Auth::user()->hasRole("CD OPS APPROVER")){
			if($activity->status_id == 6){
				$valid = true;
			}
		}
		if(Auth::user()->hasRole("CMD DIRECTOR")){
			if($activity->status_id == 7){
				$valid = true;
			}
		}

		// activity details
		$approver = ActivityApprover::getApprover($id,Auth::id());

		$planner = ActivityPlanner::where('activity_id', $activity->id)->first();
		$approvers = ActivityApprover::getNames($activity->id);

		$objectives = ActivityObjective::where('activity_id', $activity->id)->get();

		$budgets = ActivityBudget::with('budgettype')->where('activity_id', $id)->get();
		$nobudgets = ActivityNobudget::with('budgettype')->where('activity_id', $id)->get();

		$schemes = Scheme::getList($id);

		$skuinvolves = array();
		foreach ($schemes as $scheme) {
			$involves = SchemeHostSku::where('scheme_id',$scheme->id)
				->join('pricelists', 'scheme_host_skus.sap_code', '=', 'pricelists.sap_code')
				->get();

			$premiums = SchemePremuimSku::where('scheme_id',$scheme->id)
				->join('pricelists', 'scheme_premuim_skus.sap_code', '=', 'pricelists.sap_code')
				->get();
			
			$_involves = array();
			foreach ($involves as $value) {
				$_involves[] = $value;
			}
			$_premiums = array();
			foreach ($premiums as $premium) {
				$_premiums[] = $premium;
			}

			$scheme->allocations = SchemeAllocation::getAllocations($scheme->id);
			$non_ulp = explode(",", $scheme->ulp_premium);
			

			$skuinvolves[$scheme->id]['premiums'] = $_premiums;
			$skuinvolves[$scheme->id]['involves'] = $_involves;
			$skuinvolves[$scheme->id]['non_ulp'] = $non_ulp;
		}

		//Involved Area
		$areas = ActivityCutomerList::getSelectedAreas($activity->id);
		$channels = ActivityChannelList::getSelectecdChannels($activity->id);

		$materials = ActivityMaterial::where('activity_id', $activity->id)->get();

		$fdapermits = ActivityFdapermit::getList($activity->id);

		$networks = ActivityTiming::getTimings($activity->id,true);

		$activity_roles = ActivityRole::getListData($activity->id);

		$artworks = ActivityArtwork::getList($activity->id);

		$pispermit = ActivityFis::where('activity_id', $activity->id)->first();

		$sku_involves = ActivitySku::getInvolves($activity->id);

		// // Product Information Sheet
		$path = '/uploads/'.$activity->cycle_id.'/'.$activity->activity_type_id.'/'.$activity->id;
		if($pispermit){
			try {
				$pis = Excel::selectSheets('Output')->load(storage_path().$path."/".$pispermit->hash_name)->get();
			} catch (Exception $e) {
				return View::make('shared.invalidpis');
			}

		}else{
			$pis = array();
		}

		// attachments
		
		$fis = ActivityFis::getList($activity->id);
		$artworks = ActivityArtwork::getList($activity->id);
		$backgrounds = ActivityBackground::getList($activity->id);
		$bandings = ActivityBanding::getList($activity->id);

		// sob

		$soballocation = AllocationSob::getByActivity($activity->id);
		// Helper::print_r($schemes);
		// dd($schemes);
		// comments
		$comments = ActivityComment::getList($activity->id);
		return View::make('submittedactivity.edit',compact('activity','comments','approver', 'objectives', 'valid',
			'activity' ,'approvers', 'planner','budgets','nobudgets','schemes','skuinvolves', 'sku_involves',
			'materials','non_ulp','networks','artworks', 'pis' , 'areas','channels', 
			'fdapermits','fis', 'backgrounds', 'bandings' ,'activity_roles',
			'activityIdList','id_index','status', 'soballocation'));
	}

	public function updateactivity($id)
	{
		$activity = Activity::findOrFail($id);

		
		if((empty($activity)) || (!ActivityApprover::myActivity($activity->id))){
			return Response::make(View::make('shared/404'), 404);
		}else{
			$status = [];
			DB::beginTransaction();

			try {

				$planner = ActivityPlanner::getPlanner($activity->id);
				$activity_status = $activity->status_id;
				if(Input::get('action') == "approve"){
					if(Auth::user()->hasRole("GCOM APPROVER")){
						$status = [5];
						$approver = ActivityApprover::getCurrentApprover($activity->id);
						if(count($approver) > 0 ){
							$approver->status_id = 2;
							$approver->update();

							$comment_status = "APPROVED BY GCOM";

							$gcom_approvers = ActivityApprover::getApproverByRole($activity->id,'GCOM APPROVER');

							if(count($gcom_approvers) == 0){
								$cd_approvers = ActivityApprover::getApproverByRole($activity->id,'CD OPS APPROVER');
								if(count($cd_approvers) == 0){
									$cmd_approvers = ActivityApprover::getApproverByRole($activity->id,'CMD DIRECTOR');
									if(count($cmd_approvers) == 0){
										$activity_status = 8;
									}else{
										ActivityApprover::updateNextApprover($activity->id,'CMD DIRECTOR');
										$activity_status = 7;
									}
								}else{
									ActivityApprover::updateNextApprover($activity->id,'CD OPS APPROVER');
									$activity_status = 6;
								}
							}
						}
					}

					if(Auth::user()->hasRole("CD OPS APPROVER")){
						$status = [6];
						$approver = ActivityApprover::getCurrentApprover($activity->id);
						if(count($approver) > 0 ){
							$approver->status_id = 2;
							$approver->update();

							$comment_status = "APPROVED BY CD OPS";

							$cd_approvers = ActivityApprover::getApproverByRole($activity->id,'CD OPS APPROVER');
							if(count($cd_approvers) == 0){
								$cmd_approvers = ActivityApprover::getApproverByRole($activity->id,'CMD DIRECTOR');
								if(count($cmd_approvers) == 0){
									$activity_status = 8;
								}else{
									ActivityApprover::updateNextApprover($activity->id,'CMD DIRECTOR');
									$activity_status = 7;
								}
							}else{
								ActivityApprover::updateNextApprover($activity->id,'CD OPS APPROVER');
								$activity_status = 6;
							}
						}
					}

					if(Auth::user()->hasRole("CMD DIRECTOR")){
						$status = [7];
						$approver = ActivityApprover::getCurrentApprover($activity->id);
						if(count($approver) > 0 ){
							$approver->status_id = 2;
							$approver->update();

							$comment_status = "APPROVED BY CMD DIRECTOR";

							$cmd_approvers = ActivityApprover::getApproverByRole($activity->id,'CMD DIRECTOR');
							if(count($cmd_approvers) == 0){
								$activity_status = 8;
							}else{
								ActivityApprover::updateNextApprover($activity->id,'CMD DIRECTOR');
								$activity_status = 7;
							}
						}
					}

					$activity->status_id = $activity_status;
					$activity->update();

					$class = "text-success";
					$message = "Activity successfully approved.";
				}else{
					if(Auth::user()->hasRole("GCOM APPROVER")){
						$status = [5];
					}

					if(Auth::user()->hasRole("CD OPS APPROVER")){
						$status = [6];
					}

					if(Auth::user()->hasRole("CMD DIRECTOR")){
						$status = [7];
					}
					$comment_status = "DENIED";
					$class = "text-danger";
					$pro_recall = 0;
					$pmog_recall = 0;

					$planner_count = ActivityPlanner::getPlannerCount($activity->id);
					if(count($planner_count) > 0){
						$last_status = 4;
						$pro_recall = 1;
						$pmog_recall = 0;
					}else{
						$last_status = 2;
					}

					$activity->pro_recall = $pro_recall;
					$activity->pmog_recall = $pmog_recall;
					$activity->status_id = $last_status;
					$activity->update();
						
					ActivityApprover::resetAll($activity->id);	
					$message = "Activity successfully denied.";
				}

				$remarks = "";

				if(Input::get("activity_type") != ""){
					$remarks .= "Activity Type : <i>".Input::get('activity_type') . "</i></br>";
				}

				if(Input::get("activity_title") != ""){
					$remarks .= "Activity Title : <i>".Input::get("activity_title") . "</i></br>";
				}

				if(Input::get("activity_background") != ""){
					$remarks .= "Background : <i>".Input::get("activity_background") . "</i></br>";
				}

				if(Input::get("activity_objective") != ""){
					$remarks .= "Objectives : <i>".Input::get("activity_objective") . "</i></br>";
				}

				if(Input::get("activity_tts") != ""){
					$remarks .= "Budget IO TTS : <i>".Input::get("activity_tts") . "</i></br>";
				}

				if(Input::get("activity_pe") != ""){
					$remarks .= "Budget IO PE : <i>".Input::get("activity_pe") . "</i></br>";
				}

				if(Input::get("activity_skus") != ""){
					$remarks .= "SKU/s Involved : <i>".Input::get("activity_skus") . "</i></br>";
				}

				if(Input::get("activity_area") != ""){
					$remarks .= "Area/s Involved : <i>".Input::get("activity_area") . "</i></br>";
				}

				if(Input::get("activity_channel") != ""){
					$remarks .= "DT Channel/s Involved : <i>".Input::get("activity_channel") . "</i></br>";
				}

				if(Input::get("activity_scheme") != ""){
					$remarks .= "Schemes : <i>".Input::get("activity_scheme") . "</i></br>";
				}

				if(Input::get("activity_scheme_skus") != ""){
					$remarks .= "SKU/s Involved Per Scheme : <i>".Input::get("activity_scheme_skus") . "</i></br>";
				}

				if(Input::get("activity_timing") != ""){
					$remarks .= "Timings : <i>".Input::get("activity_timing") . "</i></br>";
				}

				if(Input::get("activity_roles") != ""){
					$remarks .= "Roles and Responsibilities : <i>".Input::get("activity_roles") . "</i></br>";
				}

				if(Input::get("activity_material") != ""){
					$remarks .= "Material Sourcing : <i>".Input::get("activity_material") . "</i></br>";
				}

				if(Input::get("activity_fda") != ""){
					$remarks .= "FDA Permit No. : <i>".Input::get("activity_fda") . "</i></br>";
				}

				if(Input::get("activity_billing") != ""){
					$remarks .= "Billing Requirements : <i>".Input::get("activity_billing") . "</i></br>";
				}

				if(Input::get("activity_deadline") != ""){
					$remarks .= "Billing Deadline : <i>".Input::get("activity_deadline") . "</i></br>";
				}

				if(Input::get("activity_ins") != ""){
					$remarks .= "Special Instructions : <i>".Input::get("activity_ins") . "</i></br>";
				}

				if(Input::get("activity_art") != ""){
					$remarks .= "Artworks : <i>".Input::get("activity_art") . "</i></br>";
				}

				if(Input::get("activity_barcode") != ""){
					$remarks .= "Barcodes / Case Codes Per Scheme : <i>".Input::get("activity_barcode") . "</i></br>";
				}

				if(Input::get("activity_fda_ac") != ""){
					$remarks .= "FDA Permit : <i>".Input::get("activity_fda_ac") . "</i></br>";
				}

				if(Input::get("activity_alloc") != ""){
					$remarks .= "Allocations : <i>".Input::get("activity_alloc") . "</i></br>";
				}
				// echo $remarks;
				$comment = new ActivityComment;
				$comment->created_by = Auth::id();
				$comment->activity_id = $activity->id;
				$comment->comment = $remarks;
				$comment->comment_status = $comment_status;
				$comment->class = $class;
				$comment->save();

				DB::commit();

				$next_activity = Activity::getNextForApproval($status);
				if(!empty($next_activity)){
					return Redirect::to(URL::action('SubmittedActivityController@edit', array('id' => $next_activity->id, 's' => $status)))
						->with('class', "alert-success" )
						->with('message', $message);
				}else{
					// {{ HTML::linkAction('SubmittedActivityController@edit','View', array('id' => $activity->id, 's' => $s), array('class' => 'btn btn-success btn-xs')) }}
					return Redirect::to(URL::action('SubmittedActivityController@index', array('st' => $status)))
						->with('class', "alert-success" )
						->with('message', "All activity approved.");
				}
				
			} catch (q $e) {
				DB::rollback();
				// return Redirect::to(URL::action('SubmittedActivityController@edit', $id))
				return Redirect::to(URL::action('SubmittedActivityController@edit', array('id' => $id, 's' => $status)))
					->with('class', "alert-danger" )
					->with('message', "Error updating activity.");
			}
			
		}

		
	}

	

}