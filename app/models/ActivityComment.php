<?php

class ActivityComment extends \Eloquent {
	protected $fillable = [];

	public function createdby()
    {
        return $this->belongsTo('User','created_by','id');
    }

    public function status()
    {
        return $this->belongsTo('ActivityStatus','comment_status_id','id');
    }
}