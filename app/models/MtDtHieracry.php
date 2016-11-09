<?php

class MtDtHieracry extends \Eloquent {
	protected $fillable = [];
	public $timestamps = false;
	protected $table = 'mt_dt_hieracry';

	public static function getDistributors($channels){
		return self::select('areas.area_code', 'areas.area_name', 'customers.customer_code', 'customers.customer_name',
			'ship_tos.ship_to_code', 'ship_tos.plant_code', 'ship_tos.ship_to_name')
			->join('sub_channels', 'sub_channels.coc_03_code', '=', 'mt_dt_hieracry.coc_03_code')
			->join('areas', 'areas.area_code', '=', 'mt_dt_hieracry.area_code')
			->join('customers', 'customers.customer_code', '=', 'mt_dt_hieracry.customer_code')
			->join('ship_tos', 'ship_tos.plant_code', '=', 'mt_dt_hieracry.plant_code')
			->whereIn('mt_dt_hieracry.coc_05_code', $channels)
			->whereNotNull('ship_tos.ship_to_code')
			->groupBy('mt_dt_hieracry.customer_code')
			->groupBy('mt_dt_hieracry.area_code')
			->get();
	}

	
}