<?php

class Weekpercentage extends \Eloquent {
	protected $table = 'week_percentages';
	protected $fillable = ['scheme_id', 'weekno', 'share'];

	public $timestamps = false;

	public static function allowSharePerWeek($scheme_id){
		$data = array();
		$records = self::where('scheme_id', $scheme_id)->get();
		foreach ($records as $key => $value) {
			$data[$value->weekno] = $value->share;
		}

		return $data;
	}
}

