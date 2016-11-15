<?php

class TradedealSchemeSubType extends \Eloquent {
	protected $fillable = [];
	public $timestamps = false;

	public static function getSchemeSubtypes($scheme){
		return self::where('tradedeal_scheme_id', $scheme->id)->get();
	}
}