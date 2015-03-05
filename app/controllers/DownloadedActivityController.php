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
			->whereIn('activities.status_id',array(2,3))
			->where('activity_planners.user_id',Auth::id())
			->get();
		return View::make('downloadedactivity.index',compact('activities'));
	}

	// public function nobudget()
	// {
	// 	Input::flash();
	// 	$activities = Activity::join('activity_planners', 'activities.id', '=', 'activity_planners.activity_id')
	// 		->where('activities.downloaded','1')
	// 		->where('activity_planners.user_id',Auth::id())
	// 		->get();
	// 	return View::make('downloadedactivity.nobudget',compact('activities'));
	// }

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
		if($activity->status_id == 2){
			$sel_planner = ActivityPlanner::where('activity_id',$id)
				->first();
			$sel_approver = ActivityApprover::getList($id);

			$scope_types = ScopeType::orderBy('scope_name')->lists('scope_name', 'id');
			$planners = User::isRole('PMOG PLANNER')->lists('first_name', 'id');
			$approvers = User::isRole('CD OPS APPROVER')->lists('first_name', 'id');

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

			$attachments = ActivityAttachment::where('activity_id', $id)->get();

			return View::make('downloadedactivity.edit', compact('activity', 'scope_types', 'planners', 'approvers', 'cycles',
			 'activity_types', 'divisions' , 'objectives',  'users', 'budgets', 'nobudgets', 'sel_planner','sel_approver',
			 'attachments'));
		}

		if($activity->status_id == 3){
			$sel_planner = ActivityPlanner::where('activity_id',$id)
				->first();
			$sel_approver = ActivityApprover::getList($id);

			$scope_types = ScopeType::orderBy('scope_name')->lists('scope_name', 'id');
			$planners = User::isRole('PMOG PLANNER')->lists('first_name', 'id');
			$approvers = User::isRole('CD OPS APPROVER')->lists('first_name', 'id');

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


			return View::make('downloadedactivity.recalled', compact('activity', 'scope_types', 'planners', 'approvers', 'cycles',
			 'activity_types', 'divisions' , 'objectives',  'users', 'budgets', 'nobudgets', 'sel_planner','sel_approver'));
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

	public function doupload($id){
		if(Input::hasFile('file')){
			$distination = storage_path().'/uploads/';
			$file = Input::file('file');
			$original_file_name = $file->getClientOriginalName();
			$file_name = $original_file_name;
			$file_path = $distination.$file_name;

			//Alter the file name until it's unique to prevent overwriting
			while(File::exists($file_path)) {
				$file_name = Str::slug($file->getClientOriginalName()).Str::random(6).'.'.File::extension($file->getClientOriginalName());
				$file_path = $distination.$file_name;
			}

			//Now the upload part
			$file->move($distination,$file_name);

			$docu = new ActivityAttachment;
			$docu->created_by = Auth::id();
			$docu->activity_id = $id;
			$docu->hash_name = $file_name;
			$docu->file_name = $original_file_name;
			$docu->file_desc = (Input::get('file_desc') =='') ? $original_file_name : Input::get('file_desc');
			$docu->save();

			return Redirect::route('downloadedactivity.edit',$id)
				->with('class', 'alert-success')
				->with('message', 'Remarks successfuly posted.');
		}else{
			return Redirect::route('downloadedactivity.edit',$id)
				->with('class', 'alert-danger')
				->with('message', 'Error uploading file.');
		}
	}

	public function downloadfile($id){
		$file = ActivityAttachment::findOrFail($id);
		// Check if file exists in app/storage/file folder
		$file_path = storage_path() .'/uploads/'. $file->hash_name;
		if (file_exists($file_path))
		{
			// Send Download
			return Response::download($file_path);
		}
		else
		{
			exit('Requested file does not exist on our server!');
		}
	
		
	}
}