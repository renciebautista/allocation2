<?php

class ActivityTiming extends \Eloquent {
	protected $fillable = [];
	public $timestamps = false;

	public static function getTimings($id){
		return self::select(DB::raw('task_id,milestone,task,responsible,duration,depend_on,DATE_FORMAT(start_date, "%m/%d/%Y") AS start_date,DATE_FORMAT(end_date, "%m/%d/%Y") AS end_date'))
					->where('activity_id', $id)
					->get();;
	}
}