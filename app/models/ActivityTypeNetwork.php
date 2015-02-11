<?php
use Rencie\Cpm\CpmActivity;
use Rencie\Cpm\Cpm;
class ActivityTypeNetwork extends \Eloquent {
	protected $fillable = [];
	public $timestamps = false;

	public static function activities($id){
		$activities = array();
		$_activities = self::where('activitytype_id', $id)->get();
		if(count($_activities) > 0){
			foreach ($_activities as $activity) {
				$dependents = ActivityNetworkDependent::depend_on($activity->id);
				if(!empty($dependents)){
					$pre = explode(",", $dependents);
				}
				
				$_activity = new  CpmActivity;;
				$_activity->id = $activity->id;
				$_activity->description = $activity->task;
				$_activity->duration = $activity->duration;
				if(!empty($pre)){
					$_activity->predecessors = $pre;
				}
				
				$activities[] = $_activity;
			}
			
		}

		// echo '<pre>';
		// print_r($activities);
		// echo '</pre>';
		return $activities;
	}
}