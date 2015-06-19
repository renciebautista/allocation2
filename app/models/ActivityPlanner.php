<?php

class ActivityPlanner extends \Eloquent {
	protected $fillable = [];
	public $timestamps = false;

	public function planner()
    {
        return $this->belongsTo('User','user_id');
    }

    public static function getPlanner($activity_id){
    	return self::where('activity_id',$activity_id)->first();
    }

     public static function getPlannerCount($activity_id){
        return self::where('activity_id',$activity_id)->get();
    }

    public static function myActivity($activity_id){
    	$activity = self::where('activity_id', $activity_id)
    		->where('user_id',Auth::id())
    		->first();
    	if(!empty($activity)){
    		return true;
    	}

    	return false;
    }
}