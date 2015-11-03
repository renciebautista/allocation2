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
		$zero = false;

		$start_week = idate('W', strtotime($scheme->sob_start_date));
		$last_week = $start_week + $total_weeks;
		$new_count = $start_week;
		for($i = $start_week; $i < $last_week; $i++){

			if($i > 52){
				if($new_count > 52){
					$new_count = 1;
				}
				$weekno = $new_count;
				$year = $year+ 1;
			}

			if(!$zero){
				if(!empty($wek_multi)){
					$multi = $wek_multi[$new_count]/100;
					$wek_value = ceil($total_alloc * $multi);
					$share = $wek_multi[$new_count];
				}else{
					$wek_value = ceil($total_alloc/$total_weeks);
					$share = (1/$total_weeks) * 100;
				}

				
				$running_value += $wek_value;
				if($running_value > $total_alloc){
					$x = $running_value - $total_alloc;
					$wek_value = $wek_value - $x;
					$zero = true;
				}
			}else{
				$wek_value = 0;
			}
			$weekno = $new_count;
			$year = idate('Y', strtotime($scheme->sob_start_date));
			$new_count++;

			

			
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
					ORDER BY id 
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
			->orderBy('allocation_id')
			->get();
	}

	public static function getByCycle($cycles){
		$query = sprintf("select allocation_sobs.id, activities.activitytype_desc,
			category_tbl.categories,brands_tbl.brands,
			schemes.name,schemes.item_code,schemes.item_desc,
			allocations.group, allocations.area, allocations.sold_to, 
			COALESCE(allocations.ship_to_code,allocations.ship_to_code,allocations.sold_to_code) as ship_to_code,
			allocations.ship_to, allocation_sobs.weekno, allocation_sobs.year,
			allocation_sobs.allocation,
			((schemes.lpat/ 1.12) * allocation_sobs.allocation) as value
			from allocation_sobs
			join allocations on allocations.id = allocation_sobs.allocation_id
			join schemes on allocation_sobs.scheme_id = schemes.id
			join activities on schemes.activity_id = activities.id
			LEFT JOIN (
				SELECT activity_id,
				    GROUP_CONCAT(CONCAT(activity_categories.category_code)) as category_codes,
					GROUP_CONCAT(CONCAT(activity_categories.category_desc)) as categories
					FROM activity_categories 
					GROUP BY activity_id
				)as category_tbl ON activities.id = category_tbl.activity_id
				LEFT JOIN (
				SELECT activity_id,
					GROUP_CONCAT(CONCAT(activity_brands.b_desc)) as brand_codes,
					GROUP_CONCAT(CONCAT(activity_brands.b_desc)) as brands
					FROM activity_brands 
					GROUP BY activity_id
				) as brands_tbl ON activities.id = brands_tbl.activity_id
			where activities.cycle_id in (".implode(",", $cycles).")
			order by allocation_sobs.id");

			return DB::select(DB::raw($query));
	}
}