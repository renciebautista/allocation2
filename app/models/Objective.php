<?php

class Objective extends \Eloquent {
	protected $fillable = [];

	public function activities()
    {
        return $this->belongsToMany('Activity');
    }
}