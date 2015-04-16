<?php

class Objective extends \Eloquent {
	protected $fillable = [];

	public function activities()
    {
        return $this->belongsToMany('Activity');
    }

    public static function getLists(){
    	return self::orderBy('objective')->lists('objective', 'id');;
    }
}