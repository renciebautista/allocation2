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
		if(Auth::user()->inRoles(['ADMINISTRATOR'])){
			$statuses = ActivityStatus::getLists();
			$scopes = Activity::getScopes();
			$proponents = Activity::getProponents();
			$planners = Activity::getPlanners();
			$approvers = Activity::getApprovers();
			$activitytypes = Activity::getActivityType();
			$divisions = Activity::getDivision();
			$categories = Activity::getCategory();
			$brands = Activity::getBrand();

			$schemefields = AllocReportPerGroup::getAvailableFields(Auth::user()->roles[0]->id);

			return View::make('allocationreport.create',compact('proponents', 'planners', 'statuses',
			'approvers', 'activitytypes', 'scopes', 'divisions', 'brands', 'categories', 'schemefields'));
		}

		if(Auth::user()->inRoles(['PROPONENT'])){
			$statuses = ActivityStatus::getLists();
			$scopes = Activity::getScopes();
			$planners = Activity::getPlanners();
			$approvers = Activity::getApprovers();
			$activitytypes = Activity::getActivityType();
			$divisions = Activity::getDivision();
			$categories = Activity::getCategory();
			$brands = Activity::getBrand();

			$schemefields = AllocReportPerGroup::getAvailableFields(Auth::user()->roles[0]->id);

			return View::make('allocationreport.createpro',compact('planners', 'statuses',
			'approvers', 'activitytypes', 'scopes', 'divisions', 'brands', 'categories', 'schemefields'));
		}

		if(Auth::user()->inRoles(['PMOG PLANNER'])){
			$statuses = ActivityStatus::getLists();
			$scopes = Activity::getScopes();
			$proponents = Activity::getProponents();
			$approvers = Activity::getApprovers();
			$activitytypes = Activity::getActivityType();
			$divisions = Activity::getDivision();
			$categories = Activity::getCategory();
			$brands = Activity::getBrand();

			$schemefields = AllocReportPerGroup::getAvailableFields(Auth::user()->roles[0]->id);

			return View::make('allocationreport.createplan',compact('proponents', 'statuses',
			'approvers', 'activitytypes', 'scopes', 'divisions', 'brands', 'categories', 'schemefields'));
		}

		if(Auth::user()->inRoles(['FIELD SALES','CMD DIRECTOR','CD OPS APPROVER','GCOM APPROVER'])){
			$scopes = Activity::getScopes();
			$proponents = Activity::getProponents();
			$planners = Activity::getPlanners();
			$activitytypes = Activity::getActivityType();
			$divisions = Activity::getDivision();
			$categories = Activity::getCategory();
			$brands = Activity::getBrand();

			$schemefields = AllocReportPerGroup::getAvailableFields(Auth::user()->roles[0]->id);

			return View::make('allocationreport.createfield',compact('proponents','planners',
			'activitytypes', 'scopes', 'divisions', 'brands', 'categories', 'schemefields'));
		}
		

		
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
				$template->created_at = date('Y-m-d H:i:s');
				$template->updated_at = date('Y-m-d H:i:s');
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
							$data[] = ['template_id' => $template->id, 'filter_type_id' => 11, 'filter_id' => trim($value)];
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
							$data[] = ['template_id' => $template->id, 'filter_type_id' => 12, 'filter_id' => trim($value)];
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
				return Redirect::action('AllocationReportController@create')
				->withInput()
				->withErrors($validation)
				->with('class', 'alert-danger')
				->with('message', 'There were validation errors.');
				
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
			
			if($_ENV['MAIL_TEST']){
				$report_id = Queue::push('AllocReportScheduler', array('temp_id' => $temp_id, 
				'user_id' => $user_id,'cycles' => (string)$_cycles),'allocreport');
			}else{
				$report_id = Queue::push('	', array('temp_id' => $temp_id, 
				'user_id' => $user_id,'cycles' => (string)$_cycles),'p_allocreport');
			}

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

		$file = AllocationReportTemplate::where('token',$token)->first();

		$filename = preg_replace('/[^A-Za-z0-9 _ .-]/', '_', $file->template_name);
		$file_name = str_replace(":","_", $foldername);

		if(!empty($file)){
			$path = storage_path().'/exports/'.$file->file_name;
			return Response::download($path,Helper::sanitize($file_name) );
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

	

	/**
	 * Update the specified resource in storage.
	 * PUT /allocationreport/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$template = AllocationReportTemplate::findOrFail($id);
		if ((is_null($template)) && ($template->created_by != Auth::id()))
		{
			$class = 'alert-danger';
			$message = 'Template does not exist.';
			return Redirect::to(URL::action('AllocationReportController@edit',$template->id))
				->with('class', $class )
				->with('message', $message);
		}else{
			$input = Input::all();
			$validation = Validator::make($input, AllocationReportTemplate::$rules);

			if($validation->passes())
			{
				DB::beginTransaction();

				try {

					$template->name = strtoupper(Input::get('name'));
					$template->updated_at = date('Y-m-d H:i:s');
					$template->update();

					AllocationReportFilter::clearFilter($template->id,1);
					if(Input::has('st')){
						$data = array();
						foreach (Input::get('st') as $value) {
							$data[] = ['template_id' => $template->id, 'filter_type_id' => 1, 'filter_id' => $value];
						}
						if(count($data) > 0){
							AllocationReportFilter::insert($data);
						}
					}	

					AllocationReportFilter::clearFilter($template->id,2);
					if(Input::has('scope')){
						$data = array();
						foreach (Input::get('scope') as $value) {
							$data[] = ['template_id' => $template->id, 'filter_type_id' => 2, 'filter_id' => $value];
						}
						if(count($data) > 0){
							AllocationReportFilter::insert($data);
						}
					}	

					AllocationReportFilter::clearFilter($template->id,3);
					if(Input::has('pro')){
						$data = array();
						foreach (Input::get('pro') as $value) {
							$data[] = ['template_id' => $template->id, 'filter_type_id' => 3, 'filter_id' => $value];
						}
						if(count($data) > 0){
							AllocationReportFilter::insert($data);
						}
					}	

					AllocationReportFilter::clearFilter($template->id,4);
					if(Input::has('planner')){
						$data = array();
						foreach (Input::get('planner') as $value) {
							$data[] = ['template_id' => $template->id, 'filter_type_id' => 4, 'filter_id' => $value];
						}
						if(count($data) > 0){
							AllocationReportFilter::insert($data);
						}
					}	

					AllocationReportFilter::clearFilter($template->id,5);
					if(Input::has('app')){
						$data = array();
						foreach (Input::get('app') as $value) {
							$data[] = ['template_id' => $template->id, 'filter_type_id' => 5, 'filter_id' => $value];
						}
						if(count($data) > 0){
							AllocationReportFilter::insert($data);
						}
					}	

					AllocationReportFilter::clearFilter($template->id,6);
					if(Input::has('type')){
						$data = array();
						foreach (Input::get('type') as $value) {
							$data[] = ['template_id' => $template->id, 'filter_type_id' => 6, 'filter_id' => $value];
						}
						if(count($data) > 0){
							AllocationReportFilter::insert($data);
						}
					}	

					AllocationReportFilter::clearFilter($template->id,7);
					if(Input::has('division')){
						$data = array();
						foreach (Input::get('division') as $value) {
							$data[] = ['template_id' => $template->id, 'filter_type_id' => 7, 'filter_id' => $value];
						}
						if(count($data) > 0){
							AllocationReportFilter::insert($data);
						}
					}

					AllocationReportFilter::clearFilter($template->id,8);
					if(Input::has('category')){
						$data = array();
						foreach (Input::get('category') as $value) {
							$data[] = ['template_id' => $template->id, 'filter_type_id' => 8, 'filter_id' => $value];
						}
						if(count($data) > 0){
							AllocationReportFilter::insert($data);
						}
					}

					AllocationReportFilter::clearFilter($template->id,9);
					if(Input::has('brand')){
						$data = array();
						foreach (Input::get('brand') as $value) {
							$data[] = ['template_id' => $template->id, 'filter_type_id' => 9, 'filter_id' => $value];
						}
						if(count($data) > 0){
							AllocationReportFilter::insert($data);
						}
					}

					AllocationReportFilter::clearFilter($template->id,10);
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

					AllocationReportFilter::clearFilter($template->id,11);
					if(Input::has('outlets_involved')){
						$data = array();
						$outlets = explode(",", Input::get('outlets_involved'));
						if(!empty($outlets)){
							foreach ($outlets as $value) {
								$data[] = ['template_id' => $template->id, 'filter_type_id' => 11, 'filter_id' => trim($value)];
							}
							if(count($data) > 0){
								AllocationReportFilter::insert($data);
							}
						}
					}
					AllocationReportFilter::clearFilter($template->id,12);
					if(Input::has('channels_involved')){
						$data = array();
						$channels = explode(",", Input::get('channels_involved'));
						if(!empty($channels)){
							foreach ($channels as $value) {
								$data[] = ['template_id' => $template->id, 'filter_type_id' => 12, 'filter_id' => trim($value)];
							}
							if(count($data) > 0){
								AllocationReportFilter::insert($data);
							}
						}
					}
						
					AllocSchemeField::where('template_id',$template->id)->delete();
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
					return Redirect::to(URL::action('AllocationReportController@edit',$template->id))
						->with('class', 'alert-success')
						->with('message', 'Template successfuly updated.');

				} catch (Exception $e) {
					DB::rollback();
					return Redirect::to(URL::action('AllocationReportController@edit',$template->id))
					->withInput()
					->withErrors($validation)
					->with('class', 'alert-danger')
					->with('message', 'There were validation errors.');
					
				}
			}

		}



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
				$message = 'Cannot delete template.';

				return Redirect::to(URL::action('AllocationReportController@index'))
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
		$sel = AllocationReportFilter::getList($id,12);
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
		$sel = AllocationReportFilter::getList($id,11);
		if(!empty($sel)){
			foreach ($sel as $row) {
				$data[] = $row;
			}
		}
		return Response::json($data,200);
	}

	public function duplicate($id){
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
				$newtemplate = new AllocationReportTemplate;
				$newtemplate->created_by = Auth::id();
				$newtemplate->name = $template->name.'_DUPLICATE';
				$newtemplate->save();

				$filters = AllocationReportFilter::where('template_id',$template->id)->get();
				$data = array();
				foreach ($filters as $filter) {
					$data[] = array('template_id' => $newtemplate->id, 'filter_type_id' => $filter->filter_type_id,
						'filter_id' => $filter->filter_id);
				}
				if(!empty($filters)){
					AllocationReportFilter::insert($data);
				}

				$fields = AllocSchemeField::where('template_id',$template->id)->get();
				$fielddata = array();
				foreach ($fields as $field) {
					$fielddata[] = array('template_id' => $newtemplate->id, 'field_id' => $field->field_id);
				}
				if(!empty($fielddata)){
					AllocSchemeField::insert($fielddata);
				}

				DB::commit();
				return Redirect::to(URL::action('AllocationReportController@edit',$newtemplate->id))
					->with('class', 'alert-success')
					->with('message', 'Template successfuly duplicated.');

				
			} catch (Exception $e) {
				DB::rollback();
				// var_dump($e);
				$class = 'alert-danger';
				$message = 'Cannot duplicate template.';

				return Redirect::to(URL::action('AllocationReportController@index'))
				->with('class', $class )
				->with('message', $message);
			}
		}
	}
}