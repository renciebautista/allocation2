<?php

class TradedealSchemeFreeItem extends \Eloquent {
	protected $fillable = ['tradedeal_scheme_id', 'tradedeal_scheme_sku_id', 'pre_code', 'pre_desc'];
	public $timestamps = false;

	public static function getPremiumsBy($activity){
		return self::select('tradedeal_part_skus.pre_desc', 'tradedeal_scheme_sku_id')
			->join('tradedeal_schemes', 'tradedeal_schemes.id', '=', 'tradedeal_scheme_free_items.tradedeal_scheme_id')
			->join('tradedeals', 'tradedeals.id', '=', 'tradedeal_schemes.tradedeal_id')
			->join('tradedeal_scheme_skus', 'tradedeal_scheme_skus.id', '=', 'tradedeal_scheme_free_items.tradedeal_scheme_sku_id')
			->join('tradedeal_part_skus', 'tradedeal_part_skus.id', '=', 'tradedeal_scheme_skus.tradedeal_part_sku_id')
			->where('tradedeal_part_skus.activity_id', $activity->id)
			->groupBy('tradedeal_part_skus.pre_code')
			->get();
	}
}