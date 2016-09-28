<?php

class Tradedeal extends \Eloquent {
	protected $fillable = [];

	public static function getActivityTradeDeal($activity){
		return self::where('activity_id', $activity->id)->first();
	}

	public function nonUlpPremium(){
		return $this->attributes['non_ulp_premium'];
	}
}