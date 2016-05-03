<?php

class SobGroup extends \Eloquent {
	protected $fillable = ['sobgroup'];
	public $timestamps = false;
	public static $rules = array(
        'sobgroup' => 'required|unique:sob_groups'
    );
}