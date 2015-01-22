<?php

class Scheme extends \Eloquent {
	protected $fillable = [];

	public static $rules = array(
		'name' => 'required',
		'quantity' => 'required|integer|min:1'
	);
}