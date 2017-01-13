<?php

class JoborderStatus extends \Eloquent {
	protected $table = 'joborder_status';
	protected $fillable = [];

	public static function getLists(){
		return self::orderBy('id')->lists('joborder_status', 'id');
	}

	public static function getUpdateLists(){
		return self::orderBy('id')
			->where('id', '>', 2)
			->lists('joborder_status', 'id');
	}
}