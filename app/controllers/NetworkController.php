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
			$data['success'] = 0;
			$activitytype = ActivityType::findOrFail($id);
			if(empty($activitytype)){
				$data['success']  = 0;
			}

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
					$key = 1;
					$last_record = ActivityTypeNetwork::orderBy('id', 'desc')
						->where('activitytype_id',$id)
						->first();
					if(!empty($last_record)){
						$key = $last_record->task_id + 1;
					}
					$milestone = new ActivityTypeNetwork();
					$milestone->activitytype_id = $id;
					$milestone->milestone = strtoupper(Input::get('milestone'));
					$milestone->task = strtoupper(Input::get('task'));
					$milestone->responsible = strtoupper(Input::get('responsible'));
					$milestone->duration = Input::get('duration');
					$milestone->show = (Input::has('show')) ? true : false;
					$milestone->task_id = $key;
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
					// $milestones = ActivityTypeNetwork::where('activitytype_id', $id)->get();
					// if(!empty($milestones)){
					// 	foreach ($milestones as $key => $value) {
					// 		$ml = ActivityTypeNetwork::find($value->id);
					// 		$ml->task_id = $key+1;
					// 		$ml->update();
					// 	}
					// }

					
				});
				

				$data['success'] = 1;
			}
		}
		return Response::json($data,200);
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
			
			$activities = ActivityTypeNetwork::where('activitytype_id', $id)
				->orderBy('task_id')
				->get();
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
			$data = array();
			$activities = ActivityTypeNetwork::activities($id);
			$holidays = Holiday::allHoliday();
			$data['days'] = 1;
			$data['start_date'] = ActivityTypeNetwork::getImplemetationDate( date('m/d/Y'),$holidays,0);
			if(count($activities)>0){
				$cpm = new Cpm($activities);
				$data['days'] = $cpm->TotalDuration();
				$data['cpm'] = $cpm->CriticalPath();
			}

			$data['min_date'] = ActivityTypeNetwork::getImplemetationDate($data['start_date'],$holidays,$data['days'] - 1);
			if (Input::has('sd'))
			{
			    $data['end_date'] = date('m/d/Y',strtotime(Input::get('sd')));
			   	$data['start_date'] = ActivityTypeNetwork::getDownloadDate($data['end_date'],$holidays,$data['days'] - 1);
			}else{
				$data['min_date'] = ActivityTypeNetwork::getImplemetationDate($data['start_date'],$holidays,$data['days'] - 1);
				$data['end_date'] = ActivityTypeNetwork::getImplemetationDate($data['start_date'],$holidays,$data['days'] - 1);
			}
			
			return Response::json($data,200);
		}
	}


	/**
	 * Show the form for editing the specified resource.
	 * GET /network/{id}/edit
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit()
	{
		if(Request::ajax()){
			$id = Input::get("d_id");
			$network = ActivityTypeNetwork::findOrFail($id);
			$data['network'] = $network;
			$data['selection'] = ActivityTypeNetwork::select('id', 'task_id')
				->where('activitytype_id', $network->activitytype_id)
				->where('id', '!=',$id)
				->lists('task_id', 'id');
			$data['selected'] = ActivityNetworkDependent::depend_on_task_array($network->id);
			return Response::json($data,200);
		}
	}

	/**
	 * Update the specified resource in storage.
	 * PUT /network/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update()
	{
		if(Request::ajax()){
			$data['success'] = 0;
			$id = Input::get("ed_id");
			$milestone = ActivityTypeNetwork::findOrFail($id);
			if(empty($milestone)){
				$data['success']  = 0;
			}

			$rules = array(
				'emilestone' => 'required',
				'etask' => 'required',
				'eresponsible' => 'required',
				'eduration' => 'required'
			);

			$validation = Validator::make(Input::all(),$rules);

			if($validation->passes())
			{
				DB::transaction(function() use ($milestone){
					$milestone->milestone = strtoupper(Input::get('emilestone'));
					$milestone->task = strtoupper(Input::get('etask'));
					$milestone->responsible = strtoupper(Input::get('eresponsible'));
					$milestone->duration = Input::get('eduration');
					$milestone->show = (Input::has('eshow')) ? true : false;
					$milestone->update();
					$depend_on = Input::get('edepend_on');

					ActivityNetworkDependent::where('child_id',$milestone->id)->delete();
					if($depend_on !== null){
						foreach (Input::get('edepend_on') as $parent) {
							$depend_on = new ActivityNetworkDependent;
							$depend_on->child_id = $milestone->id;
							$depend_on->parent_id = $parent;
							$depend_on->save();
						}
					}
				});
				

				$data['success'] = 1;
			}
		}
		return Response::json($data,200);
	}

	/**
	 * Remove the specified resource from storage.
	 * DELETE /network/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy()
	{
		if(Request::ajax()){
			$id = Input::get('d_id');
			$network = ActivityTypeNetwork::find($id);
			if(empty($network)){
				$arr['success'] = 0;
			}else{
				ActivityNetworkDependent::where('child_id',$network->id)
					->delete();
				ActivityNetworkDependent::where('parent_id',$network->id)
					->delete();
				$network->delete();


				$arr['success'] = 1;
				
			}
			$arr['id'] = $id;
			return json_encode($arr);
		}
	}

}