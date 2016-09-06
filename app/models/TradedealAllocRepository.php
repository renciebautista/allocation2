<?php

class TradedealAllocRepository  {

	public static function insertAlllocation($tradealscheme){
		self::save($tradealscheme);
	}

	// public static function updateAllocation($skus,$scheme){
	// 	SchemeAllocation::where('scheme_id',$scheme->id)->delete();
	// 	AllocationSob::where('scheme_id', $scheme->id)->delete();
	// 	self::save($skus,$scheme);
	// }

	
	private static function save($tradealscheme){
		$tradedeal = Tradedeal::find($tradealscheme->tradedeal_id);
		$activity = Activity::find($tradedeal->activity_id);
		$forced_areas = ForceAllocation::getForcedAreas($tradedeal->activity_id);
		$customers = ActivityCustomer::customers($tradedeal->activity_id);
		$_channels = ActivityChannel2::channels($tradedeal->activity_id);

		$scheme_skus = TradedealSchemeSku::select('tradedeal_scheme_skus.id', 'tradedeal_part_skus.ref_code')
			->join('tradedeal_part_skus', 'tradedeal_scheme_skus.tradedeal_part_sku_id', '=', 'tradedeal_part_skus.id')
			->where('tradedeal_scheme_id', $tradealscheme->id)
			->get();

		$_customers = self::td_customers($tradealscheme);

		// Helper::debug($_customers);

		foreach ($_customers as $customer) {
			if(!empty($customer->shiptos)){
				foreach ($customer->shiptos as $shipto) {
					// Helper::debug($customer->shiptos->ship_to_code);
					$shipto_alloc = new TradedealSchemeAllocation;
					$shipto_alloc->tradedeal_scheme_sku_id = 1;
					$shipto_alloc->area_code = $customer->area_code;
					$shipto_alloc->area = $customer->area_name;
					$shipto_alloc->sold_to_code = $customer->customer_code;
					$shipto_alloc->sold_to = $customer->customer_name;
					$shipto_alloc->ship_to_code = $shipto->ship_to_code;
					$shipto_alloc->plant_code = $shipto->plant_code;
					$shipto_alloc->ship_to_name = $shipto->ship_to_name;
					$shipto_alloc->sold_to_gsv = 0.00;
					$shipto_alloc->save();
				}
			}
			
			
		}
		
		// foreach ($scheme_skus as $row) {
		// 	$skus = [];
		// 	$skus[] = $row->ref_code;
		// 	$_allocation = new AllocationRepository2;
		// 	$allocations = $_allocation->customers($skus, $_channels, $customers,$forced_areas);
		// 	Helper::debug($allocations);
		// 	foreach ($allocations as $customer) {
		// 		if(!empty($customer->shiptos)){
		// 			foreach($customer->shiptos as $shipto){
		// 				// if(in_array($shipto['ship_to_code'], $_td_customers)){
		// 					$customer_alloc = new TradedealSchemeAllocation;
		// 					$customer_alloc->tradedeal_scheme_sku_id = $row->id;
		// 					$customer_alloc->area_code = $customer->area_code;
		// 					$customer_alloc->area = $customer->area_name;
		// 					$customer_alloc->sold_to_code = $customer->customer_code;
		// 					$customer_alloc->sold_to = $customer->customer_name;
		// 					$customer_alloc->ship_to_code = $shipto['ship_to_code'];
		// 					$customer_alloc->plant_code = $shipto['plant_code'];
		// 					$customer_alloc->ship_to_name = $shipto['ship_to_name'];
		// 					$customer_alloc->sold_to_gsv = 0.00;
		// 					$customer_alloc->save();
		// 				// }
		// 			}
		// 		}

		// 	}
		// }
		
	}

	private static function td_customers($tradealscheme){
		$trade_channels = TradedealSchemeChannel::getChannels($tradealscheme);
		
		$l5s = Level5::whereIn('l5_code',$trade_channels)->get();
		
		$l4_codes = [];
		foreach ($l5s as $row) {
			$l4_codes[] = $row->l4_code;
		}



		$l4s = Level4::whereIn('l4_code',$l4_codes)->get();
		$l3_codes = [];
		foreach ($l4s as $row) {
			$l3_codes[] = $row->coc_03_code;
		}

		// Helper::debug($l3_codes);

		$l3s = SubChannel::whereIn('coc_03_code',$l3_codes)->get();
		$channel_codes = [];
		foreach ($l3s as $row) {
			$channel_codes[] = $row->channel_code;
		}

		$accounts = Account::whereIn('channel_code',$channel_codes)
			->where('active',1)
			->get();
		$shipto_codes = [];
		foreach ($accounts as $row) {
			$shipto_codes[] = $row->ship_to_code;
		}

		// return array_unique($shipto_codes);

		$shiptos = ShipTo::whereIn('ship_to_code',$shipto_codes)
			->where('active',1)
			->get();

		// Helper::debug($shipto_codes);

		$customer_codes = [];
		foreach ($shiptos as $row) {
			$customer_codes[] = $row->customer_code;
		}

		$customers = Customer::join('areas', 'areas.area_code', '=', 'customers.area_code')
			->whereIn('customer_code',$customer_codes)
			->where('active',1)
			->orderBy('customer_name')
			->groupBy('customer_code')
			->get();

		foreach ($customers as $customer) {
			$shipto_array = [];
			foreach ($shiptos as $shipto) {
				// Helper::debug($shipto);
				if($shipto->customer_code == $customer->customer_code){
					// Helper::debug($shipto);
					$shipto_array[] = $shipto;
				}
			}
			$customer->shiptos = $shipto_array;
		}

		// Helper::debug($customers);
		return $customers;

	}

}