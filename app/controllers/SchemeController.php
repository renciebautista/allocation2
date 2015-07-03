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
		$divisions = ActivityDivision::getList($id);
		$categories = ActivityCategory::selected_category($id);
		$brands = ActivityBrand::selected_brand($id);

		$skus = Sku::items($divisions,$categories,$brands);
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

				$lpat = str_replace(",", "", Input::get('other_cost'));

				$scheme->lpat = $lpat;

				$pr = str_replace(",", "", Input::get('pr'));
				// if(!empty(Input::get('pr'))){
					
				// }else{
				// 	$pr = 0;
				// }
				$scheme->pr = $pr;
				$srp_p = str_replace(",", "", Input::get('srp_p'));
				// if(!empty(Input::get('srp_p'))){
				// 	$srp_p = str_replace(",", "", Input::get('srp_p'));
				// }else{
				// 	$srp_p = 0;
				// }
				$scheme->srp_p = $srp_p;
				$other_cost = str_replace(",", "", Input::get('other_cost'));
				// if(!empty(Input::get('other_cost'))){
				// 	$other_cost = str_replace(",", "", Input::get('other_cost'));
				// }else{
				// 	$other_cost = 0;
				// }
				$scheme->other_cost = $other_cost;

				$ulp = $srp_p + $other_cost;
				$scheme->ulp = $ulp;
				if(($ulp > 0) && ($pr > 0)){
					$scheme->cost_sale = ($ulp/$pr) * 100;
				}else{
					$scheme->cost_sale = 0;
				}
				
				$scheme->quantity = str_replace(",", "", Input::get('total_alloc'));
				$activitytype = ActivityType::find($activity->activity_type_id);
				$scheme->deals = str_replace(",", "", Input::get('deals'));

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
				$pe_r = $scheme->total_deals * $other_cost;;
				$scheme->pe_r = $pe_r;
				// $scheme->total_cost = str_replace(",", "", Input::get('total_cost'));
				$scheme->total_cost = $tts_r + $pe_r;

				$scheme->final_total_deals = $scheme->total_deal;
				$scheme->final_total_cases = $scheme->total_cases;
				$scheme->final_tts_r =$scheme->tts_r;
				$scheme->final_pe_r = $scheme->pe_r;
				$scheme->final_total_cost = $scheme->total_cost;

				$scheme->user_id = Auth::id();
				$scheme->ulp_premium = Input::get('ulp_premium');

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
		$divisions = ActivityDivision::getList($scheme->activity_id);
		$categories = ActivityCategory::selected_category($scheme->activity_id);
		$brands = ActivityBrand::selected_brand($scheme->activity_id);
		$skus = Sku::items($divisions,$categories,$brands);
		$involves = Pricelist::items();

		$sel_skus =  SchemeSku::getSkus($scheme->id);
		$sel_hosts = SchemeHostSku::getHosts($scheme->id);
		$sel_premuim = SchemePremuimSku::getPremuim($scheme->id);

		$sku = Sku::getSku($sel_skus[0]);
		$host = Pricelist::getSku($sel_hosts[0]);
		$premuim = Pricelist::getSku($sel_premuim[0]);

		$customers = ActivityCustomer::customers($scheme->activity_id);
		$_channels = ActivityChannel::channels($scheme->activity_id);
		$qty = $scheme->quantity;


		$allocations = Allocation::schemeAllocations($id);

		$ac_groups = AccountGroup::where('show_in_summary',1)->get();

		if(!empty($ac_groups)){
			foreach ($ac_groups as $ac_group) {
				$customer = array();
				foreach ($allocations  as $allocation) {
					if(!empty($allocation->account_group_name)){
						if($ac_group->account_group_name == $allocation->account_group_name){
							if(array_key_exists($allocation->outlet, $customer)){
								$customer[$allocation->outlet]->computed_alloc +=  $allocation->computed_alloc;
								$customer[$allocation->outlet]->force_alloc +=  $allocation->force_alloc;
								$customer[$allocation->outlet]->final_alloc +=  $allocation->final_alloc;
							}else{
								$object = new StdClass;
								$object->account_name = $allocation->outlet;
								$object->computed_alloc = $allocation->computed_alloc;
								$object->force_alloc = $allocation->force_alloc;
								$object->final_alloc = $allocation->final_alloc;
								$customer[$allocation->outlet] = $object;
							}
							
						}
					}
					
				}
				sort($customer);
				$ac_group->customers = $customer;
			}
		}

		$groups = array();
		foreach ($allocations  as $allocation) {
			if((empty($allocation->customer_id)) && (empty($allocation->shipto_id))){
				if(array_key_exists($allocation->group, $groups)){
					if(array_key_exists($allocation->area, $groups[$allocation->group]->area)){
						$groups[$allocation->group]->area[$allocation->area]->computed_alloc +=  $allocation->computed_alloc;
						$groups[$allocation->group]->area[$allocation->area]->force_alloc +=  $allocation->force_alloc;
						$groups[$allocation->group]->area[$allocation->area]->final_alloc +=  $allocation->final_alloc;
					}else{
						$area_object = new StdClass;
						$area_object->group = $allocation->group;
						$area_object->area_name = $allocation->area;
						$area_object->computed_alloc = $allocation->computed_alloc;
						$area_object->force_alloc = $allocation->force_alloc;
						$area_object->final_alloc = $allocation->final_alloc;
					}

					$groups[$allocation->group]->area[$allocation->area] =  $area_object;
					$groups[$allocation->group]->computed_alloc +=  $allocation->computed_alloc;
					$groups[$allocation->group]->force_alloc +=  $allocation->force_alloc;
					$groups[$allocation->group]->final_alloc +=  $allocation->final_alloc;
				}else{
					

					$area_object = new StdClass;
					$area_object->group = $allocation->group;
					$area_object->area_name = $allocation->area;
					$area_object->computed_alloc = $allocation->computed_alloc;
					$area_object->force_alloc = $allocation->force_alloc;
					$area_object->final_alloc = $allocation->final_alloc;
					

					$object = new StdClass;
					$object->group_name = $allocation->group;
					$object->computed_alloc = $allocation->computed_alloc;
					$object->force_alloc = $allocation->force_alloc;
					$object->final_alloc = $allocation->final_alloc;
					$object->area[$allocation->area] = $area_object;
					$groups[$allocation->group] = $object;
				}

			}
		}

		// Helper::print_r($ac_groups);
		$total_gsv = SchemeAllocation::totalgsv($id);

		if(Auth::user()->hasRole("PROPONENT")){
			if($activity->status_id < 4){
				return View::make('scheme.edit',compact('scheme', 'activity', 'skus', 'involves', 'sel_skus', 'sel_hosts',
					'sel_premuim','allocations', 'total_sales', 'qty','id','total_gsv', 'ac_groups', 'groups'));
			}else{
				return View::make('scheme.read_only',compact('scheme', 'activity', 'skus', 'involves', 'sel_skus', 'sel_hosts',
					'sel_premuim','allocations', 'total_sales', 'qty','id', 'summary', 'total_gsv','sku', 'host', 'premuim','ac_groups','groups'));
			}
		}

		if(Auth::user()->hasRole("PMOG PLANNER")){
			if($activity->status_id == 4){
				return View::make('scheme.edit',compact('scheme', 'activity', 'skus', 'involves', 'sel_skus', 'sel_hosts',
					'sel_premuim','allocations', 'total_sales', 'qty','id', 'summary', 'total_gsv','ac_groups', 'groups'));
			}else{
				return View::make('scheme.read_only',compact('scheme', 'activity', 'skus', 'involves', 'sel_skus', 'sel_hosts',
					'sel_premuim','allocations', 'total_sales', 'qty','id', 'summary', 'total_gsv','sku', 'host', 'premuim','ac_groups','groups'));
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
				$activity = Activity::find($scheme->activity_id);
				$scheme->name = strtoupper(Input::get('scheme_name'));
				$scheme->item_code = Input::get('item_code');
				$scheme->item_barcode = Input::get('item_barcode');
				$scheme->item_casecode = Input::get('item_casecode');

				$lpat = str_replace(",", "", Input::get('other_cost'));

				$scheme->lpat = $lpat;
				
				$pr = str_replace(",", "", Input::get('pr'));
				// if(!empty(Input::get('pr'))){
				// 	$pr = str_replace(",", "", Input::get('pr'));
				// }else{
				// 	$pr = 0;
				// }
				$scheme->pr = $pr;
				$srp_p = str_replace(",", "", Input::get('srp_p'));
				// if(!empty(Input::get('srp_p'))){
				// 	$srp_p = str_replace(",", "", Input::get('srp_p'));
				// }else{
				// 	$srp_p = 0;
				// }
				$scheme->srp_p = $srp_p;
				$other_cost = str_replace(",", "", Input::get('other_cost'));
				// if(!empty(Input::get('other_cost'))){
				// 	$other_cost = str_replace(",", "", Input::get('other_cost'));
				// }else{
				// 	$other_cost = 0;
				// }
				$scheme->other_cost = $other_cost;

				$ulp = $srp_p + $other_cost;
				$scheme->ulp = $ulp;
				if(($ulp > 0) && ($pr > 0)){
					$scheme->cost_sale = ($ulp/$pr) * 100;
				}else{
					$scheme->cost_sale = 0;
				}
				
				$scheme->quantity = str_replace(",", "", Input::get('total_alloc'));
				$activitytype = ActivityType::find($activity->activity_type_id);

				$scheme->deals = str_replace(",", "", Input::get('deals'));
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
				$pe_r = $scheme->total_deals * $other_cost;;
				$scheme->pe_r = $pe_r;
				// $scheme->total_cost = str_replace(",", "", Input::get('total_cost'));
				$scheme->total_cost = $tts_r + $pe_r;

				$scheme->final_total_deals = $scheme->total_deal;
				$scheme->final_total_cases = $scheme->total_cases;
				$scheme->final_tts_r =$scheme->tts_r;
				$scheme->final_pe_r = $scheme->pe_r;
				$scheme->final_total_cost = $scheme->total_cost;
				$scheme->ulp_premium = Input::get('ulp_premium');
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
				
				$final_pe = $total_deals *  $scheme->other_cost;
				
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
		$scheme = Scheme::findOrFail($id);
		if (is_null($scheme))
		{
			$class = 'alert-danger';
			$message = 'Scheme does not exist.';
		}else{

			DB::beginTransaction();

			try {
			   SchemeSku::where('scheme_id',$scheme->id)->delete();
				SchemeHostSku::where('scheme_id',$scheme->id)->delete();
				SchemePremuimSku::where('scheme_id',$scheme->id)->delete();
				SchemeAllocation::where('scheme_id',$scheme->id)->delete();
				$scheme->delete();

				DB::commit();
				$class = 'alert-success';
				$message = 'Scheme successfully deleted.';

				return Redirect::to(URL::action('ActivityController@edit', array('id' => $scheme->activity_id)) . "#schemes")
				->with('class', $class )
				->with('message', $message);
			    
			    // all good
			} catch (\Exception $e) {
			    DB::rollback();
			    $class = 'alert-danger';
				$message = 'Cannot delete scheme.';

				return Redirect::to(URL::action('ActivityController@edit', array('id' => $scheme->activity_id)) . "#schemes")
				->with('class', $class )
				->with('message', $message);
			    // something went wrong
			}			
			
		}

		return Redirect::to(URL::action('ActivityController@edit', array('id' => $scheme->activity_id)) . "#schemes")
				->with('class', $class )
				->with('message', $message);
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
				
				$final_pe = $total_deals *  $scheme->other_cost;
				
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
			'allocations.ship_to', 'allocations.channel', 'allocations.account_group_name', 'allocations.outlet', 'allocations.sold_to_gsv', 
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
		    ->edit_column('computed_alloc', function($row) {
				if($row->computed_alloc > -1){
					return number_format($row->computed_alloc);
				}
		    })
		    ->edit_column('force_alloc', function($row) {
				if($row->force_alloc > -1){
					return number_format($row->force_alloc);
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
		$allocations = SchemeAllocation::getAllocationsForExport($id);
		$scheme = Scheme::find($id);

		Excel::create($scheme->name, function($excel) use($allocations){
			$excel->sheet('allocations', function($sheet) use($allocations) {
				$sheet->fromModel($allocations);
			})->download('xls');

		});
	}

	public function duplicate($id){
		DB::beginTransaction();

		try {
			$scheme = Scheme::find($id);
			$new_scheme = new Scheme;
			$new_scheme->activity_id = $scheme->activity_id;
			$new_scheme->name = $scheme->name;
			$new_scheme->item_code = $scheme->item_code;
			$new_scheme->item_barcode = $scheme->item_barcode;
			$new_scheme->item_casecode = $scheme->item_casecode;
			$new_scheme->pr = $scheme->pr;
			$new_scheme->srp_p = $scheme->srp_p;
			$new_scheme->other_cost = $scheme->other_cost;
			$new_scheme->ulp = $scheme->ulp;
			$new_scheme->cost_sale = $scheme->cost_sale;
			$new_scheme->quantity = $scheme->quantity;
			$new_scheme->deals = $scheme->deals;
			$new_scheme->total_deals = $scheme->total_deals;
			$new_scheme->total_cases = $scheme->total_cases;
			$new_scheme->tts_r = $scheme->tts_r;
			$new_scheme->pe_r = $scheme->pe_r;
			$new_scheme->total_cost = $scheme->total_cost;
			$new_scheme->user_id = Auth::id();
			$new_scheme->final_alloc = $scheme->final_alloc;
			$new_scheme->final_total_deals = $scheme->final_total_deals;
			$new_scheme->final_total_cases = $scheme->final_total_cases;
			$new_scheme->final_tts_r = $scheme->final_tts_r;
			$new_scheme->final_pe_r = $scheme->final_pe_r;
			$new_scheme->final_total_cost = $scheme->final_total_cost;
			$new_scheme->save();

			// add skus
			$scheme_skus = SchemeSku::where('scheme_id',$scheme->id)->get();
			if(!empty($scheme_skus)){
				foreach ($scheme_skus as $sku) {
					SchemeSku::insert(array('scheme_id' => $new_scheme->id, 'sku' => $sku->sku));
				}
			}
			// add host sku
			$host_skus = SchemeHostSku::where('scheme_id',$scheme->id)->get();
			if(!empty($host_skus)){
				foreach ($host_skus as $sku) {
					SchemeHostSku::insert(array('scheme_id' => $new_scheme->id, 'sap_code' => $sku->sap_code));
				}
			}

			// add premuim sku
			$premuim_skus = SchemePremuimSku::where('scheme_id',$scheme->id)->get();
			if(!empty($premuim_skus)){
				foreach ($premuim_skus as $sku) {
					SchemePremuimSku::insert(array('scheme_id' => $new_scheme->id, 'sap_code' => $sku->sap_code));
				}
			}

			$allocations = Allocation::schemeAllocations($scheme->id);
			$last_area_id = 0;
			$last_shipto_id = 0;
			foreach ($allocations as $allocation) {
				$scheme_alloc = new SchemeAllocation;

				if((!empty($allocation->customer_id)) && (empty($allocation->shipto_id))){
					$scheme_alloc->customer_id = $last_area_id;
				}

				if((!empty($allocation->customer_id)) && (!empty($allocation->shipto_id))){
					$scheme_alloc->customer_id = $last_area_id;
					$scheme_alloc->shipto_id = $last_shipto_id;
				}
				
				$scheme_alloc->scheme_id = $new_scheme->id;
				$scheme_alloc->group = $allocation->group;
				$scheme_alloc->area = $allocation->area;
				$scheme_alloc->sold_to = $allocation->sold_to;
				$scheme_alloc->ship_to = $allocation->ship_to;
				$scheme_alloc->channel = $allocation->channel;
				$scheme_alloc->account_group_name = $allocation->account_group_name;
				$scheme_alloc->outlet = $allocation->outlet;
				$scheme_alloc->sold_to_gsv = $allocation->sold_to_gsv;
				$scheme_alloc->sold_to_gsv_p = $allocation->sold_to_gsv_p;
				$scheme_alloc->sold_to_alloc = $allocation->sold_to_alloc;
				$scheme_alloc->ship_to_gsv = $allocation->ship_to_gsv;
				$scheme_alloc->ship_to_gsv_p = $allocation->ship_to_gsv_p;
				$scheme_alloc->ship_to_alloc = $allocation->ship_to_alloc;
				$scheme_alloc->outlet_to_gsv = $allocation->outlet_to_gsv;
				$scheme_alloc->outlet_to_gsv_p = $allocation->outlet_to_gsv_p;
				$scheme_alloc->outlet_to_alloc = $allocation->outlet_to_alloc;
				$scheme_alloc->multi = $allocation->multi;
				$scheme_alloc->computed_alloc = $allocation->computed_alloc;
				$scheme_alloc->force_alloc = $allocation->force_alloc;
				$scheme_alloc->final_alloc = $allocation->final_alloc;
				$scheme_alloc->in_deals = $allocation->in_deals;
				$scheme_alloc->in_cases = $allocation->in_cases;
				$scheme_alloc->tts_budget = $allocation->tts_budget;
				$scheme_alloc->pe_budget = $allocation->pe_budget;
				$scheme_alloc->save();

				if((empty($allocation->customer_id)) && (empty($allocation->shipto_id))){
					$last_area_id = $scheme_alloc->id;
				}

				if((!empty($allocation->customer_id)) && (!empty($allocation->shipto_id))){
					$last_shipto_id = $scheme_alloc->id;
				}
			}
			DB::commit();
			$class = 'alert-success';
			$message = 'Scheme  successfully duplicated.';
		} catch (\Exception $e) {
				DB::rollback();
				// echo $e;
			    $class = 'alert-danger';
				$message = 'Cannot duplicate activity.';
				// something went wrong
		}

		return Redirect::to(URL::action('ActivityController@edit', array('id' => $scheme->activity_id)) . "#schemes")
				->with('class', $class )
				->with('message', $message);	
		
	}

	public function duplicatescheme($id){
		DB::beginTransaction();

		try {
			$scheme = Scheme::find($id);
			$new_scheme = new Scheme;
			$new_scheme->activity_id = $scheme->activity_id;
			$new_scheme->name = $scheme->name;
			$new_scheme->item_code = $scheme->item_code;
			$new_scheme->item_barcode = $scheme->item_barcode;
			$new_scheme->item_casecode = $scheme->item_casecode;
			$new_scheme->pr = $scheme->pr;
			$new_scheme->srp_p = $scheme->srp_p;
			$new_scheme->other_cost = $scheme->other_cost;
			$new_scheme->ulp = $scheme->ulp;
			$new_scheme->cost_sale = $scheme->cost_sale;
			$new_scheme->quantity = $scheme->quantity;
			$new_scheme->deals = $scheme->deals;
			$new_scheme->total_deals = $scheme->total_deals;
			$new_scheme->total_cases = $scheme->total_cases;
			$new_scheme->tts_r = $scheme->tts_r;
			$new_scheme->pe_r = $scheme->pe_r;
			$new_scheme->total_cost = $scheme->total_cost;
			$new_scheme->user_id = Auth::id();
			$new_scheme->final_alloc = $scheme->final_alloc;
			$new_scheme->final_total_deals = $scheme->final_total_deals;
			$new_scheme->final_total_cases = $scheme->final_total_cases;
			$new_scheme->final_tts_r = $scheme->final_tts_r;
			$new_scheme->final_pe_r = $scheme->final_pe_r;
			$new_scheme->final_total_cost = $scheme->final_total_cost;
			$new_scheme->save();

			// add skus
			$scheme_skus = SchemeSku::where('scheme_id',$scheme->id)->get();
			if(!empty($scheme_skus)){
				foreach ($scheme_skus as $sku) {
					SchemeSku::insert(array('scheme_id' => $new_scheme->id, 'sku' => $sku->sku));
				}
			}
			// add host sku
			$host_skus = SchemeHostSku::where('scheme_id',$scheme->id)->get();
			if(!empty($host_skus)){
				foreach ($host_skus as $sku) {
					SchemeHostSku::insert(array('scheme_id' => $new_scheme->id, 'sap_code' => $sku->sap_code));
				}
			}

			// add premuim sku
			$premuim_skus = SchemePremuimSku::where('scheme_id',$scheme->id)->get();
			if(!empty($premuim_skus)){
				foreach ($premuim_skus as $sku) {
					SchemePremuimSku::insert(array('scheme_id' => $new_scheme->id, 'sap_code' => $sku->sap_code));
				}
			}

			$allocations = Allocation::schemeAllocations($scheme->id);
			$last_area_id = 0;
			$last_shipto_id = 0;
			foreach ($allocations as $allocation) {
				$scheme_alloc = new SchemeAllocation;

				if((!empty($allocation->customer_id)) && (empty($allocation->shipto_id))){
					$scheme_alloc->customer_id = $last_area_id;
				}

				if((!empty($allocation->customer_id)) && (!empty($allocation->shipto_id))){
					$scheme_alloc->customer_id = $last_area_id;
					$scheme_alloc->shipto_id = $last_shipto_id;
				}
				
				$scheme_alloc->scheme_id = $new_scheme->id;
				$scheme_alloc->group = $allocation->group;
				$scheme_alloc->area = $allocation->area;
				$scheme_alloc->sold_to = $allocation->sold_to;
				$scheme_alloc->ship_to = $allocation->ship_to;
				$scheme_alloc->channel = $allocation->channel;
				$scheme_alloc->account_group_name = $allocation->account_group_name;
				$scheme_alloc->outlet = $allocation->outlet;
				$scheme_alloc->sold_to_gsv = $allocation->sold_to_gsv;
				$scheme_alloc->sold_to_gsv_p = $allocation->sold_to_gsv_p;
				$scheme_alloc->sold_to_alloc = $allocation->sold_to_alloc;
				$scheme_alloc->ship_to_gsv = $allocation->ship_to_gsv;
				$scheme_alloc->ship_to_gsv_p = $allocation->ship_to_gsv_p;
				$scheme_alloc->ship_to_alloc = $allocation->ship_to_alloc;
				$scheme_alloc->outlet_to_gsv = $allocation->outlet_to_gsv;
				$scheme_alloc->outlet_to_gsv_p = $allocation->outlet_to_gsv_p;
				$scheme_alloc->outlet_to_alloc = $allocation->outlet_to_alloc;
				$scheme_alloc->multi = $allocation->multi;
				$scheme_alloc->computed_alloc = $allocation->computed_alloc;
				$scheme_alloc->force_alloc = $allocation->force_alloc;
				$scheme_alloc->final_alloc = $allocation->final_alloc;
				$scheme_alloc->in_deals = $allocation->in_deals;
				$scheme_alloc->in_cases = $allocation->in_cases;
				$scheme_alloc->tts_budget = $allocation->tts_budget;
				$scheme_alloc->pe_budget = $allocation->pe_budget;
				$scheme_alloc->save();

				if((empty($allocation->customer_id)) && (empty($allocation->shipto_id))){
					$last_area_id = $scheme_alloc->id;
				}

				if((!empty($allocation->customer_id)) && (!empty($allocation->shipto_id))){
					$last_shipto_id = $scheme_alloc->id;
				}
			}
			DB::commit();
			$class = 'alert-success';
			$message = 'Scheme successfully duplicated.';
		} catch (\Exception $e) {
				DB::rollback();
				// echo $e;
			    $class = 'alert-danger';
				$message = 'Cannot duplicate activity.';
				// something went wrong
		}

		return Redirect::to(URL::action('SchemeController@edit', array('id' => $new_scheme->id)))
				->with('class', $class )
				->with('message', $message);
	} 
}