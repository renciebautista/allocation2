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

		$host = Pricelist::involves($brands,$activity);
		$premuim =  Pricelist::items();
		return View::make('scheme.create', compact('activity','skus', 'host', 'premuim'));
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

				$ulp = $srp_p + $other_cost;
				$scheme->ulp = $ulp;

				$lpat = str_replace(",", "", Input::get('lpat'));
				$scheme->lpat = $lpat;

				if(($lpat > 0) && ($ulp > 0)){
					$scheme->cost_sale = ($ulp/$lpat) * 100;
					
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
				
				if(Input::get('ulp_premium') != ""){
					$tts_r = 0;
					$non = $srp_p * $scheme->total_deals;
					$per = $scheme->total_deals * $scheme->other_cost;
					$pe_r = $non+$per;
				}else{
					// $scheme->tts_r =  str_replace(",", "", Input::get('tts_r'));
					$tts_r = $scheme->quantity * $scheme->deals * $srp_p;
					
					// $scheme->pe_r = str_replace(",", "", Input::get('pe_r'));
					$pe_r = $scheme->total_deals * $other_cost;
					
				}

				$scheme->tts_r =  $tts_r;
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

				// add scheme sku
				SchemeRepository::addSchemeSku($scheme);
				
				// add scheme sku involve
				SchemeRepository::addSchemeSkuInvole($scheme);

				// add scheme sku involve
				SchemeRepository::addSchemePremiumSku($scheme);
				
				// create allocation
				SchemeAllocRepository::insertAlllocation($scheme);

				// update final alloc
				$scheme2 = Scheme::find($scheme->id);
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

				$per = 0;
				if(Input::get('ulp_premium') != ""){
					$scheme2->final_tts_r = 0;
					$non = $srp_p * $total_deals;
					$per = $total_deals * $other_cost;
					$scheme2->final_pe_r = $non+$per;
				}else{
					$scheme2->final_tts_r = $final_tts;
					$scheme2->final_pe_r = $per;
				}
				
				
				$scheme2->final_total_cost = $scheme2->final_tts_r+$scheme2->final_pe_r;
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

		$activity_schemes = Scheme::getIdList($activity->id);
		$id_index = array_search($id, $activity_schemes);

		$divisions = ActivityDivision::getList($scheme->activity_id);
		$categories = ActivityCategory::selected_category($scheme->activity_id);
		$brands = ActivityBrand::selected_brand($scheme->activity_id);
		$skus = Sku::items($divisions,$categories,$brands);
		// $involves = Pricelist::items();

		$host_sku = Pricelist::involves($brands,$activity);
		$premuim_sku =  Pricelist::items();

		$sel_skus =  SchemeSku::getSkus($scheme->id);
		$sel_hosts = SchemeHostSku::getHosts($scheme->id);
		$sel_premuim = SchemePremuimSku::getPremuim($scheme->id);
		




		$premuim = array();
		if(!empty($sel_premuim)){
			$premuim = Pricelist::getSku($sel_premuim[0]);
		}
		
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

		$ref_sku = SchemeSku::where('scheme_id',$scheme->id)->first();
		// Helper::print_r($ref_sku);
		$total_gsv = SchemeAllocation::totalgsv($id);

		if(Auth::user()->hasRole("PROPONENT")){
			if($activity->status_id < 4){
				return View::make('scheme.edit',compact('scheme', 'activity_schemes', 'id_index', 'activity', 'skus', 'sel_skus', 'sel_hosts',
					'sel_premuim','allocations', 'total_sales', 'qty','id','total_gsv', 'ac_groups', 'groups','host_sku','premuim_sku'));
			}else{
				return View::make('scheme.read_only',compact('scheme', 'activity', 'activity_schemes', 'id_index', 'skus', 'involves', 'sel_skus', 'sel_hosts',
					'sel_premuim','allocations', 'total_sales', 'qty','id', 'summary', 'total_gsv','sku', 'host', 'premuim','ac_groups','groups',
					'host_sku','premuim_sku','ref_sku'));
			}
		}

		if(Auth::user()->hasRole("PMOG PLANNER")){
			if($activity->status_id == 4){
				return View::make('scheme.edit',compact('scheme', 'activity_schemes', 'id_index', 'activity', 'skus', 'sel_skus', 'sel_hosts',
					'sel_premuim','allocations', 'total_sales', 'qty','id', 'summary', 'total_gsv','ac_groups', 'groups','host_sku','premuim_sku'));
			}else{
				return View::make('scheme.read_only',compact('scheme', 'activity_schemes', 'id_index', 'activity', 'skus', 'sel_skus', 'sel_hosts',
					'sel_premuim','allocations', 'total_sales', 'qty','id', 'summary', 'total_gsv','sku', 'host', 'premuim','ac_groups','groups',
					'host_sku','premuim_sku','ref_sku'));
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
				
				$pr = str_replace(",", "", Input::get('pr'));

				$scheme->pr = $pr;
				$srp_p = str_replace(",", "", Input::get('srp_p'));
	
				$scheme->srp_p = $srp_p;
				$other_cost = str_replace(",", "", Input::get('other_cost'));

				$scheme->other_cost = $other_cost;

				$ulp = $srp_p + $other_cost;
				$scheme->ulp = $ulp;

				$lpat = str_replace(",", "", Input::get('lpat'));
				$scheme->lpat = $lpat;

				if(($lpat > 0) && ($ulp > 0)){
					$scheme->cost_sale = ($ulp/$lpat) * 100;
					
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
				
				if(Input::get('ulp_premium') != ""){
					$tts_r = 0;
					$non = $srp_p * $scheme->total_deals;
					$per = $scheme->total_deals * $scheme->other_cost;
					$pe_r = $non+$per;
				}else{
					// $scheme->tts_r =  str_replace(",", "", Input::get('tts_r'));
					$tts_r = $scheme->quantity * $scheme->deals * $srp_p;
					
					// $scheme->pe_r = str_replace(",", "", Input::get('pe_r'));
					$pe_r = $scheme->total_deals * $other_cost;
					
				}
				$scheme->tts_r =  $tts_r;
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

				// update scheme sku
				SchemeRepository::addSchemeSku($scheme);
				
				// update scheme sku involve
				SchemeRepository::addSchemeSkuInvole($scheme);

				// update scheme sku involve
				SchemeRepository::addSchemePremiumSku($scheme);

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

				$per = 0;
				if(Input::get('ulp_premium') != ""){
					$scheme2->final_tts_r = 0;
					$non = $srp_p * $total_deals;
					$per = $total_deals * $other_cost;
					$scheme2->final_pe_r = $non+$per;
				}else{
					$scheme2->final_tts_r = $final_tts;
					$scheme2->final_pe_r = $final_pe;
				}
				
				
				$scheme2->final_total_cost = $scheme2->final_tts_r+$scheme2->final_pe_r;
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
		// dd(Input::all());
		if(Request::ajax()){
			$id = Input::get('scheme_id');
			$new_alloc = Input::get('new_alloc');
			$alloc = SchemeAllocation::find($id);

			if(empty($alloc)){
				$arr['success'] = 0;
			}else{
				$scheme = Scheme::find($alloc->scheme_id);
				$alloc->final_alloc = str_replace(",", "", $new_alloc);

				$in_deals = 0;
				$in_cases = 0;
				if($scheme->activity->activitytype->uom == 'CASES'){
					$in_deals = $alloc->final_alloc * $scheme->deals;
					$in_cases = $alloc->final_alloc;
					$tts_budget =$alloc->final_alloc * $scheme->deals * $scheme->srp_p; 
				}else{
					if($alloc->final_alloc > 0){
						$in_cases = round($alloc->final_alloc/$scheme->deals);
						$in_deals =  $alloc->final_alloc;
					}
					$tts_budget = $alloc->final_alloc * $scheme->srp_p;
				}

				$alloc->in_deals = $in_deals;
				$alloc->in_cases = $in_cases;
				$alloc->tts_budget = $tts_budget;
				$alloc->pe_budget = $alloc->final_alloc *  $scheme->other_cost;
				$alloc->update();

				SchemeAllocation::recomputeAlloc($alloc,$scheme);

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
				$sheet->fromModel($allocations,null, 'A1', true);
			})->download('xls');

		});
	}



	public function duplicate($id){
		$scheme = Scheme::find($id);
		$data = SchemeRepository::duplicate($id);
		return Redirect::to(URL::action('ActivityController@edit', array('id' => $scheme->activity_id)) . "#schemes")
				->with('class', $data['class'] )
				->with('message', $data['message']);	
		
	}

	public function duplicatescheme($id){
		$data = SchemeRepository::duplicate($id);
		return Redirect::to(URL::action('SchemeController@edit', array('id' => $data['scheme_id'])))
				->with('class', $data['class'] )
				->with('message', $data['message']);
	} 
}