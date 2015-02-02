<?php

class Cycle extends \Eloquent {
	protected $fillable = [];

	public $timestamps = false;

	public static $rules = array(
		'cycle_name' => 'required',
		'month' => 'required|integer|min:1',
		'vetting_deadline' => 'required|date',
		'replyback_deadline' => 'required|date',
		'submission_deadline' => 'required|date',
		'release_date' => 'required|date',
		'implemintation_date' => 'required|date',
	);

	public static function search($filter){
		return DB::table('cycles')
			->select('cycles.*','months.month')
			->join('months', 'months.id', '=', 'cycles.month_id')
			->where('cycles.cycle_name', 'LIKE' ,"%$filter%")
			->get();
	}
}