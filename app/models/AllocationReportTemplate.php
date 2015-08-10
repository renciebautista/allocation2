<?php

class AllocationReportTemplate extends \Eloquent {
	protected $fillable = [];
	public $timestamps = false;
	public static $rules = array(
		'name' => 'required'
	);

	public static function myTemplates(){
		return self::where('created_by', Auth::id())->get();
	}
}