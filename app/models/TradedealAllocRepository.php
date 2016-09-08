<?php

class TradedealAllocRepository  {

	public static function insertAlllocation($tradealscheme){
		self::save($tradealscheme);
	}

	public static function updateAllocation($tradealscheme){
		TradedealSchemeAllocation::where('tradedeal_scheme_id',$tradealscheme->id)->delete();
		// AllocationSob::where('scheme_id', $scheme->id)->delete();
		self::save($tradealscheme);
	}



	
	private static function save($tradealscheme){
		$tradedeal = Tradedeal::find($tradealscheme->tradedeal_id);
		$activity = Activity::find($tradedeal->activity_id);
		$forced_areas = ForceAllocation::getForcedAreas($tradedeal->activity_id);
		$customers = ActivityCustomer::customers($tradedeal->activity_id);
		$_channels = ActivityChannel2::channels($tradedeal->activity_id);

		$scheme_skus = TradedealSchemeSku::select('tradedeal_scheme_skus.id', 'tradedeal_part_skus.ref_code', 'qty', 'host_cost', 'host_pcs_case')
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
		}else{
			foreach ($scheme_skus as $row) {
				$skus[] = $row;
			}
			self::generate_allocation($_channels, $customers,$forced_areas,$trade_channels, $td_customers, $tradealscheme, $tradedeal, $skus);
		}

		
		
		// if($tradealscheme->tradedeal_type_id == 2){
		// 	self::generate_allocation($skus, $_channels, $customers,$forced_areas,$trade_channels, $td_customers, $tradealscheme, $tradedeal, $host_sku);
		// }
		
	}

	private static function generate_allocation($_channels, $customers,$forced_areas,$trade_channels, $td_customers, $tradealscheme, $tradedeal, $host_sku){
		$allocationRepo = new AllocationRepository2;
		$skus = [];
		foreach ($host_sku as $row) {
			$skus[] =  $row->ref_code;
		}
		// Helper::debug($skus);
		$gsvsales = $allocationRepo->customers($skus, $_channels, $customers,$forced_areas, true, $trade_channels, $td_customers);

		if($tradealscheme->tradedeal_type_id == 1){
			$sku = $host_sku[0];
		}else{
			$total_purchase_requirement = 0;
			// $totat_qty = 0;
			foreach ($host_sku as $col_sku) {
				$uom = $tradealscheme->tradedeal_uom_id;
				$col_sku_pr = $col_sku->qty * $col_sku->host_cost;
		      	if($uom == 1){

		      	}
		        if($uom == 2){
		        	$col_sku_pr = $col_sku_pr * 12;
		        }
		        if($uom == 3){
		        	$col_sku_pr = $col_sku_pr * $col_sku->host_pcs_case;
		        }
		        // $totat_qty = $totat_qty + $col_sku->qty;
		        $total_purchase_requirement = $total_purchase_requirement + $col_sku_pr;
			}
			$purchase_requirement = $total_purchase_requirement;
		}

		// Helper::debug($totat_qty);

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

			

			if($tradealscheme->tradedeal_type_id == 1){

				// $sold_to_gsv = $sold_to_gsv * $sku->host_pcs_case;
				
				$purchase_requirement = 0;
				$uom = $tradealscheme->tradedeal_uom_id;
				$purchase_requirement = $sku->qty * $sku->host_cost;
		      	if($uom == 1){

		      	}
		        if($uom == 2){
		        	$purchase_requirement = $purchase_requirement * 12;
		        }
		        if($uom == 3){
		        	$purchase_requirement = $purchase_requirement * $sku->host_pcs_case;
		        }
			}else{

			}
			
			if($purchase_requirement == 0){
				$computed_deals = 0;
			}else{
				$computed_deals = round(($sold_to_gsv * $tradedeal->alloc_in_weeks) / $purchase_requirement);
			}
			

			$shipto_alloc = new TradedealSchemeAllocation;
			$shipto_alloc->tradedeal_scheme_id = $tradealscheme->id;
			if($tradealscheme->tradedeal_type_id == 1){
				$shipto_alloc->tradedeal_scheme_sku_id = $sku->id;
			}else{
				$shipto_alloc->tradedeal_scheme_sku_id = 0;
			}
			
			$shipto_alloc->area_code = $customer->area_code;
			$shipto_alloc->area = $customer->area_name;
			$shipto_alloc->sold_to_code = $customer->customer_code;
			$shipto_alloc->sold_to = $customer->customer_name;
			$shipto_alloc->ship_to_code = $customer->ship_to_code;
			$shipto_alloc->plant_code = $customer->plant_code;
			$shipto_alloc->ship_to_name = $customer->ship_to_name;
			$shipto_alloc->sold_to_gsv = $sold_to_gsv;
			$shipto_alloc->computed_deals = $computed_deals;

			$shipto_alloc->save();
			
		}
	}

}