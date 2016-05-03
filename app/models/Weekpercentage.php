<?php

class Weekpercentage extends \Eloquent {
	protected $table = 'week_percentages';
	protected $fillable = ['scheme_id', 'weekno', 'share', 'sob_group_id'];

	public $timestamps = false;

	public static function allowSharePerWeek($scheme_id){
		$data = array();
		$records = self::where('scheme_id', $scheme_id)->get();


		// echo '<pre>';
		// print_r($records);
		// echo '</pre>';
		// dd($records);

		foreach ($records as $key => $value) {
			$data[$value->sob_group_id][$value->weekno] = $value->share;
		}

		// echo '<pre>';
		// print_r($data);
		// echo '</pre>';
		// dd($records);
		return $data;
	}
}

