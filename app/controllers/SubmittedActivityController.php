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
		// show gcom
		// echo Auth::user()->roles()->first()->id;
		Input::flash();
		if(Auth::user()->hasRole("GCOM APPROVER")){
			$status_id = 5;
		}
		if(Auth::user()->hasRole("CD OPS APPROVER")){
			$status_id = 6;
		}
		if(Auth::user()->hasRole("CMD DIRECTOR")){
			$status_id = 7;
		}
		$activities = Activity::select('activities.*')
			->join('activity_approvers', 'activities.id', '=', 'activity_approvers.activity_id')
			->where('activities.status_id',$status_id)
			->where('activity_approvers.user_id',Auth::id())
			->get();
		return View::make('submittedactivity.index',compact('activities'));
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /submittedactivity/create
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /submittedactivity
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 * GET /submittedactivity/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		// ActivityApprover::resetApprover($id,4);
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
		// edit gcom
		$approver = ActivityApprover::getApprover($id,Auth::id());
		$activity = Activity::find($id);
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
		$schemes = Scheme::sorted($id);

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
		$artworks = ActivityArtwork::getArtworks($activity->id);

		$scheme_customers = SchemeAllocation::getCustomers($activity->id);
		
		$pis = Excel::selectSheets('Output')->load(storage_path().'/uploads/fisupload/i1U6YvxiUjCuTXswyUGW.xlsx')->get();

		$comments = ActivityComment::where('activity_id', $activity->id)->orderBy('created_at','desc')->get();
		return View::make('submittedactivity.edit',compact('activity', 'planner','budgets','nobudgets','schemes','skuinvolves','materials',
			'fdapermit', 'networks','artworks' ,'scheme_customers', 'pis','comments','approver', 'valid'));

		// $approver = ActivityApprover::allApproverByRole($id,"GCOM APPROVER");
		// print_r($approver);
	}

	/**
	 * Update the specified resource in storage.
	 * PUT /submittedactivity/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 * DELETE /submittedactivity/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

	public function updateactivity($id)
	{
		if(Request::ajax()){
			$arr = DB::transaction(function() use ($id)  {
				$activity = Activity::find($id);

				if(empty($activity)){
					$arr['success'] = 0;
					Session::flash('class', 'alert-danger');
					Session::flash('message', 'An error occured while updating activity.'); 
				}else{

					$comment_status = "DENIED";
					$class = "text-danger";
					$status = Input::get('status');
					$approver_status = 3;
					$role = "";
					$activity_status = 0;

					if($status == 1){
						$approver_status = 2;
					}
					
					$approver = ActivityApprover::getApprover($id,Auth::id());
					$approver->status_id = $approver_status;
					$approver->update();


					if($status == 1){
						$cdops_approvers = ActivityApprover::getApproverByRole($id,'CD OPS APPROVER');
						if(count($cdops_approvers) > 0){
							$comment_status = "SUBMITTED TO CD OPS";
							$role = "GCOM APPROVER";
							$activity_status = 6;
						}else{
							$cmd_approvers = ActivityApprover::getApproverByRole($id,'CMD DIRECTOR');
							if(count($cmd_approvers) > 0){
								$comment_status = "SUBMITTED TO CMD";
								$role = "CD OPS APPROVER";
								$activity_status = 7;
							}else{
								$comment_status = "APPROVED FOR FIELD";
								$role = "CMD DIRECTOR";
								$activity_status = 8;
							}
						}
						$class = "text-success";

					}else{
						// deny
						$cdops_approvers = ActivityApprover::getAllApproverByRole($id,'CD OPS APPROVER');
						if(count($cdops_approvers) == 0){
							$gcom_approvers = ActivityApprover::getAllApproverByRole($id,'GCOM APPROVER');
							if(count($gcom_approvers) == 0){
								$last_status = 4;
								$last_role = 2;
							}else{
								$last_status = 5;
								$last_role = 3;
							}
						}else{
							$last_status = 6;
							$last_role = 4;
						}


						$comment_status = "DENIED";
						$class = "text-danger";

						$activity->status_id = $last_status;
						$activity->update();

						
					}

					

					$comment = new ActivityComment;
					$comment->created_by = Auth::id();
					$comment->activity_id = $id;
					$comment->comment = Input::get('submitremarks');
					$comment->comment_status = $comment_status;
					$comment->class = $class;
					$comment->save();

					if($status == 1){
						ActivityApprover::updateActivity($id,$role,$activity_status);
					}

					ActivityApprover::resetApprover($id,$last_role);

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