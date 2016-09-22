<?php

class TradedealSchemeAllocation extends \Eloquent {
	protected $fillable = [];



	public static function getShiptoBy($activity){
		$shiptos = self::select('area', 'plant_code', 'ship_to_name', DB::raw('tradedeal_schemes.name as scheme_name'), 
			'pcs_deal', DB::raw('null as alloc'), 'tradedeal_scheme_allocations.tradedeal_scheme_id', 'tradedeal_schemes.tradedeal_uom_id',
			'tradedeal_schemes.pcs_deal')
			->join('tradedeal_schemes', 'tradedeal_schemes.id', '=', 'tradedeal_scheme_allocations.tradedeal_scheme_id')
			->join('tradedeals', 'tradedeals.id', '=', 'tradedeal_schemes.tradedeal_id')
			->where('activity_id', $activity->id)
			->orderBy('area')
			->orderBy('ship_to_name')
			->orderBy('tradedeal_schemes.tradedeal_type_id')
			->orderBy('tradedeal_schemes.tradedeal_uom_id')
			->groupBy('scheme_name', 'plant_code')
			->get();
		

		$td_premiums = self::getPremiumsBy($activity);
		foreach ($shiptos as $key => $value) {
			$allocs = [];
			foreach ($td_premiums as $premium) {
				$computed_pcs = 0;
				$alloc = self::getShipToPremiumAlloc($premium, $value);
				// Helper::debug($alloc);
				if(!empty($alloc)){
					$computed_pcs = $alloc[0]->computed_pcs;
				}

				if($value->tradedeal_uom_id == 1){
					$computed_alloc = number_format($computed_pcs,2);
				}

				if($value->tradedeal_uom_id == 2){
					$computed_alloc =  number_format($computed_pcs / 12,2);
				}

				if($value->tradedeal_uom_id == 3){
					$computed_alloc =  number_format($computed_pcs / $value->pcs_deal,2);
				}
				$allocs[$premium->pre_code] = $computed_alloc;
			}
			$shiptos[$key]->allocs = $allocs;
		}

		// Helper::debug($shiptos);
		return $shiptos;

	}

	public static function getShipToPremiumAlloc($premium, $shipto){
		// return self::where('pre_code', $premium->pre_code)
		// 	->where('plant_code', $shipto->plant_code)
		// 	->where('tradedeal_scheme_id', $shipto->tradedeal_scheme_id)
		// 	->first();


		$query = sprintf("select pre_code, sum(computed_pcs) as computed_pcs
			from tradedeal_scheme_allocations
			where pre_code = '%s'
			and plant_code = '%s'
			and tradedeal_scheme_id = '%s'", $premium->pre_code, $shipto->plant_code, $shipto->tradedeal_scheme_id);

		return DB::select(DB::raw($query));

	}

	public static function getPremiumsBy($activity){
		return self::select('tradedeal_scheme_allocations.pre_code', 'tradedeal_scheme_allocations.pre_desc')
			->join('tradedeal_schemes', 'tradedeal_schemes.id', '=', 'tradedeal_scheme_allocations.tradedeal_scheme_id')
			->join('tradedeals', 'tradedeals.id', '=', 'tradedeal_schemes.tradedeal_id')
			->where('activity_id', $activity->id)
			->orderBy('tradedeal_scheme_allocations.pre_desc')
			->groupBy('tradedeal_scheme_allocations.pre_code')
			->get();

	}

	
}