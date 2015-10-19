<?php

class AllocationSob extends \Eloquent {
	protected $fillable = [];

	public static function createAllocation($id,$ship_to){
		$scheme = Scheme::find($id);
		$total_alloc = $ship_to->final_alloc;
		$total_weeks = $scheme->weeks;

		$data = array();
		$running_value = 0;
		$zero = false;

		$start_week = idate('W', strtotime($scheme->sob_start_date));
		$last_week = $start_week + $total_weeks;
		$new_count = 1;
		for($i = $start_week; $i < $last_week; $i++){
			if(!$zero){
				$wek_value = ceil($total_alloc/$total_weeks);
				$running_value += $wek_value;
				if($running_value > $total_alloc){
					$x = $running_value - $total_alloc;
					$wek_value = $wek_value - $x;
					$zero = true;
				}
			}else{
				$wek_value = 0;
			}
			$weekno = $i;
			if($i > 52){
				$weekno = $new_count;
				$new_count++;
			}
			
			
			$data[] = array('scheme_id' => $scheme->id, 
				'allocation_id' => $ship_to->id, 
				'weekno' => $weekno, 
				'share' => (1/$total_weeks) * 100,
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

	public static function getByCycle($cysles){
		
	}
}