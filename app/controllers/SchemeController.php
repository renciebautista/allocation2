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
		$skus = Sku::items($activity->division_code,$categories,$brands);
		$involves = Pricelist::items();
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
			$insert_id = DB::transaction(function() use ($id)  {
				$total_sales = 0;
				$activity = Activity::find($id);

				$scheme = new Scheme;
				$scheme->activity_id = $activity->id;

				$scheme->name = strtoupper(Input::get('scheme_name'));
				$scheme->item_code = Input::get('item_code');
				$scheme->item_barcode = Input::get('item_barcode');
				$scheme->item_casecode = Input::get('item_casecode');
				$pr = str_replace(",", "", Input::get('pr'));
				$scheme->pr = $pr;
				$srp_p = str_replace(",", "", Input::get('srp_p'));
				$scheme->srp_p = $srp_p;
				$other_cost = str_replace(",", "", Input::get('other_cost'));
				$scheme->other_cost = $other_cost;

				// $scheme->ulp =  str_replace(",", "", Input::get('ulp'));
				$ulp = $srp_p + $other_cost;
				$scheme->ulp = $ulp;
				// $scheme->cost_sale = str_replace(",", "", Input::get('cost_sale'));
				$scheme->cost_sale = ($ulp/$pr) * 100;

				$scheme->quantity = str_replace(",", "", Input::get('total_alloc'));

				$activitytype = ActivityType::find($activity->activity_type_id);

				$scheme->deals = str_replace(",", "", Input::get('deals'));
				// $scheme->total_deals = str_replace(",", "", Input::get('total_deals'));
				// $scheme->total_cases = str_replace(",", "", Input::get('total_cases'));

				if($activitytype->uom == "CASES"){
					$scheme->total_deals = $scheme->quantity * $scheme->deals;
					$scheme->total_cases = $scheme->quantity;
				}else{
					$scheme->total_deals = $scheme->quantity;
					$scheme->total_cases = round($scheme->quantity/ $scheme->deals);
				}
				
				
				// $scheme->tts_r =  str_replace(",", "", Input::get('tts_r'));
				$tts_r = $scheme->quantity * $scheme->deals * $srp_p;;
				$scheme->tts_r =  $tts_r;
				// $scheme->pe_r = str_replace(",", "", Input::get('pe_r'));
				$pe_r = $scheme->quantity * $other_cost;;
				$scheme->pe_r = $pe_r;
				// $scheme->total_cost = str_replace(",", "", Input::get('total_cost'));
				$scheme->total_cost = $tts_r + $pe_r;

				$scheme->final_total_deals = $scheme->total_deal;
				$scheme->final_total_cases = $scheme->total_cases;
				$scheme->final_tts_r =$scheme->tts_r;
				$scheme->final_pe_r = $scheme->pe_r;
				$scheme->final_total_cost = $scheme->total_cost;

				$scheme->user_id = Auth::id();

				$scheme->save();

				$skus = array();
				foreach (Input::get('skus') as $sku){
					$skus[] = array('scheme_id' => $scheme->id, 'sku' => $sku);
				}
				SchemeSku::insert($skus);

				$hosts = array();
				foreach (Input::get('involve') as $sap_code){
					$hosts[] = array('scheme_id' => $scheme->id, 'sap_code' => $sap_code);
				}
				SchemeHostSku::insert($hosts);

				$premuim = array();
				foreach (Input::get('premuim') as $sap_code){
					$premuim[] = array('scheme_id' => $scheme->id, 'sap_code' => $sap_code);
				}
				SchemePremuimSku::insert($hosts);

				// create allocation
				SchemeAllocRepository::insertAlllocation($scheme);

				$scheme2 = Scheme::find($scheme->id);
				$scheme2->final_alloc = SchemeAllocation::finalallocation($scheme->id);
				$scheme2->update();

				return $scheme->id;
			});
			return Redirect::to(URL::action('SchemeController@edit', array('id' => $insert_id)))
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
		return Redirect::action('SchemeController@edit',$id);
		// $scheme = Scheme::find($id);
		// $skus = SchemeSku::getSkus($id);
		// $customers = ActivityCustomer::customers($scheme->activity_id);

		// // $channels = array('C1', 'C2', 'C3');

		// $_channels = ActivityChannel::channels($scheme->activity_id);

		// $qty = $scheme->quantity;

		// $_allocation = new AllocationRepository;
		// $allocations = $_allocation->customers($skus, $_channels, $customers);
		
		// $total_sales = $_allocation->total_sales();

		// $summary = $_allocation->allocation_summary();
		// $big10 = $_allocation->account_group("AG4");
		// $gaisanos = $_allocation->account_group("AG5");
		// $nccc = $_allocation->account_group("AG6");
		// // echo '<pre>';
		// // print_r($big10);
		// // echo '</pre>';
		// // $channels = array();
		// // $groups = $_allocation->groups();
		// // $areas = $_allocation->areas();
		// // $soldtos = $_allocation->soldtos();
		// return View::make('scheme.show', compact('allocations','total_sales',
		// 	'qty','id', 'summary', 'big10', 'gaisanos', 'nccc'));
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
		$skus = Sku::items($activity->division_code,$categories,$brands);
		$involves = Pricelist::items();

		$sel_skus =  SchemeSku::getSkus($scheme->id);
		$sel_hosts = SchemeHostSku::getHosts($scheme->id);
		$sel_premuim = SchemePremuimSku::getPremuim($scheme->id);

		$sku = Sku::getSku($sel_skus[0]);
		$host = Pricelist::getSku($sel_hosts[0]);
		$premuim = Pricelist::getSku($sel_premuim[0]);

		// print_r($sel_skus);
		$customers = ActivityCustomer::customers($scheme->activity_id);
		// print_r($customers);
		$_channels = ActivityChannel::channels($scheme->activity_id);
		// print_r($_channels);
		$qty = $scheme->quantity;
		// print_r($qty);
		$_allocation = new AllocationRepository;
		
		$allocations = $_allocation->customers($sel_skus, $_channels, $customers);
		// Helper::print_r($allocations);
		$total_sales = $_allocation->total_gsv();

		$summary = $_allocation->allocation_summary();
		$big10 = $_allocation->account_group("AG4");
		$gaisanos = $_allocation->account_group("AG5");
		$nccc = $_allocation->account_group("AG6");

		$total_gsv = SchemeAllocation::totalgsv($id);

		if(Auth::user()->hasRole("PROPONENT")){
			if($activity->status_id < 4){
				return View::make('scheme.edit',compact('scheme', 'activity', 'skus', 'involves', 'sel_skus', 'sel_hosts',
					'sel_premuim',
					'allocations', 'total_sales', 'qty','id', 'summary', 'big10', 'gaisanos', 'nccc', 'scheme_customers',
					 'total_gsv'));
			}else{
				return View::make('scheme.read_only',compact('scheme', 'activity', 'skus', 'involves', 'sel_skus', 'sel_hosts',
					'sel_premuim',
					'allocations', 'total_sales', 'qty','id', 'summary', 'big10', 'gaisanos', 'nccc', 'scheme_customers',
					 'total_gsv'));
			}
		}

		if(Auth::user()->hasRole("PMOG PLANNER")){
			if($activity->status_id == 4){
				return View::make('scheme.edit',compact('scheme', 'activity', 'skus', 'involves', 'sel_skus', 'sel_hosts',
					'sel_premuim',
					'allocations', 'total_sales', 'qty','id', 'summary', 'big10', 'gaisanos', 'nccc', 'scheme_customers',
					 'total_gsv'));
			}else{
				return View::make('scheme.read_only',compact('scheme', 'activity', 'skus', 'involves', 'sel_skus', 'sel_hosts',
					'sel_premuim',
					'allocations', 'total_sales', 'qty','id', 'summary', 'big10', 'gaisanos', 'nccc', 'scheme_customers',
					 'total_gsv','sku', 'host', 'premuim'));
			}
		}

		
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
				$scheme->total_deals = str_replace(",", "", Input::get('total_deals'));
				$scheme->total_cases = str_replace(",", "", Input::get('total_cases'));

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

				$hosts = array();
				SchemeHostSku::where('scheme_id',$scheme->id)->delete();
				foreach (Input::get('involve') as $sap_code){
					$hosts[] = array('scheme_id' => $scheme->id, 'sap_code' => $sap_code);
				}
				SchemeHostSku::insert($hosts);

				$premuim = array();
				SchemePremuimSku::where('scheme_id',$scheme->id)->delete();
				foreach (Input::get('premuim') as $sap_code){
					$premuim[] = array('scheme_id' => $scheme->id, 'sap_code' => $sap_code);
				}
				SchemePremuimSku::insert($premuim);

				SchemeAllocRepository::updateAllocation($scheme);
				
				// update final alloc
				$scheme2 = Scheme::find($id);
				$final_alloc = SchemeAllocation::finalallocation($scheme->id);
				$total_cases = 0;
				$total_deals = 0;
				if($scheme->activity->activitytype->uom == 'CASES'){
					$total_deals = $final_alloc * $scheme->deals;
					$total_cases = $final_alloc;
					$final_tts = $final_alloc * $scheme->deals * $scheme->srp_p; 
				}else{
					
					if($final_alloc > 0){
						$total_cases = round($final_alloc/$scheme->deals);
						$total_deals = $final_alloc;
					}
					$final_tts = $final_alloc * $scheme->srp_p; 
				}
				
				$final_pe = $final_alloc *  $scheme->other_cost;
				
				$scheme2->final_alloc = $final_alloc;
				$scheme2->final_total_deals = $total_deals;
				$scheme2->final_total_cases = $total_cases;
				$scheme2->final_tts_r = $final_tts;
				$scheme2->final_pe_r = $final_pe;
				$scheme2->final_total_cost = $final_tts+$final_pe;
				$scheme2->update();

				
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
		if(Request::ajax()){
			$id = Input::get('scheme_id');
			$new_alloc = Input::get('new_alloc');
			$alloc = SchemeAllocation::find($id);

			if(empty($alloc)){
				$arr['success'] = 0;
			}else{
				$alloc->final_alloc = str_replace(",", "", $new_alloc);
				$alloc->update();

				$scheme = Scheme::find($alloc->scheme_id);

				SchemeAllocation::recomputeAlloc($alloc);
				$final_alloc = SchemeAllocation::finalallocation($alloc->scheme_id);

				if($scheme->activity->activitytype->uom == 'CASES'){
					$total_deals = $final_alloc * $scheme->deals;
					$total_cases = $final_alloc;
					$final_tts = $final_alloc * $scheme->deals * $scheme->srp_p; 
				}else{
					$total_deals = $final_alloc;
					if($total_deals < 1){
						$total_cases = 0;
					}else{
						$total_cases = round($final_alloc/$total_deals);
					}
					$final_tts = $final_alloc * $scheme->srp_p; 
				}
				
				$final_pe = $final_alloc *  $scheme->other_cost;
				
				$scheme->final_alloc = $final_alloc;
				$scheme->final_total_deals = $total_deals;
				$scheme->final_total_cases = $total_cases;
				$scheme->final_tts_r = $final_tts;
				$scheme->final_pe_r = $final_pe;
				$scheme->final_total_cost = $final_tts+$final_pe;
				$scheme->update();

				$arr['srp_p'] = $scheme->srp_p;
				$arr['scheme_id'] = $scheme;
				$arr['final_total'] = $final_alloc;
				$arr['final_total_deals'] = $scheme->final_total_deals;
				$arr['final_total_cases'] = $scheme->final_total_cases;
				$arr['final_tts_r'] = $final_tts;
				$arr['final_pe_r'] = $final_pe;
				$arr['final_total_cost'] = $scheme->final_total_cost;
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
			'allocations.final_alloc' ,'allocations.customer_id', 'multi','allocations.shipto_id','allocations.computed_alloc', 'allocations.force_alloc')
		->where('scheme_id', $id)
		->orderBy('allocations.id');

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

	public function export($id){
		$allocations = SchemeAllocation::getExportAllocations($id);
		$scheme = Scheme::find($id);

		Excel::create($scheme->name, function($excel) use($allocations){
			$excel->sheet('allocations', function($sheet) use($allocations) {
				$sheet->fromModel($allocations);
				$sheet->setColumnFormat(array(
				    'L' => '0.00',
				));
			});

		})->download('xls');


	}
}