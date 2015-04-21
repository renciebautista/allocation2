<?php

class Holiday extends \Eloquent {
	protected $fillable = [];
	public static $rules = array(
        'desc' => 'required',
        'date' => 'required'
    );
}