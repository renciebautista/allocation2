<?php

class ActivityMaterial extends \Eloquent {
	protected $fillable = [];

	 public function source()
    {
        return $this->belongsTo('MaterialSource','source_id','id');
    }
}