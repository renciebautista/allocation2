<?php

class ForceAllocation extends \Eloquent {
	protected $fillable = [];
	public $timestamps = false;
	
	public static function getlist($id){
		return self::select('force_allocations.*', 'areas.area_name')
			->where('activity_id',$id)
			->join('areas', 'force_allocations.area_code', '=', 'areas.area_code')
			->get();
		;
	}

	public static function getAreas($id){
		$list = array();
		$data = self::where('activity_id',$id)->get();
		if(!empty($data)){
			foreach ($data as $row) {
				$list[] = $row->area_code;
			}
		}
		return $list;
	}
}