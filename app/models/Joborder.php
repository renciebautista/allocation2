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

	public function department(){
		return $this->belongsTo('Department');
	}

	public static function getActivityJo($activity){
		return self::where('activity_id', $activity->id)->get();
	}
}