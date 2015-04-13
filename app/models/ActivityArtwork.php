<?php

class ActivityArtwork extends \Eloquent {
	protected $fillable = [];

	public static function getArtworks($id){
		return self::where('activity_id', $id)->get();
	}
}