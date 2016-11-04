<?php

class Tradedeal extends \Eloquent {
	protected $fillable = [];

	public static function getActivityTradeDeal($activity){
		return self::where('activity_id', $activity->id)->first();
	}

	public function nonUlpPremium(){
		return $this->attributes['non_ulp_premium'];
	}

	public static function total_deals($activity){
		return self::join('tradedeal_schemes', 'tradedeals.id', '=', 'tradedeal_schemes.tradedeal_id')
			->join('tradedeal_scheme_allocations', 'tradedeal_schemes.id', '=', 'tradedeal_scheme_allocations.tradedeal_scheme_id')
			->where('tradedeals.activity_id', $activity->id)
			->sum('tradedeal_scheme_allocations.final_pcs');
	}

	public static function total_premium_cost($activity){
		return self::join('tradedeal_schemes', 'tradedeals.id', '=', 'tradedeal_schemes.tradedeal_id')
			->join('tradedeal_scheme_allocations', 'tradedeal_schemes.id', '=', 'tradedeal_scheme_allocations.tradedeal_scheme_id')
			->where('tradedeals.activity_id', $activity->id)
			->sum('tradedeal_scheme_allocations.computed_cost');
	}
}