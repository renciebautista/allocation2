<?php

class ActivityRole extends \Eloquent {
	protected $fillable = [];
	public $timestamps = false;

	public static function getList($activity_id){
		return self::where('activity_id', $activity_id)->get();;
	}

	public static function getListData($activity_id){

		return self::select('owner','point','timing')
			->where('activity_id', $activity_id)
			->orderBy('id')
			->get();;
	}
}