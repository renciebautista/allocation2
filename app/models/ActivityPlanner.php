<?php

class ActivityPlanner extends \Eloquent {
	protected $fillable = [];
	public $timestamps = false;

	public function planner()
    {
        return $this->belongsTo('User','user_id');
    }
}