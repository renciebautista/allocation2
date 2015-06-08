<?php

class Objective extends \Eloquent {
	protected $fillable = [];

	public function activities()
    {
        return $this->belongsToMany('Activity');
    }

    public static function getLists(){
    	return self::orderBy('id')->lists('objective', 'id');;
    }
}