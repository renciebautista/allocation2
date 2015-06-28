<?php

class MaterialSource extends \Eloquent {
	protected $fillable = [];

	public static function getList($activity_id){
		return self::where('activity_id', $activity_id)->get();
	}
}