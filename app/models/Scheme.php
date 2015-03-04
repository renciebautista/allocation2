<?php

class Scheme extends \Eloquent {
	protected $fillable = [];

	public static $rules = array(
		'scheme_name' => 'required',
		'pr' => 'required',
		'srp_p' => 'required',
		'total_alloc' => 'required',
		'deals' => 'required',
		'skus' => 'required',
	);
}