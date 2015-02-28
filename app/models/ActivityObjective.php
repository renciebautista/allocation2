<?php

class ActivityObjective extends \Eloquent {
	protected $fillable = [];

	public static function getList($id){
		$list = array();
		$data = self::where('activity_id',$id)->get();
		if(!empty($data)){
			foreach ($data as $row) {
				$list[] = $row->objective_id;
			}
		}
		return $list;
	}
}