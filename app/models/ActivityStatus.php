<?php

class ActivityStatus extends \Eloquent {
	protected $fillable = [];

	public static function availableStatus($id = 0){
		return self::orderBy('id')
			->where('id', '>', $id)
			->lists('status', 'id');
	}
}