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

	private static function searchObject($array, $att_name, $value)
	{
		foreach ($array as $obj) {
			if($obj->$att_name == $value){
				return $obj;
			}
		}
	} 

	public static function timings($id,$start_date){

		$activities = self::where('activitytype_id', $id)->get();
		foreach ($activities as $key => $value) {
			$activities[$key]->depend_on = ActivityNetworkDependent::depend_on_task($value->id);			
		}

		foreach ($activities as $key => $value) {
				$var_end_date ='';
			if($value->task_id == 1){
				$activities[$key]->start_date = date_format(date_create($start_date),'m/d/Y');
				$activities[$key]->end_date =  date('m/d/Y',strtotime($activities[$key]->start_date.' +'.$value->duration.' days'));
				$var_end_date = $activities[$key]->end_date;
			}else{
				
				if(!empty($activities[$key]->depend_on)){
					$depend_on = explode(",", $activities[$key]->depend_on);
					foreach ($depend_on  as $obj) {
						$parent_obj = self::searchObject($activities,'task_id', $obj);
						$old_date = strtotime($var_end_date);
						$new_date = strtotime( $parent_obj->end_date);
						if($new_date > $old_date ){
							$var_end_date = $parent_obj->end_date;
						}
					}
				}
				$activities[$key]->start_date = date_format(date_create($var_end_date),'m/d/Y');
				$activities[$key]->end_date =  date('m/d/Y',strtotime($activities[$key]->start_date.' +'.$value->duration.' days'));
			}
		}
		// echo '<pre>';
		// print_r($activities);
		// echo '</pre>';
		// // print_r(self::searchObject($activities,'task_id', 1));

		return $activities;
	}
}