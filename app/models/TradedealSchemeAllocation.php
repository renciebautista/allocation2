<?php

class TradedealSchemeAllocation extends \Eloquent {
	protected $fillable = [];

	public static function exportAlloc($tradedeal){
		$query = sprintf("select area, sold_to_code, sold_to, plant_code,ship_to_name, scheme_code, tradedeal_schemes.name as scheme_description,
			tradedeal_schemes.tradedeal_uom_id, 
			COALESCE(tradedeal_part_skus.pre_pcs_case,tradedeal_schemes.pre_pcs_case) as pcs_case, pre_desc_variant, computed_pcs
			from tradedeal_scheme_allocations 
			right join tradedeal_schemes on tradedeal_schemes.id = tradedeal_scheme_allocations.tradedeal_scheme_id
			left join tradedeal_scheme_skus on tradedeal_scheme_skus.id = tradedeal_scheme_allocations.tradedeal_scheme_sku_id
			left join tradedeal_part_skus on tradedeal_part_skus.id = tradedeal_scheme_skus.tradedeal_part_sku_id
			where tradedeal_schemes.tradedeal_id = '%d' and  computed_pcs > 0 order by area, sold_to, ship_to_name, scheme_description",$tradedeal->id);

		return DB::select(DB::raw($query));
	}

	public static function getSchemeCode($tradedealscheme, $host_sku){
		return self::where('tradedeal_scheme_id', $tradedealscheme->id)
			->where('tradedeal_scheme_sku_id', $host_sku->id)
			->first();
	}

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

	public static function getAllocation($scheme,$host_sku){
		return self::where('tradedeal_scheme_id', $scheme->id)
		->where('tradedeal_scheme_sku_id', $host_sku->id)
		->get();
	}

	public static function getCollectiveAllocation($scheme){
		return self::where('tradedeal_scheme_id', $scheme->id)
		->get();
	}

	public static function getTotalDeals($scheme,$host_sku){
		return self::where('tradedeal_scheme_id', $scheme->id)
			->where('tradedeal_scheme_sku_id', $host_sku->id)
			->sum('computed_pcs');
	}


	public static function getAll($activity){
		return self::select('tradedeal_schemes.id as scheme_id', 'scheme_code', 'tradedeal_schemes.name', 'tradedeal_types.id as tradedeal_type_id', 'tradedeal_types.tradedeal_type',
			'area_code', 'area', 'sold_to_code', 'sold_to', 'ship_to_code', 'plant_code', 'ship_to_name', 'sold_to_gsv',  'weekly_run_rates', 'tradedeals.alloc_in_weeks','tradedeal_scheme_allocations.pur_req', 'computed_pcs',
			'computed_cost', 'pre_desc_variant','tradedeal_schemes.buy', 'tradedeal_schemes.free')
			->join('tradedeal_schemes', 'tradedeal_schemes.id', '=', 'tradedeal_scheme_allocations.tradedeal_scheme_id')
			->join('tradedeal_types', 'tradedeal_types.id', '=', 'tradedeal_schemes.tradedeal_type_id')
			->join('tradedeals', 'tradedeals.id', '=', 'tradedeal_schemes.tradedeal_id')
			->where('tradedeals.activity_id', $activity->id)
			->get();
	}
	
}