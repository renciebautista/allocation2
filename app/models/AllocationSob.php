<?php

class AllocationSob extends \Eloquent {
	protected $fillable = [];

	public static function createAllocation($ship_to,$scheme){
		$total_alloc = $ship_to->final_alloc;
		$total_weeks = $scheme->weeks;

		$data = array();
		$running_value = 0;
		$zero = false;
		for($i = 0; $i < $total_weeks; $i++){
			$week_no = $i+1;
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
			
			
			$data[] = array('scheme_id' => $scheme->id, 'allocation_id' => $ship_to->id, 'weekno' => $i+1, 'allocation' => $wek_value);

		}
		self::insert($data);
	}

	public static function getSob($id){
		$rows = self::where('scheme_id', $id)->get();
		if(count($rows)> 0){
			$query1 = sprintf("SELECT 
				GROUP_CONCAT(DISTINCT 
					CONCAT('MAX(IF(weekno = ', weekno, ',allocation,NULL)) AS wk_', weekno)
			  ) as query_sting
			 FROM allocation_sobs;");
			
			$x = DB::select(DB::raw($query1));
			// echo $x[0]->query_sting;

			$query = sprintf("SELECT  allocation_id,allocations.ship_to, ".$x[0]->query_sting."
				FROM allocation_sobs
				join allocations on allocations.id = allocation_sobs.allocation_id
				WHERE allocation_sobs.scheme_id = '".$id."'
				GROUP BY allocation_id
				ORDER BY allocation_id");

			return DB::select(DB::raw($query));
		}
		return array();
	}
}