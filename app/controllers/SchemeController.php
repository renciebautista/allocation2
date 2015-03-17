<?php

class SchemeController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /scheme
	 *
	 * @return Response
	 */
	public function index($id)
	{
		$schemes = Scheme::where('activity_id',$id)->get();
		return View::make('scheme.index', compact('id', 'schemes'));
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /scheme/create
	 *
	 * @return Response
	 */
	public function create($id)
	{
		$activity = Activity::find($id);

		$categories = ActivityCategory::selected_category($id);
		$brands = ActivityBrand::selected_brand($id);
		$skus = Sku::select('sku_code', DB::raw('CONCAT(sku_desc, "- ", sku_code) AS full_desc'))
			->where('division_code',$activity->division_code)
			->whereIn('category_code',$categories)
			->whereIn('brand_code',$brands)
			->orderBy('sku_code')
			->lists('full_desc', 'sku_code');
		$involves = Pricelist::orderBy('sap_desc')->lists('sap_desc', 'sap_code');



		return View::make('scheme.create', compact('activity','skus', 'involves'));
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /scheme
	 *
	 * @return Response
	 */
	public function store($id)
	{
		$validation = Validator::make(Input::all(), Scheme::$rules);

		if($validation->passes())
		{
			DB::transaction(function() use ($id)  {
				$total_sales = 0;
				$activity = Activity::find($id);

				$scheme = new Scheme;
				$scheme->activity_id = $activity->id;

				$scheme->name = strtoupper(Input::get('scheme_name'));
				$scheme->item_code = Input::get('item_code');
				$scheme->item_barcode = Input::get('item_barcode');
				$scheme->item_casecode = Input::get('item_casecode');

				$scheme->pr =  str_replace(",", "", Input::get('pr'));
				$scheme->srp_p = str_replace(",", "", Input::get('srp_p'));
				$scheme->other_cost =str_replace(",", "", Input::get('other_cost'));

				$scheme->ulp =  str_replace(",", "", Input::get('ulp'));
				$scheme->cost_sale = str_replace(",", "", Input::get('cost_sale'));

				$scheme->quantity = str_replace(",", "", Input::get('total_alloc'));
				$scheme->deals = str_replace(",", "", Input::get('deals'));
				$scheme->tts_r =  str_replace(",", "", Input::get('tts_r'));
				$scheme->pe_r = str_replace(",", "", Input::get('pe_r'));
				$scheme->total_cost = str_replace(",", "", Input::get('total_cost'));
				$scheme->user_id = Auth::id();

				$scheme->save();

				$skus = array();
				foreach (Input::get('skus') as $sku){
					$skus[] = array('scheme_id' => $scheme->id, 'sku' => $sku);
				}
				SchemeSku::insert($skus);

				// create allocation
				SchemeAllocRepository::saveAlllocation($scheme);

			});
			// #schemes
			// return Redirect::action('ActivityController@edit', array('id' => $id))
			return Redirect::to(URL::action('ActivityController@edit', array('id' => $id)) . "#schemes")
				->with('class', 'alert-success')
				->with('message', 'Scheme "'.Input::get('scheme_name').'" was successfuly created.');
			
		}

		return Redirect::action('SchemeController@create', array('id' => $id))
			->withInput()
			->withErrors($validation)
			->with('class', 'alert-danger')
			->with('message', 'There were validation errors.');
	}

	/**
	 * Display the specified resource.
	 * GET /scheme/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{	
		
		$scheme = Scheme::find($id);
		$skus = SchemeSku::getSkus($id);
		$customers = ActivityCustomer::customers($scheme->activity_id);

		// $channels = array('C1', 'C2', 'C3');

		$_channels = ActivityChannel::channels($scheme->activity_id);

		$qty = $scheme->quantity;

		$_allocation = new AllocationRepository;
		$allocations = $_allocation->customers($skus, $_channels, $customers);
		
		$total_sales = $_allocation->total_sales();

		$summary = $_allocation->allocation_summary();
		$big10 = $_allocation->account_group("AG4");
		$gaisanos = $_allocation->account_group("AG5");
		$nccc = $_allocation->account_group("AG6");
		// echo '<pre>';
		// print_r($big10);
		// echo '</pre>';
		// $channels = array();
		// $groups = $_allocation->groups();
		// $areas = $_allocation->areas();
		// $soldtos = $_allocation->soldtos();
		return View::make('scheme.show', compact('allocations','total_sales',
			'qty','id', 'summary', 'big10', 'gaisanos', 'nccc'));
	}

	/**
	 * Show the form for editing the specified resource.
	 * GET /scheme/{id}/edit
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$scheme = Scheme::find($id);
		$activity = Activity::find($scheme->activity_id);
		$categories = ActivityCategory::selected_category($scheme->activity_id);
		$brands = ActivityBrand::selected_brand($scheme->activity_id);
		$skus = Sku::select('sku_code', DB::raw('CONCAT(sku_desc, "- ", sku_code) AS full_desc'))
			->where('division_code',$activity->division_code)
			->whereIn('category_code',$categories)
			->whereIn('brand_code',$brands)
			->orderBy('sku_code')
			->lists('full_desc', 'sku_code');

		$sel_skus =  SchemeSku::getSkus($scheme->id);
		// print_r($sel_skus);
		$customers = ActivityCustomer::customers($scheme->activity_id);
		// print_r($customers);
		$_channels = ActivityChannel::channels($scheme->activity_id);
		// print_r($_channels);
		$qty = $scheme->quantity;
		// print_r($qty);
		// $_allocation = new AllocationRepository;
		
		// $allocations = $_allocation->customers($sel_skus, $_channels, $customers);
		// // print_r($allocations);
		// $total_sales = $_allocation->total_sales();

		// $summary = $_allocation->allocation_summary();
		// $big10 = $_allocation->account_group("AG4");
		// $gaisanos = $_allocation->account_group("AG5");
		// $nccc = $_allocation->account_group("AG6");

		$scheme_customers = SchemeAllocation::getCustomerAllocation($id);

		// echo "<pre>";
		// print_r($scheme_customers);
		// echo "</pre>";
		return View::make('scheme.edit',compact('scheme', 'activity', 'skus', 'sel_skus',
			'allocations', 'total_sales', 'qty','id', 'summary', 'big10', 'gaisanos', 'nccc', 'scheme_customers'));
	}

	/**
	 * Update the specified resource in storage.
	 * PUT /scheme/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$validation = Validator::make(Input::all(), Scheme::$rules);

		if($validation->passes())
		{
			DB::transaction(function() use ($id)  {

				$scheme = Scheme::find($id);

				$scheme->name = strtoupper(Input::get('scheme_name'));
				$scheme->item_code = Input::get('item_code');
				$scheme->item_barcode = Input::get('item_barcode');
				$scheme->item_casecode = Input::get('item_casecode');

				$scheme->pr =  str_replace(",", "", Input::get('pr'));
				$scheme->srp_p = str_replace(",", "", Input::get('srp_p'));
				$scheme->other_cost =str_replace(",", "", Input::get('other_cost'));

				$scheme->ulp =  str_replace(",", "", Input::get('ulp'));
				$scheme->cost_sale = str_replace(",", "", Input::get('cost_sale'));

				$scheme->quantity = str_replace(",", "", Input::get('total_alloc'));
				$scheme->deals = str_replace(",", "", Input::get('deals'));
				$scheme->tts_r =  str_replace(",", "", Input::get('tts_r'));
				$scheme->pe_r = str_replace(",", "", Input::get('pe_r'));
				$scheme->total_cost = str_replace(",", "", Input::get('total_cost'));
				$scheme->user_id = Auth::id();

				$scheme->update();

				$skus = array();
				SchemeSku::where('scheme_id',$scheme->id)->delete();
				foreach (Input::get('skus') as $sku){
					$skus[] = array('scheme_id' => $scheme->id, 'sku' => $sku);
				}
				SchemeSku::insert($skus);

				SchemeAllocRepository::updateAllocation($scheme);
				
			});
			// #schemes
			return Redirect::action('SchemeController@edit', array('id' => $id))
				->with('class', 'alert-success')
				->with('message', 'Scheme "'.Input::get('scheme_name').'" was successfuly updated.');
			
		}

		return Redirect::action('SchemeController@edit', array('id' => $id))
			->withInput()
			->withErrors($validation)
			->with('class', 'alert-danger')
			->with('message', 'There were validation errors.');
	}

	/**
	 * Remove the specified resource from storage.
	 * DELETE /scheme/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

	public function updateallocation(){
		// return json_encode(Input::all());
		if(Request::ajax()){
			$id = Input::get('scheme_id');
			$new_alloc = Input::get('new_alloc');
			$alloc = SchemeAllocation::find($id);

			if(empty($alloc)){
				$arr['success'] = 0;
			}else{
				$alloc->final_alloc = str_replace(",", "", $new_alloc);
				$alloc->update();
				$arr['success'] = 1;
				
			}
			
			$arr['id'] = $id;
			return json_encode($arr);
		}
	}

	public function allocation($id){
		// $scheme_customers = SchemeAllocation::where('scheme_id', $id)->get();
		$result = DB::table('allocations')
		->select('allocations.id','allocations.group','allocations.area','allocations.sold_to',
			'allocations.ship_to', 'allocations.channel', 'allocations.outlet', 'allocations.sold_to_gsv', 
			'allocations.sold_to_gsv_p', 'allocations.sold_to_alloc', 'allocations.ship_to_gsv',
			'allocations.ship_to_alloc' ,'allocations.outlet_to_gsv', 'allocations.outlet_to_gsv_p', 'allocations.outlet_to_alloc',
			'allocations.final_alloc' ,'allocations.customer_id', 'allocations.shipto_id')
		->where('scheme_id', $id);

		// echo '<pre>';
		// echo print_r($result);
		// echo '</pre>';
		return Datatables::of($result)
			->set_index_column('id')
			->edit_column('sold_to_gsv', function($row) {
				if($row->sold_to_gsv != 0){
					return number_format($row->sold_to_gsv,2);
				}
		    })
		    ->edit_column('sold_to_gsv_p', function($row) {
				if($row->sold_to_gsv_p != 0){
					return number_format($row->sold_to_gsv_p,2);
				}
		    })
		    ->edit_column('sold_to_alloc', function($row) {
				if($row->sold_to_alloc != 0){
					return number_format($row->sold_to_alloc);
				}
		    })
		    ->edit_column('ship_to_gsv', function($row) {
				if($row->ship_to_gsv != 0){
					return number_format($row->ship_to_gsv,2);
				}
		    })
		    ->edit_column('ship_to_alloc', function($row) {
				if($row->ship_to_alloc != 0){
					return number_format($row->ship_to_alloc);
				}
		    })
		    ->edit_column('outlet_to_gsv', function($row) {
				if($row->outlet_to_gsv != 0){
					return number_format($row->outlet_to_gsv,2);
				}
		    })
		    ->edit_column('outlet_to_gsv_p', function($row) {
				if($row->outlet_to_gsv_p != 0){
					return number_format($row->outlet_to_gsv_p,2);
				}
		    })
		    ->edit_column('outlet_to_alloc', function($row) {
				if($row->outlet_to_alloc != 0){
					return number_format($row->outlet_to_alloc);
				}
		    })
		    ->edit_column('final_alloc', function($row) {
				if($row->final_alloc > -1){
					return number_format($row->final_alloc);
				}
		    })
			->make(true);
	}
}