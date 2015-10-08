<?php

class AllocationSob extends \Eloquent {
	protected $fillable = [];

	public static function getSob($id){
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
			GROUP BY allocation_id
			ORDER BY allocation_id");

		return DB::select(DB::raw($query));
	}
}