<?php

class ActivityFis extends \Eloquent {
	protected $table = 'activity_fis';
	protected $fillable = [];

	public static function getList($activity_id){
		return self::where('activity_id', $activity_id)->get();
	}
}