<?php

class TradedealSchemeAllocation extends \Eloquent {
	protected $fillable = [];



	public static function getShiptoBy($activity){
		$td_premiums = TradedealSchemeFreeItem::getPremiumsBy($activity);

		$shiptos = self::select('area', 'plant_code', 'ship_to_name', DB::raw('tradedeal_schemes.name as scheme_name'), 'pcs_deal', DB::raw('null as alloc'))
			->join('tradedeal_schemes', 'tradedeal_schemes.id', '=', 'tradedeal_scheme_allocations.tradedeal_scheme_id')
			->join('tradedeals', 'tradedeals.id', '=', 'tradedeal_schemes.tradedeal_id')
			->where('activity_id', $activity->id)
			->orderBy('area')
			->orderBy('ship_to_name')
			->orderBy('tradedeal_schemes.tradedeal_type_id')
			->orderBy('tradedeal_schemes.tradedeal_uom_id')
			->groupBy('scheme_name', 'plant_code')
			->get();

		// $allocations = self::join('tradedeal_schemes', 'tradedeal_schemes.id', '=', 'tradedeal_scheme_allocations.tradedeal_scheme_id')
		// 	->join('tradedeals', 'tradedeals.id', '=', 'tradedeal_schemes.tradedeal_id')
		// 	->join('tradedeal_scheme_skus', 'tradedeal_scheme_skus.id', '=', 'tradedeal_schemes.tradedeal_scheme_sku_id')
		// 	->where('activity_id', $activity->id)
		// 	->get();

		// foreach ($shiptos as $key => $shipto) {
		// 	foreach ($allocations as $alloc) {
		// 		if($shipto->plant_code == $alloc->plant_code){
		// 			$data[$alloc->tradedeal_scheme_sku_id] = $alloc->computed_pcs;
		// 			$shipto->alloc = $data;
		// 		}
		// 	}
		// }
		
		// Helper::debug($shiptos);
		return $shiptos;

	}

	
}