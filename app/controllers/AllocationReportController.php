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
		$scopes = Activity::select('scope_desc','scope_type_id')
			->groupBy('scope_type_id')
			->lists('scope_desc', 'scope_type_id');

		$proponents = Activity::select('proponent_name','created_by')
			->groupBy('created_by')
			->lists('proponent_name', 'created_by');

		$planners = Activity::select('planner_desc','user_id')
			->join('activity_planners', 'activities.id', '=', 'activity_planners.activity_id')
			->groupBy('user_id')
			->orderBy('planner_desc')
			->lists('planner_desc', 'user_id');

		$approvers = Activity::select('approver_desc','user_id')
			->join('activity_approvers', 'activities.id', '=', 'activity_approvers.activity_id')
			->groupBy('user_id')
			->orderBy('approver_desc')
			->lists('approver_desc', 'user_id');
		
		$activitytypes = Activity::select('activitytype_desc','activity_type_id')
			->groupBy('activity_type_id')
			->lists('activitytype_desc', 'activity_type_id');
		
		$divisions = Activity::select('division_desc','activity_divisions.division_code')
			->join('activity_divisions', 'activities.id', '=', 'activity_divisions.activity_id')
			->groupBy('activity_divisions.division_code')
			->orderBy('division_desc')
			->lists('division_desc', 'division_code');

		$categories = Activity::select('category_desc','activity_categories.category_code')
			->join('activity_categories', 'activities.id', '=', 'activity_categories.activity_id')
			->groupBy('activity_categories.category_code')
			->orderBy('category_desc')
			->lists('category_desc', 'category_code');

		$brands = Activity::select('brand_desc','activity_brands.brand_code')
			->join('activity_brands', 'activities.id', '=', 'activity_brands.activity_id')
			->groupBy('activity_brands.brand_code')
			->orderBy('brand_desc')
			->lists('brand_desc', 'brand_code');

		$groups = SchemeAllocation::select('group','group_code')
			->groupBy('group_code')
			->orderBy('group')
			->get();

		$areas = SchemeAllocation::select('area','area_code')
			->groupBy('area_code')
			->orderBy('area')
			->get();

		$soldtos = SchemeAllocation::select('sold_to','sold_to_code')
			->groupBy('sold_to_code')
			->orderBy('sold_to')
			->get();

		$shiptos = SchemeAllocation::select('ship_to','ship_to_code')
			->groupBy('ship_to_code')
			->orderBy('ship_to')
			->get();

		$outlets = SchemeAllocation::select('outlet')
			->whereNotNull('outlet')
			->groupBy('outlet')
			->orderBy('outlet')
			->get();

		// Helper::print_r($groups);
		$schemefields = SchemesField::all();
		return View::make('allocationreport.create',compact('proponents', 'planners', 'statuses',
			'approvers', 'activitytypes', 'scopes', 'divisions', 'brands', 'categories', 'schemefields',
			'groups','areas', 'soldtos', 'shiptos', 'outlets'));
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

				if(Input::has('groups')){
					$data = array();
					foreach (Input::get('groups') as $value) {
						$data[] = ['template_id' => $template->id, 'filter_type_id' => 10, 'filter_id' => $value];
					}
					if(count($data) > 0){
						AllocationReportFilter::insert($data);
					}
				}

				if(Input::has('areas')){
					$data = array();
					foreach (Input::get('areas') as $value) {
						$data[] = ['template_id' => $template->id, 'filter_type_id' => 11, 'filter_id' => $value];
					}
					if(count($data) > 0){
						AllocationReportFilter::insert($data);
					}
				}

				if(Input::has('soldto')){
					$data = array();
					foreach (Input::get('soldto') as $value) {
						$data[] = ['template_id' => $template->id, 'filter_type_id' => 12, 'filter_id' => $value];
					}
					if(count($data) > 0){
						AllocationReportFilter::insert($data);
					}
				}

				if(Input::has('shipto')){
					$data = array();
					foreach (Input::get('shipto') as $value) {
						$data[] = ['template_id' => $template->id, 'filter_type_id' => 13, 'filter_id' => $value];
					}
					if(count($data) > 0){
						AllocationReportFilter::insert($data);
					}
				}

				if(Input::has('outlet')){
					$data = array();
					foreach (Input::get('outlet') as $value) {
						$data[] = ['template_id' => $template->id, 'filter_type_id' => 14, 'filter_id' => $value];
					}
					if(count($data) > 0){
						AllocationReportFilter::insert($data);
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
				var_dump($e);
				DB::rollback();
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

	public function download($id){
		$template = AllocationReportTemplate::findOrFail($id);

		$rules = array('cy' => 'required');

		$validation = Validator::make(Input::all(), $rules);
		if($validation->passes())
		{
			$cycles = array();
			foreach (Input::get('cy') as $cy) {
				$cycles[] = $cy;
			}

			$report_id = Queue::push('AllocReportScheduler', array('temp_id' => $id),'allocreport');
			AllocationReportFile::create(array('report_id' => $report_id,'temp_id' =>  $id,'cycles' => $cycles));

			// return Redirect::to(URL::action('AllocationReportController@show', array('id' => $template->id)))
			// 	->with('class', 'alert-success')
			// 	->with('message', 'Report successfuly initiated, please wait for an email link to download the report.');
		}else{
			return Redirect::to(URL::action('AllocationReportController@show', array('id' => $template->id)))
				->with('class', 'alert-danger')
				->with('message', 'Cannot generate report please select a cycle.');
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
		//
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
		//
	}

}