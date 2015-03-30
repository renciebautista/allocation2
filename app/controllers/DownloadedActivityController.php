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
		$activities = Activity::select('activities.*')
			->join('activity_planners', 'activities.id', '=', 'activity_planners.activity_id')
			->whereIn('activities.status_id',array(4))
			->where('activity_planners.user_id',Auth::id())
			->get();
		return View::make('downloadedactivity.index',compact('activities'));
	}
	/**
	 * Show the form for creating a new resource.
	 * GET /downloadedactivity/create
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /downloadedactivity
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 * GET /downloadedactivity/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
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
		$activity = Activity::find($id);
		if($activity->status_id == 4){
			$submitstatus = array('2' => 'DENY', '5' => 'SUBMIT TO GCOM');
			$sel_planner = ActivityPlanner::where('activity_id',$id)->first();
			$sel_approver = ActivityApprover::getList($id);
			$sel_objectives = ActivityObjective::getList($id);
			$sel_channels = ActivityChannel::getList($id);

			$scope_types = ScopeType::orderBy('scope_name')->lists('scope_name', 'id');
			$planners = User::isRole('PMOG PLANNER')->lists('first_name', 'id');
			$approvers = User::isRole('CD OPS APPROVER')->lists('first_name', 'id');
			$channels = Channel::orderBy('channel_name')->lists('channel_name', 'id');

			$activity_types = ActivityType::orderBy('activity_type')->lists('activity_type', 'id');
			$cycles = Cycle::orderBy('cycle_name')->lists('cycle_name', 'id');
			
			$divisions = Sku::select('division_code', 'division_desc')
				->groupBy('division_code')
				->orderBy('division_desc')->lists('division_desc', 'division_code');

			$objectives = Objective::orderBy('objective')->lists('objective', 'id');

			$budgets = ActivityBudget::with('budgettype')
				->where('activity_id', $id)
				->get();

			$nobudgets = ActivityNobudget::with('budgettype')
				->where('activity_id', $id)
				->get();

			$schemes = Scheme::where('activity_id', $activity->id)
				->orderBy('created_at', 'desc')
				->get();

			$scheme_customers = SchemeAllocation::getCustomers($activity->id);
			$force_allocs = ForceAllocation::getlist($activity->id);

			// $attachments = ActivityAttachment::where('activity_id', $activity->id)->get();

			$scheme_allcations = SchemeAllocation::getAllocation($activity->id);
			$materials = ActivityMaterial::where('activity_id', $activity->id)->get();

			$fdapermits = ActivityFdapermit::where('activity_id', $activity->id)->get();
			$fis = ActivityFis::where('activity_id', $activity->id)->get();
			$artworks = ActivityArtwork::where('activity_id', $activity->id)->get();
			$backgrounds = ActivityBackground::where('activity_id', $activity->id)->get();
			$bandings = ActivityBanding::where('activity_id', $activity->id)->get();

			// comments
			$comments = ActivityComment::where('activity_id', $activity->id)->orderBy('created_at','desc')->get();

			return View::make('downloadedactivity.edit', compact('activity', 'scope_types', 'planners', 'approvers', 'cycles',
			 'activity_types', 'divisions' , 'objectives',  'users', 'budgets', 'nobudgets', 'sel_planner','sel_approver',
			 'sel_objectives', 'channels', 'sel_channels', 'schemes', 'networks',
			 'scheme_customers', 'scheme_allcations', 'materials', 'fdapermits', 'fis', 'artworks', 'backgrounds', 'bandings',
			 'force_allocs','submitstatus', 'comments'));
		}
			
	}

	/**
	 * Update the specified resource in storage.
	 * PUT /downloadedactivity/{id}
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
	 * DELETE /downloadedactivity/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

	public function submittogcm($id){
		if(Request::ajax()){
			$activity = Activity::find($id);

			if(empty($activity)){
				$arr['success'] = 0;
			}else{
				$activity->status_id = Input::get('submitstatus');
				$activity->update();

				$comment = new ActivityComment;
				$comment->created_by = Auth::id();
				$comment->activity_id = $id;
				$comment->comment = Input::get('submitremarks');
				$comment->comment_status_id = Input::get('submitstatus');
				$comment->save();

				$arr['success'] = 1;
			}
			return json_encode($arr);
		}
	}

	public function preview($id){
		return View::make('shared.preview');
	}

}