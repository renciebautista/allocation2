<?php
use Rencie\Cpm\CpmActivity;
use Rencie\Cpm\Cpm;
class NetworkController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /network
	 *
	 * @return Response
	 */
	public function index($id)
	{
		$activitytype = ActivityType::find($id);
		return View::make('network.index', compact('activitytype'));
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /network/create
	 *
	 * @return Response
	 */
	public function create($id)
	{
		$activitytype = ActivityType::find($id);
		return View::make('network.create', compact('activitytype'));
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /network
	 *
	 * @return Response
	 */
	public function store($id)
	{
		if(Request::ajax()){

			$rules = array(
				'milestone' => 'required',
				'task' => 'required',
				'responsible' => 'required',
				'duration' => 'required'
			);

			$validation = Validator::make(Input::all(),$rules);

			if($validation->passes())
			{
				DB::transaction(function() use ($id){
					$milestone = new ActivityTypeNetwork();
					$milestone->activitytype_id = $id;
					$milestone->milestone = strtoupper(Input::get('milestone'));
					$milestone->task = strtoupper(Input::get('task'));
					$milestone->responsible = strtoupper(Input::get('responsible'));
					$milestone->duration = Input::get('duration');
					$milestone->save();
					$depend_on = Input::get('depend_on');
					if($depend_on !== null){
						foreach (Input::get('depend_on') as $parent) {
							$depend_on = new ActivityNetworkDependent;
							$depend_on->child_id = $milestone->id;
							$depend_on->parent_id = $parent;
							$depend_on->save();
						}
					}

					// update task id
					$milestones = ActivityTypeNetwork::where('activitytype_id', $id)
						->get();
					if(!empty($milestones)){
						foreach ($milestones as $key => $value) {
							$ml = ActivityTypeNetwork::find($value->id);
							$ml->task_id = $key+1;
							$ml->update();
						}
					}
				});
				

				return 0;
			}

			return 1;
		}
		
	}

	/**
	 * Display the specified resource.
	 * GET /network/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		if(Request::ajax()){
			
			$activities = ActivityTypeNetwork::where('activitytype_id', $id)->get();
			foreach ($activities as $key => $value) {
				$activities[$key]->depend_on = ActivityNetworkDependent::depend_on_task($value->id);
			}
			return Response::json($activities);
		}
	}

	public function dependOn($id){
		if(Request::ajax()){

			$data = ActivityTypeNetwork::select('id', 'task_id')
				->where('activitytype_id', $id)
				->get();

			return Response::json($data,200);
		}
	}

	public function totalduration($id){
		if(Request::ajax()){

			$activities = ActivityTypeNetwork::activities($id);
			if(count($activities)>0){
				$cpm = new Cpm($activities);
				return $cpm->TotalDuration();
			}
			return 0;
			
		}
	}

	/**
	 * Show the form for editing the specified resource.
	 * GET /network/{id}/edit
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 * PUT /network/{id}
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
	 * DELETE /network/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}