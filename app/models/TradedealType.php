<?php

class TradedealType extends \Eloquent {
	protected $fillable = [];

	public static function getList(){
		return self::where('active',1)
			->lists('tradedeal_type', 'id');
	}
}