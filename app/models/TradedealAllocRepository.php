<?php

class TradedealAllocRepository  {

	public static function insertAlllocation($tradealscheme){
		self::save($tradealscheme);
	}

	public static function updateAllocation($tradealscheme){
		TradedealSchemeAllocation::where('tradedeal_scheme_id',$tradealscheme->id)->delete();
		self::save($tradealscheme);
	}


	private static function save($tradealscheme){
		$tradedeal = Tradedeal::find($tradealscheme->tradedeal_id);
		$activity = Activity::find($tradedeal->activity_id);
		$forced_areas = ForceAllocation::getForcedAreas($tradedeal->activity_id);

		$customers = ActivityCustomer::customers($tradedeal->activity_id);
		$_channels = ActivityChannel2::channels($tradedeal->activity_id);

		$scheme_skus = TradedealSchemeSku::select('tradedeal_scheme_skus.id', 'tradedeal_part_skus.ref_code', 'qty', 'host_cost', 'host_pcs_case', 'ref_pcs_case',
			'pre_code', 'pre_desc', 'pre_cost', 'pre_pcs_case', 'brand_shortcut', 'variant', 'tradedeal_part_skus.id as host_id', 'pre_variant', 'host_sku_format',
			'pre_brand_shortcut', 'pre_sku_format', 'pre_variant')
			->join('tradedeal_part_skus', 'tradedeal_scheme_skus.tradedeal_part_sku_id', '=', 'tradedeal_part_skus.id')
			->where('tradedeal_scheme_id', $tradealscheme->id)
			->orderBy('tradedeal_part_skus.id')
			->get();

		$trade_customers = TradedealSchemeChannel::getCustomers($tradealscheme);
		$td_customers = Customer::getForTradedeal();
		$skus = [];

		if($tradealscheme->tradedeal_uom_id == 1){
			$scheme_uom_abv = 'P';
			$scheme_uom_abv2 = 'PC';
		}
		if($tradealscheme->tradedeal_uom_id == 2){
			$scheme_uom_abv = 'D';
			$scheme_uom_abv2 = 'DZ';
		}
		if($tradealscheme->tradedeal_uom_id == 3){
			$scheme_uom_abv = 'C';
			$scheme_uom_abv2 = 'CS';
		}

	
		if($tradealscheme->tradedeal_type_id == 1){
			foreach ($scheme_skus as $row) {
				unset($hostskus);
				$hostskus[] = $row;
				$brand = $row->brand_shortcut;
				$month_year = date('ym',strtotime($activity->eimplementation_date));
				$host_variant = substr(strtoupper($row->variant),0,1);
				$series = TradeIndividualSeries::getSeries($month_year, $tradealscheme->id, $row->host_id);
				$deal_id = 'B'.$month_year.$scheme_uom_abv.$brand.$host_variant .sprintf("%02d", $series->series);
				self::generate_allocation($hostskus, $_channels, $customers,$forced_areas,$tradealscheme, $tradedeal, $deal_id, $trade_customers);

			}
		}else if($tradealscheme->tradedeal_type_id == 2){
			// foreach ($scheme_skus as $row) {
			// 	$skus[] = $row;
			// }
			// if(!$tradedeal->non_ulp_premium){
			// 	$collective_premium = TradedealPartSku::findOrFail($tradealscheme->pre_id);
			// }else{
			// 	$collective_premium = $tradedeal;
			// }
			

			// self::generate_allocation($_channels, $customers,$forced_areas,$trade_channels, $td_customers, $tradealscheme, $tradedeal, $skus, $collective_premium, $deal_id);
		}else{
			$lowest_cost = 0;
			$lowest_skus = [];
			$brands =[];
			foreach ($scheme_skus as $row) {
				if($lowest_cost == 0){
					$lowest_cost = $row->host_cost;
					$lowest_skus[0] = $row;
				}else{
					if($lowest_cost > $row->host_cost){
						$lowest_cost = $row->host_cost;
						$lowest_skus[0] = $row;
					}
				}
				$brands[] = $row->brand_shortcut;
			}

			

			if(!$tradedeal->non_ulp_premium){
				$collective_premium = TradedealPartSku::findOrFail($tradealscheme->pre_id);
			}else{
				$collective_premium = $tradedeal;
			}

			// generate scheme code
			$brand = array_unique($brands);
			$brand_short_cut = 'MTL';
			if(count($brand) == 1){
				$brand_short_cut = $brand[0];
			}
			$month_year = date('ym',strtotime($activity->eimplementation_date));
			$series = TradeCollectiveSeries::getSeries($month_year, $tradealscheme->id);
			$deal_id = $month_year.$scheme_uom_abv.$brand_short_cut.sprintf("%02d", $series->series);

			self::generate_allocation($lowest_skus, $_channels, $customers,$forced_areas, $tradealscheme, $tradedeal, $deal_id, $trade_customers, $collective_premium);
		}
		
	}

	private static function weekly_run_rates($tradealscheme, $sold_to_gsv, $hostsku ){
		$weekly_run_rates = 0;
		if($tradealscheme->tradedeal_type_id == 1){
			$weekly_run_rates = ( $sold_to_gsv / 52 ) * $hostsku[0]->host_cost * $hostsku[0]->host_pcs_case;
		}else if($tradealscheme->tradedeal_type_id == 2){
			// $shipto_alloc->tradedeal_scheme_sku_id = $sku->id;
			// $total_cost = 0;
			// $total_pur_req = 0;
			// foreach ($host_sku as $row) {
			// 	$total_cost +=  $row->host_cost;
			// 	$total_pur_req += $row->host_cost * $row->qty * $uom_multiplpier;
			// }
			// $weekly_run_rates = ( $sold_to_gsv / 52 ) * $total_cost * $host_sku[0]->ref_pcs_case ;
			// $pur_req = $total_pur_req;
		}else{
			$weekly_run_rates = ( $sold_to_gsv / 52 ) * $hostsku[0]->host_cost * $hostsku[0]->host_pcs_case;
		}

		return $weekly_run_rates;
	}

	private static function purchase_requirement($tradealscheme, $sold_to_gsv, $uom_multiplpier, $hostsku ){
		$purchase_requirement = 0;
		if($tradealscheme->tradedeal_type_id == 1){
			$purchase_requirement = $hostsku[0]->host_cost * $tradealscheme->buy * $uom_multiplpier;
		}else if($tradealscheme->tradedeal_type_id == 2){
			// $shipto_alloc->tradedeal_scheme_sku_id = $sku->id;
			// $total_cost = 0;
			// $total_pur_req = 0;
			// foreach ($host_sku as $row) {
			// 	$total_cost +=  $row->host_cost;
			// 	$total_pur_req += $row->host_cost * $row->qty * $uom_multiplpier;
			// }
			// $weekly_run_rates = ( $sold_to_gsv / 52 ) * $total_cost * $host_sku[0]->ref_pcs_case ;
			// $pur_req = $total_pur_req;
		}else{
			$purchase_requirement = $hostsku[0]->host_cost * $tradealscheme->buy * $uom_multiplpier;
		}

		return $purchase_requirement;
	}

	private static function computed_pcs($weekly_run_rates,$alloc_in_weeks, $pur_req, $uom_multiplpier, $free){
		$deals = round(($weekly_run_rates * $alloc_in_weeks) / $pur_req);
		$pcs = $deals * $uom_multiplpier * $free;
		if($pcs < 1){
			$pcs = 0;
		}
		return $pcs;
	}

	private static function generate_allocation($host_skus, $_channels, $customers,$forced_areas, $tradealscheme, $tradedeal, $deal_id, $trade_customers, $collective_premium = null){
		$allocationRepo = new AllocationRepository2;

		$ref_sku = [];
		foreach ($host_skus as $row) {
			$ref_sku[] = $row->ref_code;
		}

		$td_sub_channels = TradedealSchemeChannel::getSubChannels($tradealscheme);
		
		$gsvsales = $allocationRepo->customers($ref_sku, $_channels, $customers,$forced_areas, $td_sub_channels);

		// Helper::debug($gsvsales);
		
		if(!is_null($collective_premium)){
			if(isset($collective_premium->pre_code)){
				$premium['pre_code'] = $collective_premium->pre_code;
				$premium['pre_desc'] = $collective_premium->pre_desc;
				$premium['cost'] = $collective_premium->pre_cost;
				$premium['variant'] = $collective_premium->pre_variant;
			}else{
				$premium['pre_code'] = $collective_premium->non_ulp_premium_code;
				$premium['pre_desc'] = $collective_premium->non_ulp_premium_desc;
				$premium['cost'] = $collective_premium->non_ulp_premium_cost;
				$premium['variant'] = '';
			}
		}else{
			$premium['pre_code'] = $host_skus[0]->pre_code;
			$premium['pre_desc'] = $host_skus[0]->pre_desc;
			$premium['cost'] = $host_skus[0]->pre_cost;
			$premium['variant'] = $host_skus[0]->pre_variant;
		}
		
		$scheme_desc = self::generateSchemeDesc($tradedeal, $tradealscheme, $host_skus[0]);

		$uom = $tradealscheme->tradedeal_uom_id;

		$uom_multiplpier  = 1;
		if($uom == 1){
			$uom_multiplpier  = 1;
		}
		if($uom == 2){
			$uom_multiplpier  = 12;
		}
		if($uom == 3){
			$uom_multiplpier = $host_skus[0]->host_pcs_case;
		}

		$uom_premium  = 1;
		if($uom == 1){
			$uom_premium  = 1;
		}
		if($uom == 2){
			$uom_premium  = 12;
		}
		if($uom == 3){
			if(!is_null($collective_premium)){
				
				if(!$tradedeal->non_ulp_premium){
					$uom_premium = $collective_premium->pre_pcs_case;
				}else{
					$uom_premium = $collective_premium->non_ulp_pcs_case;
				}
			}else{
				$uom_premium = $host_skus[0]->pre_pcs_case;
			}
			
		}
		
		$tradedeal_scheme_sku_id = 0;
		if($tradealscheme->tradedeal_type_id == 1){
			$tradedeal_scheme_sku_id = $host_skus[0]->id;
		}

		$ship_nodes = [];
		$cust_nodes = [];
		
		
		if(!empty($customers)){
			foreach ($customers as $selected_customer) {
				$n = explode('.', $selected_customer);
				if(count($n) == 1){
					$n_nodes = CustomerTree::where('channel_code', $n[0])->get();
					foreach ($n_nodes as $n_node) {

						if($n_node->plant_code != ''){
							$ship_nodes[] = $n_node->plant_code;
						}

						$cust_nodes[] = $n_node->customer_code;

					}
					
				}

				if(count($n) == 2){
					$n_nodes = CustomerTree::where('channel_code', $n[0])
						->where('group_code', $n[1])
						->get();
					foreach ($n_nodes as $n_node) {
						if($n_node->plant_code != ''){
							$ship_nodes[] = $n_node->plant_code;
						}

						$cust_nodes[] = $n_node->customer_code;
					}
					
				}

				if(count($n) == 3){
					$n_nodes = CustomerTree::where('channel_code', $n[0])
						->where('group_code', $n[1])
						->where('area_code', $n[2])
						->get();
					foreach ($n_nodes as $n_node) {
						if($n_node->plant_code != ''){
							$ship_nodes[] = $n_node->plant_code;
						}

						$cust_nodes[] = $n_node->customer_code;
					}
					
				}

				if(count($n) == 4){
					$n_nodes = CustomerTree::where('channel_code', $n[0])
						->where('group_code', $n[1])
						->where('area_code', $n[2])
						->where('customer_code', $n[3])
						->get();
					foreach ($n_nodes as $n_node) {
						if($n_node->plant_code != ''){
							$ship_nodes[] = $n_node->plant_code;
						}

						$cust_nodes[] = $n_node->customer_code;
					}
					
				}

				if(count($n) == 5){
					$n_nodes = CustomerTree::where('channel_code', $n[0])
						->where('group_code', $n[1])
						->where('area_code', $n[2])
						->where('customer_code', $n[3])
						->where('plant_code', $n[4])
						->get();
					foreach ($n_nodes as $n_node) {
						if($n_node->plant_code != ''){
							$ship_nodes[] = $n_node->plant_code;
						}

						$cust_nodes[] = $n_node->customer_code;
					}
					
				}

				if(count($n) == 6){
					$n_nodes = CustomerTree::where('channel_code', $n[0])
						->where('group_code', $n[1])
						->where('area_code', $n[2])
						->where('customer_code', $n[3])
						->where('plant_code', $n[4])
						->where('account_id', $n[5])
						->get();
					foreach ($n_nodes as $n_node) {
						if($n_node->plant_code != ''){
							$ship_nodes[] = $n_node->plant_code;
						}

						$cust_nodes[] = $n_node->customer_code;
					}
					
				}
			}
		}

		$selected_customers = array_unique($cust_nodes);
		$selected_shiptos = array_unique($ship_nodes);

		foreach ($gsvsales as $customer) {
			// if((in_array($customer->customer_code, $trade_customers))){
				if($customer->trade_deal == 1){
					if(in_array($customer->customer_code, $selected_customers)){
						if(!empty($customer->shiptos)){
							foreach ($customer->shiptos as $shipto) {
								if(in_array($shipto['plant_code'], $selected_shiptos)){
									$alloc = new TradedealSchemeAllocation;
									$alloc->tradedeal_scheme_id = $tradealscheme->id;
									$alloc->tradedeal_scheme_sku_id = $tradedeal_scheme_sku_id; 
									$alloc->scheme_code = $deal_id; 
									$alloc->scheme_desc = $scheme_desc;
									$alloc->area_code = $customer->area_code;
									$alloc->area = $customer->area_name;
									$alloc->sold_to_code = $customer->customer_code;
									$alloc->sold_to =  $customer->customer_name;
									$alloc->ship_to_code = $shipto['ship_to_code'];
									$alloc->plant_code = $shipto['plant_code'];
									$alloc->ship_to_name = $shipto['ship_to_name'];
									$alloc->sold_to_gsv = $shipto['gsv']; 
									$alloc->weekly_run_rates = self::weekly_run_rates($tradealscheme, $shipto['gsv'], $host_skus);
									$alloc->pur_req = self::purchase_requirement($tradealscheme, $shipto['gsv'], $uom_multiplpier, $host_skus);
									$alloc->computed_pcs = self::computed_pcs($alloc->weekly_run_rates,$tradedeal->alloc_in_weeks, $alloc->pur_req, $uom_premium, $tradealscheme->free);
									$alloc->manual_pcs = 0;
									$alloc->final_pcs = $alloc->computed_pcs;
									$alloc->prem_cost = $premium['cost'];
									$alloc->computed_cost = $alloc->computed_pcs * $premium['cost'];
									$alloc->deal_multiplier = $uom_premium;
									$alloc->pre_code = $premium['pre_code'];
									$alloc->pre_desc = $premium['pre_desc'];
									$alloc->pre_desc_variant = $premium['pre_desc'].' '.$premium['variant'];
									$alloc->save();
								}
							}
						}else{
							$alloc = new TradedealSchemeAllocation;
							$alloc->tradedeal_scheme_id = $tradealscheme->id;
							$alloc->tradedeal_scheme_sku_id = $tradedeal_scheme_sku_id; 
							$alloc->scheme_code = $deal_id;
							$alloc->scheme_desc = $scheme_desc;
							$alloc->area_code = $customer->area_code;
							$alloc->area = $customer->area_name;
							$alloc->sold_to_code = $customer->customer_code;
							$alloc->sold_to =  $customer->customer_name;
							// $alloc->ship_to_code = $shipto['ship_to_code'];
							// $alloc->plant_code = $shipto['plant_code'];
							// $alloc->ship_to_name = $shipto['ship_to_name'];
							$alloc->sold_to_gsv = $customer->gsv; 
							$alloc->weekly_run_rates = self::weekly_run_rates($tradealscheme, $customer->gsv, $host_skus);
							$alloc->pur_req = self::purchase_requirement($tradealscheme, $customer->gsv, $uom_multiplpier, $host_skus);
							$alloc->computed_pcs = self::computed_pcs($alloc->weekly_run_rates,$tradedeal->alloc_in_weeks, $alloc->pur_req, $uom_multiplpier, $tradealscheme->free);
							$alloc->manual_pcs = 0;
							$alloc->final_pcs = $alloc->computed_pcs;
							$alloc->prem_cost = $premium['cost'];
							$alloc->computed_cost = $alloc->computed_pcs * $premium['cost'];
							$alloc->deal_multiplier = $uom_premium;
							$alloc->pre_code = $premium['pre_code'];
							$alloc->pre_desc = $premium['pre_desc'];
							$alloc->pre_desc_variant = $premium['pre_desc'].' '.$premium['variant'];
							$alloc->save();
						}
					}
				}
			// }
		}
	}

	private static function generateSchemeDesc($tradedeal, $tradealscheme, $host_sku){
		$scheme_desc = '';
		$scheme_uom_abv2;
		if($tradealscheme->tradedeal_uom_id == 1){
			$scheme_uom_abv2 = 'PC';
		}
		if($tradealscheme->tradedeal_uom_id == 2){
			$scheme_uom_abv2 = 'DZ';
		}
		if($tradealscheme->tradedeal_uom_id == 3){
			$scheme_uom_abv2 = 'CS';
		}

		if($tradealscheme->tradedeal_type_id == 1){
			if($tradedeal->non_ulp_premium){
				$scheme_desc = $tradealscheme->buy.'+'.$tradealscheme->free.' '.$scheme_uom_abv2.' '.$host_sku->brand_shortcut. ' '. $host_sku->host_sku_format. ' '.$host_sku->variant.'+'.' '.substr($host_sku->pre_desc, 0, 13);
			}else{
				$scheme_desc = $tradealscheme->buy.'+'.$tradealscheme->free.' '.$scheme_uom_abv2.' '.$host_sku->brand_shortcut. ' '.$host_sku->variant.'+'.$host_sku->pre_brand_shortcut. ' '. $host_sku->pre_sku_format . ' '. $host_sku->pre_variant;
			}
		}
		if($tradealscheme->tradedeal_type_id == 2){
			
		}
		if($tradealscheme->tradedeal_type_id == 3){
			$scheme_desc = $tradealscheme->buy.'+'.$tradealscheme->free.' '. $scheme_uom_abv2. ' ';
			$host_skus = TradedealSchemeSku::getHostSku($tradealscheme);

			$host_brand = [];
			foreach ($host_skus as $host) {
				$host_brand[] = $host->brand_shortcut;
			}

			$x_brands = array_unique($host_brand);
			$variant = '';
			if(count($x_brands) == 1){
				$host_variants = [];
				foreach ($host_skus as $host) {
					$host_variants[] = $host->pre_variant;
				}
				$x_variants = array_unique($host_variants);
				$x_brand = $x_brands[0];
				$variant = implode("/", $x_variants);
				$brand = $x_brand. ' '.substr($variant, 0,9);
			}else{
				if(count($x_brands) > 3){
					$brand = 'MULTIPLEBRAND';
				}else{
					$brand = implode("/", $x_brands);
				}
			}
			
			$scheme_desc .= $brand;

			if($tradedeal->non_ulp_premium){
				$scheme_desc .= '+'. substr($tradealscheme->pre_desc, 0,13);
			}else{
				$premium = TradedealPartSku::find($tradealscheme->pre_id);
				$scheme_desc .= '+'.$premium->pre_brand_shortcut.' '.$premium->pre_sku_format.' '.$premium->pre_variant;
			}

			
		}

		return $scheme_desc;
	}


	public static function manualUpload($records,$activity){
		DB::beginTransaction();
			try {
			foreach ($records as $row) {
				$alloc = TradedealSchemeAllocation::find($row->id);
				if(!empty($alloc)){
					$alloc->manual_pcs = $row->new_allocation;
					$alloc->final_pcs = $row->new_allocation;
					$alloc->computed_cost = $alloc->prem_cost * $row->new_allocation;
					$alloc->update();
				}
			}
			
			DB::commit();
		} catch (\Exception $e) {
			dd($e);
			DB::rollback();
		}
	}

}