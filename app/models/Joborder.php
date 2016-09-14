<?php

class Joborder extends \Eloquent {
	protected $fillable = [];

	public static $rules = array(
		'task' => 'required|integer|min:1',
		'sub_task' => 'required|integer|min:1',
		'start_date' => 'required',
		'end_date' => 'required',
		'details' => 'required'
	);

	public function activity(){
		return $this->belongsTo('Activity');
	}

	public function assignedto(){
		return $this->belongsTo('User', 'created_by');
	}

	public function department(){
		return $this->belongsTo('Department');
	}

	public function status(){
		return $this->belongsTo('JoborderStatus', 'joborder_status_id');
	}

	public function comments(){
		return $this->hasMany('JoborderComment', 'joborder_id');
	}

	public static function getActivityJo($activity){
		return self::where('activity_id', $activity->id)->get();
	}

	public static function departmentJoborder($user){
		return self::where('department_id', $user->department_id)
			// ->where('joborder_status_id', 1)
			->get();
	}
}