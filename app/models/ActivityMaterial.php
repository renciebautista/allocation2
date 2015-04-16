<?php

class ActivityMaterial extends \Eloquent {
	protected $fillable = [];

	public function source()
    {
        return $this->belongsTo('MaterialSource','source_id','id');
    }

    public static function getList($activity_id){
    	return self::where('activity_id', $activity_id)->get();
    }
}