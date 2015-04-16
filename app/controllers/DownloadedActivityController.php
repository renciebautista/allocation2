<?php

class DownloadedActivityController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /downloadedactivity
	 *
	 * @return Response
	 */
	public function index()
	{
		Input::flash();
		$statuses = ActivityStatus::availableStatus(1);
		$activities = Activity::searchDownloaded(Auth::id(),Input::get('proponent'),Input::get('status'),Input::get('cycle'),Input::get('scope'),
			Input::get('type'),Input::get('title'));
		$cycles = Cycle::getLists();
		$scopes = ScopeType::getLists();
		$types = ActivityType::getLists();
		$proponents = User::getApprovers(['PROPONENT']);
		return View::make('downloadedactivity.index',compact('statuses', 'activities', 'cycles', 'scopes', 'types', 'proponents'));
	}

	/**
	 * Show the form for editing the specified resource.
	 * GET /downloadedactivity/{id}/edit
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{	
		$activity = Activity::findOrFail($id);
		if(!ActivityPlanner::myActivity($activity->id)){
			return Response::make(View::make('shared/404'), 404);
		}

		$sel_planner = ActivityPlanner::getPlanner($activity->id);
		$sel_approver = ActivityApprover::getList($activity->id);
		$sel_objectives = ActivityObjective::getList($activity->id);
		$sel_channels = ActivityChannel::getList($activity->id);
		$approvers = User::getApprovers(['GCOM APPROVER','CD OPS APPROVER','CMD DIRECTOR']);
		$channels = Channel::getList();
		$objectives = Objective::getLists();
		$budgets = ActivityBudget::getBudgets($activity->id);
		$nobudgets = ActivityNobudget::getBudgets($activity->id);
		$schemes = Scheme::getList($activity->id);
		$scheme_customers = SchemeAllocation::getCustomers($activity->id);
		$force_allocs = ForceAllocation::getlist($activity->id);
		$scheme_customers = SchemeAllocation::getCustomers($activity->id);
		$force_allocs = ForceAllocation::getlist($activity->id);
		$scheme_allcations = SchemeAllocation::getAllocation($activity->id);
		$materials = ActivityMaterial::getList($activity->id);
		// attachments
		$fdapermits = ActivityFdapermit::getList($activity->id);
		$fis = ActivityFis::getList($activity->id);
		$artworks = ActivityArtwork::getList($activity->id);
		$backgrounds = ActivityBackground::getList($activity->id);
		$bandings = ActivityBanding::getList($activity->id);
		// comments
		$comments = ActivityComment::getList($activity->id);

		if($activity->status_id == 4){
			$submitstatus = array('1' => 'SUBMIT ACTIVITY','2' => 'DENY ACTIVITY');
			$scope_types = ScopeType::getLists($activity->id);
			$planners = User::getApprovers(['PMOG PLANNER']);
			$activity_types = ActivityType::getLists();
			$cycles = Cycle::getLists();
			$divisions = Sku::getDivisionLists();

			return View::make('downloadedactivity.edit', compact('activity', 'scope_types', 'planners', 'approvers', 'cycles',
			 'activity_types', 'divisions' , 'objectives',  'users', 'budgets', 'nobudgets', 'sel_planner','sel_approver',
			 'sel_objectives', 'channels', 'sel_channels', 'schemes', 'networks',
			 'scheme_customers', 'scheme_allcations', 'materials', 'fdapermits', 'fis', 'artworks', 'backgrounds', 'bandings',
			 'force_allocs','submitstatus', 'comments'));
		}else{
			$submitstatus = array('3' => 'RECALL ACTIVITY');
			$division = Sku::division($activity->division_code);
			$route = 'downloadedactivity.index';
			$recall = $activity->pmog_recall;
			$submit_action = 'DownloadedActivityController@submittogcm';
			return View::make('shared.activity_readonly', compact('activity', 'sel_planner', 'approvers', 'division',
			 'objectives',  'users', 'budgets', 'nobudgets','sel_approver',
			 'sel_objectives', 'channels', 'sel_channels', 'schemes', 'networks',
			 'scheme_customers', 'scheme_allcations', 'materials', 'force_allocs',
			 'fdapermits', 'fis', 'artworks', 'backgrounds', 'bandings', 'comments' ,'submitstatus', 'route', 'recall', 'submit_action'));
		}
			
	}


	public function submittogcm($id){
		if(Request::ajax()){
			$arr = DB::transaction(function() use ($id)  {
				$activity = Activity::find($id);
				if((empty($activity)) || (!ActivityPlanner::myActivity($activity->id))){
					$arr['success'] = 0;
				}else{
					$status = (int) Input::get('submitstatus');
					$activity_status = 2;
					$pro_recall = 0;
					$pmog_recall = 0;
					if($status == 1){
						//check next approver
						$pmog_recall = 1;
						$gcom_approvers = ActivityApprover::getApproverByRole($activity->id,'GCOM APPROVER');
						if(count($gcom_approvers) > 0){
							$comment_status = "SUBMITTED TO GCOM";
							$activity_status = 5;
						}else{
							$cdops_approvers = ActivityApprover::getApproverByRole($activity->id,'CD OPS APPROVER');
							if(count($cdops_approvers) > 0){
								$comment_status = "SUBMITTED TO CD OPS";
								$activity_status = 6;
							}else{
								$cmd_approvers = ActivityApprover::getApproverByRole($activity->id,'CMD DIRECTOR');
								if(count($cmd_approvers) > 0){
									$comment_status = "SUBMITTED TO CMD";
									$activity_status = 7;
								}else{
									$comment_status = "APPROVED FOR FIELD";
									$activity_status = 8;
								}
							}
						}
						$class = "text-success";
					}elseif($status == 3){
						$comment_status = "RECALLED ACTIVITY";
						$class = "text-warning";
						$pro_recall = 1;
						$pmog_recall = 0;
						$activity_status = 4;
						ActivityApprover::resetAll($activity->id);
					}else{
						$comment_status = "DENIED ACTIVITY";
						$class = "text-danger";
						ActivityApprover::resetAll($activity->id);
					}

					$activity->status_id = $activity_status;
					$activity->pro_recall = $pro_recall;
					$activity->pmog_recall = $pmog_recall;
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


	public function preview($id){
		$activity = Activity::find($id);
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
			
		}

		$materials = ActivityMaterial::where('activity_id', $activity->id)
			->with('source')
			->get();

		$fdapermit = ActivityFdapermit::where('activity_id', $activity->id)->first();
		$networks = ActivityTiming::getTimings($activity->id);
		$artworks = ActivityArtwork::getList($activity->id);

		$scheme_customers = SchemeAllocation::getCustomers($activity->id);
		
		$pis = Excel::selectSheets('Output')->load(storage_path().'/uploads/fisupload/i1U6YvxiUjCuTXswyUGW.xlsx')->get();
		return View::make('shared.preview', compact('activity' ,'planner','budgets','nobudgets','schemes','skuinvolves','materials',
			'fdapermit', 'networks','artworks' ,'scheme_customers', 'pis'));
	}

}