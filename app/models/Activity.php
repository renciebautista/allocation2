<?php

class Activity extends \Eloquent {
	protected $fillable = [];

	public static $rules = array(
		'circular_name' => 'required',
		'scope' => 'required|integer|min:1',
		'cycle' => 'required|integer|min:1',
		'activity_type' => 'required|integer|min:1',
		'division' => 'required|integer|min:1',
		'budget_tts' => 'required',
		'budget_pe' => 'required',
		'background' => 'required'
	);
}