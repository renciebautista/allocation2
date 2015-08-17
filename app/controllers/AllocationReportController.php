<?php

class AllocationReportController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /allocationreport
	 *
	 * @return Response
	 */
	public function index()
	{
		$templates = AllocationReportTemplate::myTemplates();
		return View::make('allocationreport.index',compact('templates'));
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /allocationreport/create
	 *
	 * @return Response
	 */
	public function create()
	{
		$statuses = ActivityStatus::getLists();
		$scopes = Activity::getScopes();
		$proponents = Activity::getProponents();
		$planners = Activity::getPlanners();
		$approvers = Activity::getApprovers();
		$activitytypes = Activity::getActivityType();
		$divisions = Activity::getDivision();
		$categories = Activity::getCategory();
		$brands = Activity::getBrand();
		// Helper::print_r($groups);
		$schemefields = AllocReportPerGroup::getAvailableFields(Auth::user()->roles[0]->id);
		return View::make('allocationreport.create',compact('proponents', 'planners', 'statuses',
			'approvers', 'activitytypes', 'scopes', 'divisions', 'brands', 'categories', 'schemefields'));
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /allocationreport
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::all();
		$validation = Validator::make($input, AllocationReportTemplate::$rules);

		if($validation->passes())
		{
			DB::beginTransaction();

			try {
				$template = new AllocationReportTemplate;
				$template->created_by = Auth::id();
				$template->name = strtoupper(Input::get('name'));
				$template->save();

				if(Input::has('st')){
					$data = array();
					foreach (Input::get('st') as $value) {
						$data[] = ['template_id' => $template->id, 'filter_type_id' => 1, 'filter_id' => $value];
					}
					if(count($data) > 0){
						AllocationReportFilter::insert($data);
					}
				}	

				if(Input::has('scope')){
					$data = array();
					foreach (Input::get('scope') as $value) {
						$data[] = ['template_id' => $template->id, 'filter_type_id' => 2, 'filter_id' => $value];
					}
					if(count($data) > 0){
						AllocationReportFilter::insert($data);
					}
				}	

				if(Input::has('pro')){
					$data = array();
					foreach (Input::get('pro') as $value) {
						$data[] = ['template_id' => $template->id, 'filter_type_id' => 3, 'filter_id' => $value];
					}
					if(count($data) > 0){
						AllocationReportFilter::insert($data);
					}
				}	

				if(Input::has('planner')){
					$data = array();
					foreach (Input::get('planner') as $value) {
						$data[] = ['template_id' => $template->id, 'filter_type_id' => 4, 'filter_id' => $value];
					}
					if(count($data) > 0){
						AllocationReportFilter::insert($data);
					}
				}	

				if(Input::has('app')){
					$data = array();
					foreach (Input::get('app') as $value) {
						$data[] = ['template_id' => $template->id, 'filter_type_id' => 5, 'filter_id' => $value];
					}
					if(count($data) > 0){
						AllocationReportFilter::insert($data);
					}
				}	

				if(Input::has('type')){
					$data = array();
					foreach (Input::get('type') as $value) {
						$data[] = ['template_id' => $template->id, 'filter_type_id' => 6, 'filter_id' => $value];
					}
					if(count($data) > 0){
						AllocationReportFilter::insert($data);
					}
				}	

				if(Input::has('division')){
					$data = array();
					foreach (Input::get('division') as $value) {
						$data[] = ['template_id' => $template->id, 'filter_type_id' => 7, 'filter_id' => $value];
					}
					if(count($data) > 0){
						AllocationReportFilter::insert($data);
					}
				}

				if(Input::has('category')){
					$data = array();
					foreach (Input::get('category') as $value) {
						$data[] = ['template_id' => $template->id, 'filter_type_id' => 8, 'filter_id' => $value];
					}
					if(count($data) > 0){
						AllocationReportFilter::insert($data);
					}
				}

				if(Input::has('brand')){
					$data = array();
					foreach (Input::get('brand') as $value) {
						$data[] = ['template_id' => $template->id, 'filter_type_id' => 9, 'filter_id' => $value];
					}
					if(count($data) > 0){
						AllocationReportFilter::insert($data);
					}
				}

				if(Input::has('customers')){
					$data = array();
					$customers = explode(",", Input::get('customers'));
					if(!empty($customers)){
						foreach ($customers as $value) {
							$data[] = ['template_id' => $template->id, 'filter_type_id' => 10, 'filter_id' => trim($value)];
						}
						if(count($data) > 0){
							AllocationReportFilter::insert($data);
						}
					}
					
				}

				if(Input::has('outlets_involved')){
					$data = array();
					$outlets = explode(",", Input::get('outlets_involved'));
					if(!empty($outlets)){
						foreach ($outlets as $value) {
							$data[] = ['template_id' => $template->id, 'filter_type_id' => 11, 'filter_id' => $value];
						}
						if(count($data) > 0){
							AllocationReportFilter::insert($data);
						}
					}
				}

				if(Input::has('channels_involved')){
					$data = array();
					$channels = explode(",", Input::get('channels_involved'));
					if(!empty($channels)){
						foreach ($channels as $value) {
							$data[] = ['template_id' => $template->id, 'filter_type_id' => 12, 'filter_id' => $value];
						}
						if(count($data) > 0){
							AllocationReportFilter::insert($data);
						}
					}
				}
					

				if(Input::has('field')){
					$scheme_fields = array();
					foreach (Input::get('field') as $value) {
						$scheme_fields[] = ['template_id' => $template->id, 'field_id' => $value];
					}
					if(count($scheme_fields) > 0){
						AllocSchemeField::insert($scheme_fields);
					}
				}
				

				DB::commit();
				return Redirect::action('AllocationReportController@index')
					->with('class', 'alert-success')
					->with('message', 'Template successfuly created.');

			} catch (Exception $e) {
				DB::rollback();
				var_dump($e);
				
			}
		}

		return Redirect::action('AllocationReportController@create')
			->withInput()
			->withErrors($validation)
			->with('class', 'alert-danger')
			->with('message', 'There were validation errors.');
	}

	/**
	 * Display the specified resource.
	 * GET /allocationreport/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$template = AllocationReportTemplate::findOrFail($id);
		$cycles = Activity::select('cycle_desc','cycle_id')
			->groupBy('cycle_id')
			->orderBy('cycle_desc')
			->lists('cycle_desc', 'cycle_id');
		return View::make('allocationreport.generate', compact('template', 'cycles'));
	}

	public function generate($id){
		$template = AllocationReportTemplate::findOrFail($id);

		$rules = array('cy' => 'required');

		$validation = Validator::make(Input::all(), $rules);
		if($validation->passes())
		{
			$cycles = array();
			foreach (Input::get('cy') as $cy) {
				$cycles[] = $cy;
			}

			$temp_id = $id;
			$user_id = Auth::id();
			$_cycles = implode(",", $cycles);
			// var_dump($_cycles);
			$report_id = Queue::push('AllocReportScheduler', array('temp_id' => $temp_id, 
				'user_id' => $user_id,'cycles' => (string)$_cycles),'allocreport');

			return Redirect::to(URL::action('AllocationReportController@show', array('id' => $template->id)))
				->with('class', 'alert-success')
				->with('message', 'Report successfuly initiated, please wait for an email link to download the report.');
		}else{
			return Redirect::to(URL::action('AllocationReportController@show', array('id' => $template->id)))
				->with('class', 'alert-danger')
				->with('message', 'Cannot generate report please select a cycle.');
		}
	}

	public function download($token){
		$file = AllocationReportFile::where('token',$token)->first();
		if(!empty($file)){
			$path = storage_path().'/exports/'.$file->file_name;
			return Response::download($path, $file->template_name);
		}else{
			echo 'File not found';
		}
		
	}

	/**
	 * Show the form for editing the specified resource.
	 * GET /allocationreport/{id}/edit
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$template = AllocationReportTemplate::findOrFail($id);
		if ((is_null($template)) && ($template->created_by != Auth::id()))
		{
			$class = 'alert-danger';
			$message = 'Template does not exist.';
			return Redirect::to(URL::action('AllocationReportController@index'))
				->with('class', $class )
				->with('message', $message);
		}else{
			$statuses = ActivityStatus::getLists();
			$sel_status =  AllocationReportFilter::getList($template->id,1);
			$scopes = Activity::getScopes();
			$sel_scopes =  AllocationReportFilter::getList($template->id,2);
			$proponents = Activity::getProponents();
			$sel_proponents =  AllocationReportFilter::getList($template->id,3);
			$planners = Activity::getPlanners();
			$sel_planners =  AllocationReportFilter::getList($template->id,4);
			$approvers = Activity::getApprovers();
			$sel_approvers =  AllocationReportFilter::getList($template->id,5);
			$activitytypes = Activity::getActivityType();
			$sel_activitytypes =  AllocationReportFilter::getList($template->id,6);
			$divisions = Activity::getDivision();
			$sel_divisions =  AllocationReportFilter::getList($template->id,7);
			$categories = Activity::getCategory();
			$sel_categories =  AllocationReportFilter::getList($template->id,8);
			$brands = Activity::getBrand();
			$sel_brands =  AllocationReportFilter::getList($template->id,9);
			$schemefields = AllocReportPerGroup::getAvailableFields(Auth::user()->roles[0]->id);
			$sel_schemefields = AllocSchemeField::getFieldList($template->id);
			return View::make('allocationreport.edit',compact('template',
				'statuses','sel_status',
				'scopes','sel_scopes',
				'proponents', 'sel_proponents',
				'planners', 'sel_planners',
				'approvers', 'approvers',
				'activitytypes', 'sel_activitytypes',
				'divisions', 'sel_divisions',
				'categories','sel_categories',
				'brands', 'sel_brands',
				'schemefields','sel_schemefields'));
		}
	}

	public function customerselected(){
		$id = Input::get('id');
		$data = array();
		$sel = AllocationReportFilter::getList($id,10);
		if(!empty($sel)){
			foreach ($sel as $row) {
				$data[] = $row;
			}
		}
		return Response::json($data,200);
	}

	public function channelsselected(){
		$id = Input::get('id');
		$data = array();
		$sel = AllocationReportFilter::getList($id,11);
		if(!empty($sel)){
			foreach ($sel as $row) {
				$data[] = $row;
			}
		}
		return Response::json($data,200);
	}

	public function outletsselected(){
		$id = Input::get('id');
		$data = array();
		$sel = AllocationReportFilter::getList($id,12);
		if(!empty($sel)){
			foreach ($sel as $row) {
				$data[] = $row;
			}
		}
		return Response::json($data,200);
	}

	/**
	 * Update the specified resource in storage.
	 * PUT /allocationreport/{id}
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
	 * DELETE /allocationreport/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$template = AllocationReportTemplate::findOrFail($id);
		if ((is_null($template)) && ($template->created_by != Auth::id()))
		{
			$class = 'alert-danger';
			$message = 'Template does not exist.';
			return Redirect::to(URL::action('AllocationReportController@index'))
				->with('class', $class )
				->with('message', $message);
		}else{

			DB::beginTransaction();
			try {

				AllocationReportFilter::where('template_id',$template->id)->delete();
				AllocSchemeField::where('template_id',$template->id)->delete();
				$template->delete();
				DB::commit();

				$class = 'alert-success';
				$message = $template->name ." template is successfully deleted.";
				return Redirect::to(URL::action('AllocationReportController@index'))
					->with('class', $class )
					->with('message', $message);
			} catch (Exception $e) {
				DB::rollback();
				$class = 'alert-danger';
				$message = 'Cannot delete activity.';

				return Redirect::to(URL::action('ActivityController@index'))
				->with('class', $class )
				->with('message', $message);
			}

			
		}
	}

	public function customer(){
		$data = SchemeAllocation::customerTree();
		return Response::json($data,200);
	}

	public function outlets(){
		$data = SchemeAllocation::outletsTree();
		return Response::json($data,200);
	}

	public function channels(){
		$data = SchemeAllocation::channelsTree();
		return Response::json($data,200);
	}
}