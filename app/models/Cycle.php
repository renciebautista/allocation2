<?php

class Cycle extends \Eloquent {
	protected $fillable = [];

	public $timestamps = false;

	public static $rules = array(
		'cycle_name' => 'required',
		'month_year' => 'required',
		'vetting_deadline' => 'required|date',
		'replyback_deadline' => 'required|date',
		'submission_deadline' => 'required|date',
		'release_date' => 'required|date',
		'implemintation_date' => 'required|date',
	);

	public static function search($filter){
		return self::where('cycles.cycle_name', 'LIKE' ,"%$filter%")
			->get();
	}

	public static function getLists(){
		return self::orderBy('cycle_name')->lists('cycle_name', 'id');
	}
}