<?php

class ActivityNetworkDependent extends \Eloquent {
	protected $fillable = [];
	public $timestamps = false;

	public static function depend_on($id){
		$data = array();
		$parents = self::where('child_id',$id)->get();
		if(!empty($parents)){
			foreach ($parents as $activity) {
				$data[] = $activity->parent_id;
			}
		}

		return implode(",", $data);
	}
}