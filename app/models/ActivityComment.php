<?php

class ActivityComment extends \Eloquent {
	protected $fillable = [];

	public function createdby()
    {
        return $this->belongsTo('User','created_by','id');
    }

    public function status()
    {
        return $this->belongsTo('ActivityStatus','comment_status_id','id');
    }

    public static function getList($activity_id){
    	return self::where('activity_id', $activity_id)->get();;
    }
}