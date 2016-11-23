<?php

class JoborderComment extends \Eloquent {
	protected $fillable = ['joborder_id', 'created_by', 'comment'];

	public function createdBy(){
		return $this->belongsTo('User', 'created_by');
	}

	public function files(){
		return $this->hasMany('CommentFile', 'comment_id');
	}
}