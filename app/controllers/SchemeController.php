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
				$customers = ActivityCustomer::customers($scheme->activity_id);
				$_channels = ActivityChannel::channels($scheme->activity_id);

				$_allocation = new AllocationRepository;
				$allocations = $_allocation->customers(Input::get('skus'), $_channels, $customers);
				$total_sales = $_allocation->total_sales();

				foreach ($allocations as $customer) {
					$scheme_alloc = new SchemeAllocation;
					$scheme_alloc->scheme_id = $scheme->id;
					$scheme_alloc->group = $customer->group_name;
					$scheme_alloc->area = $customer->area_name;
					$scheme_alloc->sold_to = $customer->customer_name;
					$scheme_alloc->ship_to = $customer->customer_name . ' TOTAL';
					$scheme_alloc->sold_to_gsv = $customer->gsv;	
					$sold_to_gsv_p = 0;
					if($customer->gsv > 0){
						if($total_sales > 0){
							$sold_to_gsv_p = round(($customer->gsv/$total_sales) * 100,2);
						}
					}
					$scheme_alloc->sold_to_gsv_p = $sold_to_gsv_p;
					$_sold_to_alloc = 0;
					if($total_sales > 0){
						$_sold_to_alloc = round(($customer->gsv/$total_sales) * $scheme->quantity);
					}
					$scheme_alloc->sold_to_alloc = $_sold_to_alloc;
					$scheme_alloc->save();

					if(!empty($customer->shiptos)){
						foreach($customer->shiptos as $shipto){
							$shipto_alloc = 0;
							$shipto_alloc = new SchemeAllocation;
							$shipto_alloc->scheme_id = $scheme->id;
							$shipto_alloc->customer_id = $scheme_alloc->id;
							$shipto_alloc->group = $customer->group_name;
							$shipto_alloc->area = $customer->area_name;
							$shipto_alloc->sold_to = $customer->customer_name;
							$shipto_alloc->ship_to = $shipto['ship_to_name'];
							$shipto_alloc->ship_to_gsv = $shipto['gsv'];
							$_shipto_alloc = 0;
							if(!is_null($shipto['split'])){
								if($scheme_alloc->sold_to_alloc > 0){
									$_shipto_alloc = round(($scheme_alloc->sold_to_alloc * $shipto['split']) / 100);
								}
							}else{
								if($shipto['gsv'] >0){
									$_shipto_alloc = round(round($shipto['gsv'] / $customer->ado_total,2) * $scheme_alloc->sold_to_alloc);
								}
							}
							$shipto_alloc->ship_to_alloc = $_shipto_alloc;
							$shipto_alloc->save();	

							if(!empty($shipto['accounts'] )){
								$others = $shipto_alloc->ship_to_alloc;
								foreach($shipto['accounts'] as $account){
									$account_alloc = new SchemeAllocation;
									$account_alloc->scheme_id = $scheme->id;
									$account_alloc->customer_id = $scheme_alloc->id;
									$account_alloc->shipto_id = $shipto_alloc->id;
									$account_alloc->group = $customer->group_name;
									$account_alloc->area = $customer->area_name;
									$account_alloc->sold_to = $customer->customer_name;
									$account_alloc->ship_to = $shipto['ship_to_name'];
									$account_alloc->channel = $account['channel_name'];
									$account_alloc->outlet = $account['account_name'];
									$account_alloc->outlet_to_gsv = $account['gsv'];
									$p = 0;
									if($customer->gsv > 0){
										$p = round($account['gsv']/$customer->gsv * 100,2);
									}
									$account_alloc->outlet_to_gsv_p = $p;
									$_account_alloc = round(($p * $shipto_alloc->ship_to_alloc)/100);
									$account_alloc->outlet_to_alloc = $_account_alloc;
									if($_account_alloc > 0){
										$others -= $_account_alloc;
									}
									$account_alloc->save();
								}
								$_others_alloc = 0;
								$others_alloc = new SchemeAllocation;
								$others_alloc->scheme_id = $scheme->id;
								$others_alloc->customer_id = $scheme_alloc->id;
								$others_alloc->shipto_id = $shipto_alloc->id;
								$others_alloc->group = $customer->group_name;
								$others_alloc->area = $customer->area_name;
								$others_alloc->sold_to = $customer->customer_name;
								$others_alloc->ship_to = $shipto['ship_to_name'];
								$others_alloc->outlet = 'OTHERS';
								$others_alloc->outlet_to_gsv = $account['gsv'];
								if($others > 0){
									$_others_alloc = $others;
								}
								$others_alloc->outlet_to_alloc = $_others_alloc;
								$others_alloc->save();
							}
						}
					}
				}

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

		$scheme_customers = SchemeAllocation::getCustomerAllocation($activity->id);
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

}