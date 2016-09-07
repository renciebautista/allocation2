<?php

class JoborderStatus extends \Eloquent {
	protected $table = 'joborder_status';
	protected $fillable = [];

	public static function getLists(){
		return self::orderBy('id')->lists('joborder_status', 'id');
	}
}