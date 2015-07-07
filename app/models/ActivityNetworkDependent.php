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

	public static function depend_on_task($id){
		$data = array();
		$parents = self::select('activity_type_networks.task_id')
			->where('child_id',$id)
			->join('activity_type_networks', 'activity_network_dependents.parent_id', '=', 'activity_type_networks.id')
			->orderBy('parent_id')
			->get();
		if(!empty($parents)){
			foreach ($parents as $activity) {
				$data[] = $activity->task_id;
			}
		}

		return implode(",", $data);
	}

	public static function depend_on_array($id){
		$data = array();
		$parents = self::select('activity_type_networks.task_id')
			->where('child_id',$id)
			->join('activity_type_networks', 'activity_network_dependents.parent_id', '=', 'activity_type_networks.id')
			->orderBy('parent_id')
			->get();
		if(!empty($parents)){
			foreach ($parents as $activity) {
				$data[] = $activity->task_id;
			}
		}

		return $data;
	}

	public static function depend_on_task_array($id){
		$data = array();
		$parents = self::select('activity_type_networks.task_id','activity_network_dependents.parent_id')
			->where('child_id',$id)
			->join('activity_type_networks', 'activity_network_dependents.parent_id', '=', 'activity_type_networks.id')
			->orderBy('parent_id')
			->get();
		if(!empty($parents)){
			foreach ($parents as $activity) {
				$data[] = (string)$activity->parent_id;
			}
		}

		return $data;
	}
}