<?php

class CycleController extends \BaseController {

	public function availableCycle(){
		if(Request::ajax()){
			$date = date('Y-m-d',strtotime(Input::get('date')));
			$month = date("m", strtotime($date));
			$year =  date("Y", strtotime($date));
			if(Input::has('id')){
				$activity = Activity::find(Input::get('id'));
				$data['cycles'] = Cycle::select('id', 'cycle_name')
				->where('month_year',$month."/".$year)
				->orderBy('cycle_name')->lists('cycle_name', 'id');
				$data['sel'] = $activity->cycle_id;
			}else{
				$data['cycles'] = Cycle::select('id', 'cycle_name')
				->where('month_year',$month."/".$year)
				->orderBy('cycle_name')->lists('cycle_name', 'id');
			}
			
			return \Response::json($data,200);
		}
	}

	/**
	 * Display a listing of the resource.
	 * GET /cycle
	 *
	 * @return Response
	 */
	public function index()
	{
		Input::flash();
		$cycles = Cycle::search(Input::get('s'));
		return View::make('cycle.index', compact('cycles'));
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /cycle/create
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('cycle.create');
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /cycle
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::all();
		$validation = Validator::make($input, Cycle::$rules);

		if($validation->passes())
		{
			DB::transaction(function()
			{

				$cycle = new Cycle;
				$cycle->cycle_name = strtoupper(Input::get('cycle_name'));
				$cycle->month_year = Input::get('month_year');
				$cycle->submission_deadline = date('Y-m-d',strtotime(Input::get('submission_deadline')));
				$cycle->approval_deadline = date('Y-m-d',strtotime(Input::get('approval_deadline')));
				$cycle->pdf_deadline = date('Y-m-d',strtotime(Input::get('pdf_deadline')));
				$cycle->release_date = date('Y-m-d',strtotime(Input::get('release_date')));
				$cycle->implemintation_date = date('Y-m-d',strtotime(Input::get('implemintation_date')));
				$cycle->emergency = (Input::has('emergency')) ? 1 : 0;
				$cycle->save();

				// $types = ActivityType::all();
				$path = storage_path().'/uploads/'.$cycle->id;
				if(!File::exists($path)) {
				    // path does not exist
				    File::makeDirectory($path);
				}
				
			});
			return Redirect::route('cycle.index')
				->with('class', 'alert-success')
				->with('message', 'Record successfuly created.');
		}

		return Redirect::route('cycle.create')
			->withInput()
			->withErrors($validation)
			->with('class', 'alert-danger')
			->with('message', 'There were validation errors.');
	}

	/**
	 * Display the specified resource.
	 * GET /cycle/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		
	}

	/**
	 * Show the form for editing the specified resource.
	 * GET /cycle/{id}/edit
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$cycle = Cycle::find($id);
		return View::make('cycle.edit',compact('cycle'));
	}

	/**
	 * Update the specified resource in storage.
	 * PUT /cycle/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$input = Input::all();
		$validation = Validator::make($input, Cycle::$rules);
		if ($validation->passes())
		{
			$cycle = Cycle::find($id);
			$old_name = $cycle->cycle_name;
			if (is_null($cycle))
			{
				return Redirect::route('cycle.index')
					->with('class', 'alert-danger')
					->with('message', 'Record does not exist.');
			}


			$cycle->cycle_name = strtoupper(Input::get('cycle_name'));
			$cycle->month_year = Input::get('month_year');
			$cycle->submission_deadline = date('Y-m-d',strtotime(Input::get('submission_deadline')));
			$cycle->approval_deadline = date('Y-m-d',strtotime(Input::get('approval_deadline')));
			$cycle->pdf_deadline = date('Y-m-d',strtotime(Input::get('pdf_deadline')));
			$cycle->release_date = date('Y-m-d',strtotime(Input::get('release_date')));
			$cycle->implemintation_date = date('Y-m-d',strtotime(Input::get('implemintation_date')));
			$cycle->emergency = (Input::has('emergency')) ? 1 : 0;
			$cycle->save();

			// $old_path = storage_path().'/uploads/'.$old_name;
			// $new_path = storage_path().'/uploads/'.$cycle->cycle_name;
			// rename($old_path, $new_path);

			return Redirect::route('cycle.index')
				->with('class', 'alert-success')
				->with('message', 'Record successfuly updated.');
		}

		return Redirect::route('cycle.edit', $id)
			->withInput()
			->withErrors($validation)
			->with('class', 'alert-danger')
			->with('message', 'There were validation errors.');
	}

	/**
	 * Remove the specified resource from storage.
	 * DELETE /cycle/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$cycle = Cycle::find($id)->delete();
		if (is_null($cycle))
		{
			$class = 'alert-danger';
			$message = 'Record does not exist.';
		}else{
			$class = 'alert-success';
			$message = 'Record successfully deleted.';
		}
		return Redirect::route('cycle.index')
				->with('class', $class )
				->with('message', $message);
	}


	public function calendar(){
		Input::flash();
		$cycles = Cycle::search(Input::get('s'));
		return View::make('cycle.calendar', compact('cycles'));
	}

	public function release(){
		if(Request::ajax()){
			$ids = Input::get('ids');
			$users = User::GetPlanners(['PROPONENT' ,'PMOG PLANNER','GCOM APPROVER','CD OPS APPROVER','CMD DIRECTOR','FIELD SALES']);

			$cycle_ids = array();
			if(count($ids) > 0){
				foreach ($ids as $value) {
					$cycle_ids[] = $value;
				}
			}

			$total_activities = 0;
			$type = "mail4";
			$forRelease = Activity::where('activities.status_id','>',7)
				->join('cycles', 'activities.cycle_id','=','cycles.id')
				->where('activities.pdf', 1)
				->whereIn('activities.cycle_id',$cycle_ids)
				->get();
			if(count($forRelease) > 0){
				$total_activities = count($forRelease);
				foreach ($forRelease as $activity) {
					$activity->scheduled = 1;
					$activity->status_id = 9;
					$activity->update();
				}

				// foreach ($users as $user) {
				// 	$data['activities'] = Activity::Released($cycle_ids);
				// 	if(count($data['activities']) > 0){
				// 		if($_ENV['MAIL_TEST']){
				// 			Queue::push('MailScheduler', array('type' => $type, 'user_id' => $user->user_id, 'role_id' => $user->role_id,),'etop');
				// 		}else{
				// 			Queue::push('MailScheduler', array('type' => $type, 'user_id' => $user->user_id, 'role_id' => $user->role_id),'p_etop');
				// 		}
				// 	}
				// }
				
			}else{
			}
			
			// echo $total_activities;
			
		}

	}

	public function rerun($id){
		// rerun activities
		$cycle = Cycle::find($id);
		$activities = Activity::select('activities.id', 'activities.circular_name')
			->join('cycles', 'cycles.id', '=', 'activities.cycle_id')
			->where('cycles.id',$id)
			->where('status_id','>', 7)
			->get();
		foreach ($activities as $activity) {
			$activity->pdf = 0;
			$activity->update();
			
			if($_ENV['MAIL_TEST']){
				$job_id = Queue::push('Scheduler', array('string' => "Scheduling ".$activity->circular_name, 'id' => $activity->id),'pdf');
			}else{
				$job_id = Queue::push('Scheduler', array('string' => "Scheduling ".$activity->circular_name, 'id' => $activity->id),'p_pdf');
			}
			Job::create(array('job_id' => $job_id));
		}

		return Redirect::route('cycle.index')
				->with('class', 'alert-success')
				->with('message', $cycle->cycle_name.' cycle pdf creation is successfuly initiated.');
	}
}