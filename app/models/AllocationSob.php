<?php

class AllocationSob extends \Eloquent {
	protected $fillable = [];

	public $timestamps = false;

	public static function createAllocation($id,$ship_to,$wek_multi = null){
		// dd($wek_multi);

		$scheme = Scheme::find($id);
		$total_alloc = $ship_to->final_alloc;
		$total_weeks = $scheme->weeks;

		$data = array();
		$running_value = 0;
		$running_share = 0;
		$zero = false;

		$start_week = idate('W', strtotime($scheme->sob_start_date));
		$last_week = $start_week + $total_weeks;
		$new_count = $start_week;

		$year = idate('Y', strtotime($scheme->sob_start_date));


		for($i = $start_week; $i < $last_week; $i++){
			$wek_value = 0;
			$share = 0;

			if($i > 52){
				if($new_count > 52){
					$new_count = 1;
					$year++;
				}
				$weekno = $new_count;
				
			}

			// if(!$zero){
				if(!empty($wek_multi)){
					$multi = $wek_multi[$new_count]/100;
					if($multi != 0){
						$wek_value = ceil($total_alloc * $multi);
						$share = $wek_multi[$new_count];
					}

				}else{
					$wek_value = ceil($total_alloc/$total_weeks);
					$share = round((1/$total_weeks) * 100,2);

				}

				// echo $wek_value;
				$running_value += $wek_value;
				if($running_value > $total_alloc){
					// dd($running_value);
					$x = $running_value - $total_alloc;
					$wek_value = $wek_value - $x;
					// $zero = true;
				}

				if($wek_value < 1){
					$wek_value = 0;
				}
			// }else{
			// 	$wek_value = 0;
			// }

			$weekno = $new_count;
			$new_count++;

			$x = $last_week - 1;
			if($i == $x){
				$share = 100 - $running_share;
			}

			$running_share += $share;	

			$data[] = array('scheme_id' => $scheme->id, 
				'allocation_id' => $ship_to->id, 
				'ship_to_code' => $ship_to->ship_to_code,
				'weekno' => $weekno, 
				'year' => $year,
				'share' => $share,
				'allocation' => $wek_value);
		}

		if(count($data) > 0){
			self::insert($data);
		}
		
	}

	public static function getSob($id){
		$rows = self::where('scheme_id', $id)->get();
		if(count($rows)> 0){
			$query1 = sprintf("SELECT 
				GROUP_CONCAT(DISTINCT 
					CONCAT('MAX(IF(weekno = ', weekno, ',allocation,NULL)) AS wk_', weekno)
					ORDER BY year, weekno
			  ) as query_sting
			FROM allocation_sobs 
			WHERE scheme_id = '".$id."';");
			
			$x = DB::select(DB::raw($query1));
			// echo $x[0]->query_sting;

			$query = sprintf("SELECT allocation_id,allocations.group,allocations.area,allocations.ship_to,allocation_sobs.share,".$x[0]->query_sting."
				FROM allocation_sobs
				join allocations on allocations.id = allocation_sobs.allocation_id
				WHERE allocation_sobs.scheme_id = '".$id."'
				GROUP BY allocation_id
				ORDER BY allocation_id");

			return DB::select(DB::raw($query));
		}
		return array();
	}

	public static function getHeader($id){
		return self::select('weekno', 'share')
			->where('scheme_id', $id)
			->groupBy('weekno')
			->orderBy('year')
			->orderBy('weekno')
			->get();
	}

	public static function getByCycle($cycles){
		// $query = sprintf("select allocation_sobs.id, activities.activitytype_desc,
		// 	category_tbl.categories,brands_tbl.brands,
		// 	schemes.name,schemes.item_code,schemes.item_desc,
		// 	allocations.group, allocations.area, allocations.sold_to, 
		// 	COALESCE(allocations.ship_to_code,allocations.ship_to_code,allocations.sold_to_code) as ship_to_code,
		// 	allocations.ship_to, allocation_sobs.weekno, allocation_sobs.year,
		// 	DATE_FORMAT(loading_date,'%%m/%%d/%%Y'), DATE_FORMAT(receipt_date,'%%m/%%d/%%Y'),
		// 	allocation_sobs.allocation,
		// 	((schemes.lpat/ 1.12) * allocation_sobs.allocation) as value
		// 	from allocation_sobs
		// 	join allocations on allocations.id = allocation_sobs.allocation_id
		// 	join schemes on allocation_sobs.scheme_id = schemes.id
		// 	join activities on schemes.activity_id = activities.id
		// 	LEFT JOIN (
		// 		SELECT activity_id,
		// 		    GROUP_CONCAT(CONCAT(activity_categories.category_code)) as category_codes,
		// 			GROUP_CONCAT(CONCAT(activity_categories.category_desc)) as categories
		// 			FROM activity_categories 
		// 			GROUP BY activity_id
		// 		)as category_tbl ON activities.id = category_tbl.activity_id
		// 		LEFT JOIN (
		// 		SELECT activity_id,
		// 			GROUP_CONCAT(CONCAT(activity_brands.b_desc)) as brand_codes,
		// 			GROUP_CONCAT(CONCAT(activity_brands.b_desc)) as brands
		// 			FROM activity_brands 
		// 			GROUP BY activity_id
		// 		) as brands_tbl ON activities.id = brands_tbl.activity_id
		// 	where activities.cycle_id in (".implode(",", $cycles).")
		// 	and activities.disable = '0'
		// 	order by allocation_sobs.id");

			// dd($query);

		$query = sprintf("select allocation_sobs.id, activities.activitytype_desc,
			category_tbl.category_desc,schemes.brand_desc,
			schemes.name,schemes.item_code,schemes.item_desc,
			allocations.group, allocations.area, allocations.sold_to, 
			COALESCE(allocations.ship_to_code,allocations.ship_to_code,allocations.sold_to_code) as ship_to_code,
			allocations.ship_to, allocation_sobs.weekno, allocation_sobs.year,
			DATE_FORMAT(loading_date,'%%m/%%d/%%Y'), DATE_FORMAT(receipt_date,'%%m/%%d/%%Y'),
			allocation_sobs.allocation,
			((schemes.lpat/ 1.12) * allocation_sobs.allocation) as value
			from allocation_sobs
			join allocations on allocations.id = allocation_sobs.allocation_id
			join schemes on allocation_sobs.scheme_id = schemes.id
			join activities on schemes.activity_id = activities.id
			LEFT JOIN (
				SELECT brand_desc,category_desc
				FROM pricelists
				GROUP BY brand_desc
				)as category_tbl ON schemes.brand_desc = category_tbl.brand_desc
			where activities.cycle_id in (".implode(",", $cycles).")
			and activities.disable = '0'
			and activities.status_id = '9'
			order by allocation_sobs.id");

		return DB::select(DB::raw($query));
	}

	public static function getByActivity($id){
		$schemes = Scheme::where('activity_id',$id)->get();
		// return $schemes;
		foreach ($schemes as $key => $scheme) {
			$sobs = AllocationSob::getSob($scheme->id);
			if(count($sobs) > 0){
				$header = AllocationSob::getHeader($scheme->id);
				$sob_header = array();
				if(count($header) >0){
					foreach ($header as $value) {
						$sob_header[$value->weekno] = $value->share;
					}
				}

				$schemes[$key]->sobs = $sobs;
				$schemes[$key]->sob_header = $sob_header;
			}

			
		}

		return $schemes;
	}


	public static function regenerateSob($id){
		$with_sob = self::where('scheme_id', $id)->get();

		if(count($with_sob) > 0){
			
		}

		// // plot sob allocation
		// $customers = Allocation::where('scheme_id',$scheme->id)
		// 	// ->where('group_code','E1397')
		// 	->whereNull('customer_id')
		// 	->whereNull('shipto_id')
		// 	->orderBy('id', 'asc')
		// 	->get();

		// $group_code = array();
		// $area_code = array();
		// $sold_to_code = array();

		// $filters = SobFilter::all();
		// foreach ($filters as $filter) {
		// 	if($filter->group_code != "0"){
		// 		if (!in_array($filter->group_code, $group_code)) {
		// 		    $group_code[] = $filter->group_code;
		// 		}
				
		// 	}

		// 	if($filter->area_code != "0"){
		// 		if (!in_array($filter->area_code, $area_code)) {
		// 		    $area_code[] = $filter->area_code;
		// 		}
				
		// 	}

		// 	if($filter->customer_code != "0"){
		// 		if (!in_array($filter->customer_code, $sold_to_code)) {
		// 		    $sold_to_code[] = $filter->customer_code	;
		// 		}
				
		// 	}
		// }

		// $total_weeks = $scheme->weeks;
		// foreach ($customers as $customer) {
		// 	if((in_array($customer->group_code, $group_code)) || (in_array($customer->area_code, $area_code))|| (in_array($customer->sold_to_code, $sold_to_code))){
		// 		$data = array();
		// 		$_shiptos = Allocation::where('customer_id',$customer->id)
		// 			->whereNull('shipto_id')
		// 			->orderBy('id', 'asc')
		// 			->get();
		// 		if(count($_shiptos) == 0){
		// 			AllocationSob::createAllocation($id,$customer,Input::get('_wek'));
		// 		}else{
		// 			foreach ($_shiptos as $_shipto) {
		// 				AllocationSob::createAllocation($id,$_shipto,Input::get('_wek'));
		// 			}
		// 		}
		// 	}	
		// }
	}
}