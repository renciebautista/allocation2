<?php

class TradedealSchemeSubType extends \Eloquent {
	protected $fillable = [];
	public $timestamps = false;

	public static function getSchemeSubtypes($scheme){
		return self::where('tradedeal_scheme_id', $scheme->id)
			->orderBy('sub_type_desc')
			->get();
	}

	public static function getActivitySubtypes($activiy){
		return self::join('tradedeal_schemes', 'tradedeal_schemes.id', '=', 'tradedeal_scheme_sub_type.tradedeal_scheme_id')
			->join('tradedeals', 'tradedeals.id', '=', 'tradedeal_schemes.tradedeal_id')
			->where('activity_id', $activity->id)
			->orderBy('sub_type_desc')
			->get();
	}

	
}