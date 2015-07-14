<?php

class ActivityTiming extends \Eloquent {
	protected $fillable = [];
	public $timestamps = false;

	public static function getTimings($id,$preview = false){
		return self::select(DB::raw('task_id,milestone,task,responsible,duration,depend_on,DATE_FORMAT(final_start_date, "%m/%d/%Y") AS final_start_date,DATE_FORMAT(final_end_date, "%m/%d/%Y") AS final_end_date'))
			->where(function($query) use ($preview){
				if($preview){
					$query->where('show', 1);
				}
			})
			->where('activity_id', $id)
			->orderBy('start_date')
			->orderBy('end_date')
			->get();;
	}
	

}