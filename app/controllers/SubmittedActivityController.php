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
		Input::flash();
		$statuses = ActivityStatus::availableStatus(1);
		$cycles = Cycle::getLists();
		$scopes = ScopeType::getLists();
		$types = ActivityType::getLists();
		$proponents = User::getApprovers(['PROPONENT']);
		$activities = Activity::searchSubmitted(Input::get('pr'),Input::get('st'),Input::get('cy'),Input::get('sc'),
			Input::get('ty'),Input::get('title'));
		return View::make('submittedactivity.index',compact('statuses', 'activities', 'cycles', 'scopes', 'types', 'proponents'));
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
		$activity = Activity::findOrFail($id);
		if(!ActivityApprover::myActivity($activity->id)){
			return Response::make(View::make('shared/404'), 404);
		}

		$approver = ActivityApprover::getApprover($id,Auth::id());
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

		$planner = ActivityPlanner::where('activity_id', $activity->id)->first();
		$budgets = ActivityBudget::with('budgettype')
				->where('activity_id', $id)
				->get();

		$nobudgets = ActivityNobudget::with('budgettype')
			->where('activity_id', $id)
			->get();

		$schemes = Scheme::getList($id);

		$skuinvolves = array();
		foreach ($schemes as $scheme) {
			$involves = SchemeHostSku::where('scheme_id',$scheme->id)
				->join('pricelists', 'scheme_host_skus.sap_code', '=', 'pricelists.sap_code')
				->get();
			foreach ($involves as $value) {
				$skuinvolves[] = $value;
			}

			$scheme->allocations = SchemeAllocation::getAllocations($scheme->id);
			$non_ulp = explode(",", $scheme->ulp_premium);
			
		}

		// Helper::print_r($schemes);

		$materials = ActivityMaterial::where('activity_id', $activity->id)
			->with('source')
			->get();

		$fdapermit = ActivityFdapermit::where('activity_id', $activity->id)->first();
		$networks = ActivityTiming::getTimings($activity->id,true);
		$artworks = ActivityArtwork::getList($activity->id);
		$pispermit = ActivityFis::where('activity_id', $activity->id)->first();

		// $scheme_customers = SchemeAllocation::getCustomers($activity->id);

		//Involved Area
		$areas = ActivityCustomer::getSelectedAreas($activity->id);
		// $channels = ActivityChannel::getSelectecdChannels($activity->id);
		$channels = ActivityChannel2::getSelectecdChannels($activity->id);
		// Helper::print_array($areas);


		
		// // Product Information Sheet
		$path = '/uploads/'.$activity->cycle_id.'/'.$activity->activity_type_id.'/'.$activity->id;
		if(!empty($pispermit)){
			try {
				$pis = Excel::selectSheets('Output')->load(storage_path().$path."/".$pispermit->hash_name)->get();
			} catch (Exception $e) {
				return View::make('shared.invalidpis');
			}

		}else{
			$pis = array();
		}

		$comments = ActivityComment::getList($activity->id);
		return View::make('submittedactivity.edit',compact('activity','comments','approver', 'valid',
			'activity' ,'planner','budgets','nobudgets','schemes','skuinvolves',
			'materials','non_ulp','fdapermit', 'networks','artworks', 'pis' , 'areas','channels'));
	}

	public function updateactivity($id)
	{
		if(Request::ajax()){
			$arr = DB::transaction(function() use ($id)  {
				$activity = Activity::find($id);

				if((empty($activity)) || (!ActivityApprover::myActivity($activity->id))){
					$arr['success'] = 0;
					Session::flash('class', 'alert-danger');
					Session::flash('message', 'An error occured while updating activity.'); 
				}else{
					$status = Input::get('status');
					// end update per approver
					$planner = ActivityPlanner::getPlanner($activity->id);
					$activity_status = $activity->status_id;
					if($status == 1){  // approved
						if(Auth::user()->hasRole("GCOM APPROVER")){
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

						$class = "text-success";
					}else{ // denied
						$activity_status = 2;
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
							
						ActivityApprover::resetAll($activity->id);		
					}
					$activity->status_id = $activity_status;
					$activity->update();

					$comment = new ActivityComment;
					$comment->created_by = Auth::id();
					$comment->activity_id = $id;
					$comment->comment = Input::get('submitremarks');
					$comment->comment_status = $comment_status;
					$comment->class = $class;
					$comment->save();

					$arr['success'] = 1;
					Session::flash('class', 'alert-success');
					Session::flash('message', 'Activity successfully updated.'); 
				}
				return $arr;
			});
			
			return json_encode($arr);
		}
	}

	

}