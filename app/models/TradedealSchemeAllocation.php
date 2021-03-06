<?php

class TradedealSchemeAllocation extends \Eloquent {
	protected $fillable = [];

	public static function getSummary($tradedeal){

		$area_query = sprintf("select area, area_code
			from tradedeal_scheme_allocations 
			right join tradedeal_schemes on tradedeal_schemes.id = tradedeal_scheme_allocations.tradedeal_scheme_id
			where tradedeal_schemes.tradedeal_id = '%d' 
			group by area_code
			order by area",$tradedeal->id);

		$areas =  DB::select(DB::raw($area_query));


		$dist_query = sprintf("select area, area_code, sold_to_code, sold_to
			from tradedeal_scheme_allocations 
			right join tradedeal_schemes on tradedeal_schemes.id = tradedeal_scheme_allocations.tradedeal_scheme_id
			where tradedeal_schemes.tradedeal_id = '%d'
			group by area_code,  sold_to_code
			order by area, sold_to",$tradedeal->id);

		$distributors =  DB::select(DB::raw($dist_query));


		$ship_to_query = sprintf("select area, area_code, sold_to_code, sold_to, plant_code,ship_to_name
			from tradedeal_scheme_allocations 
			right join tradedeal_schemes on tradedeal_schemes.id = tradedeal_scheme_allocations.tradedeal_scheme_id
			where tradedeal_schemes.tradedeal_id = '%d'
			group by area_code,  sold_to_code, plant_code
			order by area, sold_to, ship_to_name",$tradedeal->id);

		$shiptos =  DB::select(DB::raw($ship_to_query));

		// $schemequery = sprintf("select area, area_code, sold_to_code, sold_to, plant_code,ship_to_name, scheme_code, tradedeal_schemes.name as scheme_description
		// 	from tradedeal_scheme_allocations 
		// 	right join tradedeal_schemes on tradedeal_schemes.id = tradedeal_scheme_allocations.tradedeal_scheme_id
		// 	where tradedeal_schemes.tradedeal_id = '%d'
		// 	group by area_code,  sold_to_code, plant_code, scheme_code
		// 	order by area, sold_to, ship_to_name, scheme_description",$tradedeal->id);

		// $schemes =  DB::select(DB::raw($schemequery));


		$query = sprintf("select area, area_code, sold_to_code, sold_to, plant_code,ship_to_name, scheme_code, tradedeal_schemes.name as scheme_description,
			tradedeal_schemes.tradedeal_uom_id, tradedeal_scheme_allocations.scheme_desc,tradedeal_types.tradedeal_type,
			COALESCE(tradedeal_part_skus.pre_pcs_case,tradedeal_schemes.pre_pcs_case) as pcs_case, pre_desc_variant, computed_pcs, final_pcs,
			activities.eimplementation_date, activities.end_date,tradedeals.non_ulp_premium, tradedeal_scheme_allocations.tradedeal_scheme_sku_id,
			tradedeal_scheme_allocations.tradedeal_scheme_id, tradedeal_scheme_allocations.pre_code, tradedeal_scheme_allocations.pre_desc,
			tradedeal_scheme_allocations.computed_cost
			from tradedeal_scheme_allocations 
			right join tradedeal_schemes on tradedeal_schemes.id = tradedeal_scheme_allocations.tradedeal_scheme_id
			right join tradedeal_types on tradedeal_types.id = tradedeal_schemes.tradedeal_type_id
			left join tradedeal_scheme_skus on tradedeal_scheme_skus.id = tradedeal_scheme_allocations.tradedeal_scheme_sku_id
			left join tradedeal_part_skus on tradedeal_part_skus.id = tradedeal_scheme_skus.tradedeal_part_sku_id
			right join tradedeals on tradedeals.id = tradedeal_schemes.tradedeal_id
			right join activities on activities.id = tradedeals.activity_id
			where tradedeal_schemes.tradedeal_id = '%d' 
			order by area, sold_to, ship_to_name, tradedeal_scheme_id, scheme_code",$tradedeal->id);

		$records =  DB::select(DB::raw($query));

		

		$data = [];
		foreach ($areas as $row) {
			$area = new stdClass();
			$area->area = $row->area;
			$area->area_code = $row->area_code;
			$area_total = [];
			foreach ($distributors as $dist) {
				if($dist->area_code == $row->area_code){
					$dist_obj = new stdClass();
					$dist_obj->sold_to_code = $dist->sold_to_code;
					$dist_obj->sold_to = $dist->sold_to;
					$dist_to_total = [];
					foreach ($shiptos as $shipto) {
						if(($row->area_code == $shipto->area_code) && ($dist->sold_to_code == $shipto->sold_to_code)){
							$shipto_obj = new stdClass();
							$shipto_obj->plant_code = $shipto->plant_code;
							$shipto_obj->ship_to_name = $shipto->ship_to_name;
							$ship_to_total = [];
							foreach ($records as $scheme) {
								if(($row->area_code == $scheme->area_code) 
									&& ($dist->sold_to_code == $scheme->sold_to_code)
									&& ($shipto->plant_code == $scheme->plant_code)
									){
									$scheme_obj = new stdClass();
									$scheme_obj->scheme_code = $scheme->scheme_code;
									$scheme_obj->scheme_description = $scheme->scheme_description;
									$scheme_obj->premiums[$scheme->pre_desc_variant] = $scheme->final_pcs;
									if(!isset($ship_to_total[$scheme->pre_desc_variant])){
										$ship_to_total[$scheme->pre_desc_variant] = 0;
									}
									$ship_to_total[$scheme->pre_desc_variant] += $scheme->final_pcs;
									
									$shipto_obj->schemes[] = $scheme_obj;
								}			
							}

							foreach ($ship_to_total as $key => $value) {
								if(!isset($dist_to_total[$key])){
									$dist_to_total[$key] = 0;
								}
								$dist_to_total[$key] += $value;
							}
							

							$shipto_obj->ship_to_total = $ship_to_total;
							$dist_obj->shipto[] = $shipto_obj;
						}
					}

					foreach ($dist_to_total as $key => $value) {
						if(!isset($area_total[$key])){
							$area_total[$key] = 0;
						}
						$area_total[$key] += $value;
					}

					$dist_obj->dist_total = $dist_to_total;
					$area->dist[] = $dist_obj;
				}
			}
			$area->area_total = $area_total;
			$data[] = $area;
		}

		// Helper::debug($data);	
		return $data;
	}

	public static function exportAlloc($tradedeal){
		$query = sprintf("select area, sold_to_code, sold_to, plant_code,ship_to_name, scheme_code, tradedeal_schemes.name as scheme_description,
			tradedeal_schemes.tradedeal_uom_id, tradedeal_scheme_allocations.scheme_desc,tradedeal_types.tradedeal_type,
			COALESCE(tradedeal_part_skus.pre_pcs_case,tradedeal_schemes.pre_pcs_case) as pcs_case, pre_desc_variant, computed_pcs, final_pcs,
			activities.eimplementation_date, activities.end_date,tradedeals.non_ulp_premium, tradedeal_scheme_allocations.tradedeal_scheme_sku_id,
			tradedeal_scheme_allocations.tradedeal_scheme_id, tradedeal_scheme_allocations.pre_code, tradedeal_scheme_allocations.pre_desc,
			tradedeal_scheme_allocations.computed_cost
			from tradedeal_scheme_allocations 
			right join tradedeal_schemes on tradedeal_schemes.id = tradedeal_scheme_allocations.tradedeal_scheme_id
			right join tradedeal_types on tradedeal_types.id = tradedeal_schemes.tradedeal_type_id
			left join tradedeal_scheme_skus on tradedeal_scheme_skus.id = tradedeal_scheme_allocations.tradedeal_scheme_sku_id
			left join tradedeal_part_skus on tradedeal_part_skus.id = tradedeal_scheme_skus.tradedeal_part_sku_id
			right join tradedeals on tradedeals.id = tradedeal_schemes.tradedeal_id
			right join activities on activities.id = tradedeals.activity_id
			where tradedeal_schemes.tradedeal_id = '%d' 
			and  final_pcs > 0 
			order by area, sold_to, ship_to_name, tradedeal_scheme_id, scheme_code",$tradedeal->id);

		return DB::select(DB::raw($query));
	}

	public static function getSchemeCode($tradedealscheme, $host_sku){
		return self::where('tradedeal_scheme_id', $tradedealscheme->id)
			->where('tradedeal_scheme_sku_id', $host_sku->id)
			->first();
	}

	public static function getCollecttiveSchemeCode($tradedealscheme){
		return self::where('tradedeal_scheme_id', $tradedealscheme->id)
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
					$computed_pcs = $alloc[0]->final_pcs;
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
		->whereNotNull('plant_code')
		->get();
	}

	public static function getCollectiveAllocation($scheme){
		return self::where('tradedeal_scheme_id', $scheme->id)
		->get();
	}

	public static function getTotalDeals($scheme,$host_sku){
		return self::where('tradedeal_scheme_id', $scheme->id)
			->where('tradedeal_scheme_sku_id', $host_sku->id)
			->whereNotNull('plant_code')
			->sum('final_pcs');
	}


	public static function getAll($activity){
		return self::select('tradedeals.activity_id','tradedeal_scheme_allocations.id as alloc_id', 'scheme_code', 'scheme_desc',
		 	'tradedeal_schemes.name', 'pre_desc_variant', 'tradedeal_scheme_allocations.pre_code',
			'area_code', 'area', 'sold_to_code', 'sold_to', 'ship_to_code', 'plant_code', 'tradedeal_scheme_sku_id', 
			'ship_to_name', 'computed_pcs', 'final_pcs', 'final_pcs as total_alloc', 'tradedeal_scheme_allocations.tradedeal_scheme_id', 'tradedeal_scheme_allocations.deal_multiplier')
			->join('tradedeal_schemes', 'tradedeal_schemes.id', '=', 'tradedeal_scheme_allocations.tradedeal_scheme_id')
			->join('tradedeal_types', 'tradedeal_types.id', '=', 'tradedeal_schemes.tradedeal_type_id')
			->join('tradedeals', 'tradedeals.id', '=', 'tradedeal_schemes.tradedeal_id')
			->where('tradedeals.activity_id', $activity->id)
			->orderBy('tradedeal_schemes.name')
			->orderBy('scheme_code')
			->orderBy('scheme_desc')
			->orderBy('area')
			->orderBy('sold_to')
			->get();
	}

	public static function getAllocationSummary($activity){
		return self::select('tradedeals.activity_id','tradedeal_scheme_allocations.id as alloc_id', 'scheme_code', 'scheme_desc',DB::raw('sum(computed_pcs) as sum_computed'),
			DB::raw('sum(final_pcs) as sum_final_pcs'))
			->join('tradedeal_schemes', 'tradedeal_schemes.id', '=', 'tradedeal_scheme_allocations.tradedeal_scheme_id')
			->join('tradedeal_types', 'tradedeal_types.id', '=', 'tradedeal_schemes.tradedeal_type_id')
			->join('tradedeals', 'tradedeals.id', '=', 'tradedeal_schemes.tradedeal_id')
			->where('tradedeals.activity_id', $activity->id)
			->groupBy('scheme_code')
			->get();
	}

	public static function getMTAccounts($scheme){
		return self::where('tradedeal_scheme_id', $scheme->id)
			->whereNull('plant_code')
			->groupBy('sold_to')
			->get();

	}
	
}