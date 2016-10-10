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
			'pre_code', 'pre_desc', 'pre_cost', 'pre_pcs_case')
			->join('tradedeal_part_skus', 'tradedeal_scheme_skus.tradedeal_part_sku_id', '=', 'tradedeal_part_skus.id')
			->where('tradedeal_scheme_id', $tradealscheme->id)
			->orderBy('tradedeal_part_skus.id')
			->get();

		$trade_channels = TradedealSchemeChannel::getChannels($tradealscheme);
		
		$td_customers = Level5::getCustomers($trade_channels);

		$skus = [];

		if($tradealscheme->tradedeal_type_id == 1){
			foreach ($scheme_skus as $row) {
				unset($skus);
				$skus[] = $row;
				self::generate_allocation($_channels, $customers,$forced_areas,$trade_channels, $td_customers, $tradealscheme, $tradedeal, $skus);
			}
		}else if($tradealscheme->tradedeal_type_id == 2){
			foreach ($scheme_skus as $row) {
				$skus[] = $row;
			}
			if(!$tradedeal->non_ulp_premium){
				$collective_premium = TradedealPartSku::findOrFail($tradealscheme->pre_id);
			}else{
				$collective_premium = $tradedeal;
			}
			

			self::generate_allocation($_channels, $customers,$forced_areas,$trade_channels, $td_customers, $tradealscheme, $tradedeal, $skus, $collective_premium);
		}else{
			$lowest_cost = 0;
			$skus = [];
			foreach ($scheme_skus as $row) {
				if($lowest_cost == 0){
			        	$lowest_cost = $row->host_cost;
			        	$skus[0] = $row;
			        }else{
			        	if($lowest_cost > $row->host_cost){
				        	$lowest_cost = $row->host_cost;
				        	$skus[0] = $row;
				        }
			        }
			}

			

			if(!$tradedeal->non_ulp_premium){
				$collective_premium = TradedealPartSku::findOrFail($tradealscheme->pre_id);
			}else{
				$collective_premium = $tradedeal;
			}

			self::generate_allocation($_channels, $customers,$forced_areas,$trade_channels, $td_customers, $tradealscheme, $tradedeal, $skus, $collective_premium);
		}
		
	}

	private static function generate_allocation($_channels, $customers,$forced_areas,$trade_channels, $td_customers, $tradealscheme, $tradedeal, $host_sku, $collective_premium = null){
		$allocationRepo = new AllocationRepository2;
		$skus = [];

		foreach ($host_sku as $row) {
			$skus[] = $row->ref_code;
		}
		
		$gsvsales = $allocationRepo->customers($skus, $_channels, $customers,$forced_areas, true, $trade_channels, $td_customers);

		

		if(!is_null($collective_premium)){
			if(isset($collective_premium->pre_code)){
				$premium['pre_code'] = $collective_premium->pre_code;
				$premium['pre_desc'] = $collective_premium->pre_desc;
				$premium['cost'] = $collective_premium->pre_cost;
			}else{
				$premium['pre_code'] = $collective_premium->non_ulp_premium_code;
				$premium['pre_desc'] = $collective_premium->non_ulp_premium_desc;
				$premium['cost'] = $collective_premium->non_ulp_premium_cost;
			}
			$sku = $host_sku[0];
		}else{
			$sku = $host_sku[0];
			$premium['pre_code'] = $host_sku[0]->pre_code;
			$premium['pre_desc'] = $host_sku[0]->pre_desc;
			$premium['cost'] = $host_sku[0]->pre_cost;
		}
		
		// Helper::debug($sku);

		$uom = $tradealscheme->tradedeal_uom_id;

		foreach ($td_customers as $customer) {
			$sold_to_gsv = 0;
			$computed_deals = 0;
			foreach ($gsvsales as $sale) {
				if(!empty($sale->shiptos)){
					foreach ($sale->shiptos as $shipto) {
						if($customer->plant_code == $shipto['plant_code']){
							$sold_to_gsv += $shipto['gsv'];
						}
					}
				}
				
			}
			
			$cov_multiplier = $tradealscheme->coverage/100;
			$sold_to_gsv = $sold_to_gsv * $cov_multiplier;

			$uom_multiplpier  = 1;
			if($uom == 1){
				$uom_multiplpier  = 1;
	      	}
	        if($uom == 2){
	        	$uom_multiplpier  = 12;
	        }
	        if($uom == 3){
	        	$uom_multiplpier = $host_sku[0]->host_pcs_case;
	        }
	       
			if($tradealscheme->tradedeal_type_id == 1){
				$weekly_run_rates = ( $sold_to_gsv / 52 ) * $sku->host_cost * $sku->ref_pcs_case ;
				$pur_req = $sku->host_cost * $tradealscheme->buy * $uom_multiplpier;
			}else if($tradealscheme->tradedeal_type_id == 2){
				$total_cost = 0;
				$total_pur_req = 0;
				foreach ($host_sku as $row) {
					$total_cost +=  $row->host_cost;
					$total_pur_req += $row->host_cost * $row->qty * $uom_multiplpier;
				}
				$weekly_run_rates = ( $sold_to_gsv / 52 ) * $total_cost * $host_sku[0]->ref_pcs_case ;
				$pur_req = $total_pur_req;
			}else{
				$weekly_run_rates = ( $sold_to_gsv / 52 ) * $sku->host_cost * $sku->ref_pcs_case ;
				$pur_req = $sku->host_cost * $tradealscheme->buy * $uom_multiplpier;
			}

			$computed_pcs = round($weekly_run_rates * $tradedeal->alloc_in_weeks / $pur_req) * $uom_multiplpier * $tradealscheme->free;

			if(!empty($tradealscheme)){
				$shipto_alloc = new TradedealSchemeAllocation;
				$shipto_alloc->tradedeal_scheme_id = $tradealscheme->id;

				if($tradealscheme->tradedeal_type_id > 1){
					$shipto_alloc->tradedeal_scheme_sku_id = 0;
				}else{
					$shipto_alloc->tradedeal_scheme_sku_id = $sku->id;
				}
				

				$shipto_alloc->area_code = $customer->area_code;
				$shipto_alloc->area = $customer->area_name;
				$shipto_alloc->sold_to_code = $customer->customer_code;
				$shipto_alloc->sold_to = $customer->customer_name;
				$shipto_alloc->ship_to_code = $customer->ship_to_code;
				$shipto_alloc->plant_code = $customer->plant_code;
				$shipto_alloc->ship_to_name = $customer->ship_to_name;
				$shipto_alloc->sold_to_gsv = $sold_to_gsv;
				$shipto_alloc->weekly_run_rates = $weekly_run_rates;
				$shipto_alloc->pur_req = $pur_req;
				$shipto_alloc->computed_pcs = $computed_pcs;
				$shipto_alloc->computed_cost = $computed_pcs * $premium['cost'];

				$shipto_alloc->pre_code = $premium['pre_code'];
				$shipto_alloc->pre_desc = $premium['pre_desc'];

				$shipto_alloc->save();
			}
			
			
		}
	}

}