<?php

class CycleController extends \BaseController {

	public function availableCycle(){
		if(Request::ajax()){
			$date = date('Y-m-d',strtotime(Input::get('date')));
			if(Input::has('id')){
				$activity = Activity::find(Input::get('id'));
				$data['cycles'] = Cycle::availableCycle($date);
				$data['sel'] = $activity->cycle_id;
			}else{
				$data['cycles'] = Cycle::availableCycle($date);
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
		$cycles = Cycle::search(Input::all());
		return View::make('cycle.index2', compact('cycles'));
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
				// $cycle->month_year = Input::get('month_year');
				$cycle->start_date = date('Y-m-d',strtotime(Input::get('start_date')));
				$cycle->end_date = date('Y-m-d',strtotime(Input::get('end_date')));
				$cycle->submission_deadline = date('Y-m-d',strtotime(Input::get('submission_deadline')));
				$cycle->approval_deadline = date('Y-m-d',strtotime(Input::get('approval_deadline')));
				$cycle->pdf_deadline = date('Y-m-d',strtotime(Input::get('pdf_deadline')));
				$cycle->release_date = date('Y-m-d',strtotime(Input::get('release_date')));
				$cycle->implemintation_date = date('Y-m-d',strtotime(Input::get('implemintation_date')));
				$cycle->sob_deadline = date('Y-m-d',strtotime(Input::get('sob_deadline')));
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
			// $cycle->month_year = Input::get('month_year');
			$cycle->start_date = date('Y-m-d',strtotime(Input::get('start_date')));
			$cycle->end_date = date('Y-m-d',strtotime(Input::get('end_date')));
			$cycle->submission_deadline = date('Y-m-d',strtotime(Input::get('submission_deadline')));
			$cycle->approval_deadline = date('Y-m-d',strtotime(Input::get('approval_deadline')));
			$cycle->pdf_deadline = date('Y-m-d',strtotime(Input::get('pdf_deadline')));
			$cycle->release_date = date('Y-m-d',strtotime(Input::get('release_date')));
			$cycle->implemintation_date = date('Y-m-d',strtotime(Input::get('implemintation_date')));
			$cycle->sob_deadline = date('Y-m-d',strtotime(Input::get('sob_deadline')));
			$cycle->emergency = (Input::has('emergency')) ? 1 : 0;
			$cycle->released = (Input::has('released')) ? 1 : 0;
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
		$cycle = Cycle::findOrFail($id);
		if(!Activity::cycleUsed($cycle->id)){
			$cycle->delete();
			$class = 'alert-success';
			$message = 'Record successfully deleted.';
		}else{
			$class = 'alert-danger';
			$message = 'Cannot delete cycle.';
		}
		
		return Redirect::route('cycle.index')
				->with('class', $class )
				->with('message', $message);
	}


	public function calendar(){
		Input::flash();
		$cycles = Cycle::search(Input::all());
		return View::make('cycle.calendar', compact('cycles'));
	}


	public function rerun(){
		if(Input::get('submit') == "release"){

			$ids = Input::get('cycle');
			$users = User::GetPlanners(['PROPONENT' ,'PMOG PLANNER','GCOM APPROVER','CD OPS APPROVER','CMD DIRECTOR','FIELD SALES']);

			$cycle_ids = array();
			if(count($ids) > 0){
				foreach ($ids as $value) {
					$cycle_ids[] = $value;
				}
			}

			$total_activities = 0;
			if(count($cycle_ids) > 0){
				$type = "mail4";
				$forrelease = Activity::where('activities.status_id','>',7)
					->where('activities.pdf', 1)
					->whereIn('activities.cycle_id',$cycle_ids)
					->get();
				if(count($forrelease) > 0){
					$total_activities = count($forrelease);
					foreach ($forrelease as $activity) {
						$activity->scheduled = true;
						$activity->status_id = 9;
						$activity->update();

						// relaase sku 
						$activity_skus = ActivitySku::where('activity_id',$activity->id)->get();
						foreach ($activity_skus as $activity_sku) {
							$sku = Pricelist::where('sap_code',$activity_sku->sap_code)->first();
							$sku->active = 1;
							$sku->launch = 0;
							$sku->update();

							LaunchSkuAccess::where('sku_code',$activity_sku->sap_code)->delete();
						}
					}

					if($_ENV['MAIL_TEST']){
						Queue::push('MassMail', [],'mmail');
					}else{
						Queue::push('MassMail', [],'p_mmail');
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

				foreach ($ids as $value) {
					Cycle::where('id', $value)->update(['released' => 1]);
				}
			}
			return Redirect::route('cycle.index')
					->with('class', 'alert-success')
					->with('message', $total_activities." activities were released.");

		}else{
			$ids = Input::get('cycle');
			$cycle_ids = array();
			if(count($ids) > 0){
				foreach ($ids as $value) {
					$cycle_ids[] = $value;
				}
			}

			$total_activities = 0;
			if(count($cycle_ids) > 0){
				$activities = Activity::select('activities.id', 'activities.circular_name')
					->join('cycles', 'cycles.id', '=', 'activities.cycle_id')
					->whereIn('activities.cycle_id',$cycle_ids)
					->where('status_id','>', 7)
					->get();

				$total_activities = count($activities);
				
				foreach ($activities as $activity) {
					if(Input::get('submit') == "doc"){
						$activity->word = 0;
						$activity->update();
						
						if($_ENV['MAIL_TEST']){
							$job_id = Queue::push('WordScheduler', array('string' => "Scheduling ".$activity->circular_name, 'id' => $activity->id),'word');
						}else{
							$job_id = Queue::push('WordScheduler', array('string' => "Scheduling ".$activity->circular_name, 'id' => $activity->id),'p_word');
						}
						Job::create(array('job_id' => $job_id));
					}elseif (Input::get('submit') == "pdf") {
						$activity->pdf = 0;
						$activity->update();
						if($_ENV['MAIL_TEST']){
							$job_id = Queue::push('Scheduler', array('string' => "Scheduling ".$activity->circular_name, 'id' => $activity->id),'pdf');
						}else{
							$job_id = Queue::push('Scheduler', array('string' => "Scheduling ".$activity->circular_name, 'id' => $activity->id),'p_pdf');
						}
						Job::create(array('job_id' => $job_id));
					}elseif (Input::get('submit') == "sob") {
						
					}else{

					}
					
				}
			}
			
			

			if(Input::get('submit') == "doc"){
				$message = $total_activities." activities queue for doc creation";
			}elseif (Input::get('submit') == "pdf") {
				$message = $total_activities." activities queue for pdf creation";
			}elseif (Input::get('submit') == "sob") {

				if($_ENV['MAIL_TEST']){
					Queue::push('SobScheduler', array('cycle_ids' => $cycle_ids),'pono');
				}else{
					Queue::push('SobScheduler', array('cycle_ids' => $cycle_ids),'p_pono');
				}
				$message = $total_activities." activities queue for sob po creation";
			}else{

			}

			return Redirect::route('cycle.index')
					->with('class', 'alert-success')
					->with('message', $message);
		}
		
		
	}

}