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

		$comments = ActivityComment::getList($activity->id);
		return View::make('submittedactivity.edit',compact('activity','comments','approver', 'valid'));
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
					$comment_status = "DENIED";
					$class = "text-danger";
					$status = Input::get('status');
					$approver_status = 3;
					$role = "";
					$activity_status = 0;

					if($status == 1){
						$approver_status = 2;
					}
					// update per approver
					$approver = ActivityApprover::getApprover($id,Auth::id());
					$approver->status_id = $approver_status;
					$approver->update();
					// end update per approver

					if($status == 1){  // approved
						$cdops_approvers = ActivityApprover::getApproverByRole($id,'CD OPS APPROVER');
						if(count($cdops_approvers) > 0){
							$comment_status = "SUBMITTED TO CD OPS";
							$role = "GCOM APPROVER";
							$activity_status = 6;

							foreach ($cdops_approvers as $cdops_approver) {
								$approver = ActivityApprover::find($cdops_approver->id);
								$approver->show = 1;
								$approver->update();
							}
						}else{
							$cmd_approvers = ActivityApprover::getApproverByRole($id,'CMD DIRECTOR');
							if(count($cmd_approvers) > 0){
								$comment_status = "SUBMITTED TO CMD";
								$role = "CD OPS APPROVER";
								$activity_status = 7;

								foreach ($cmd_approvers as $cmd_approver) {
									$approver = ActivityApprover::find($cmd_approver->id);
									$approver->show = 1;
									$approver->update();
								}
							}else{
								$comment_status = "APPROVED FOR FIELD";
								$role = "CMD DIRECTOR";
								$activity_status = 8;
							}
						}
						$class = "text-success";
					}else{ // denied
						$comment_status = "DENIED";
						$class = "text-danger";
						$pro_recall = 0;
						$pmog_recall = 0;
						$planner = ActivityPlanner::getPlanner($activity->id);
						if(count($planner) > 0){
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