<?php

class MtDtHieracry extends \Eloquent {
	protected $fillable = [];
	public $timestamps = false;
	protected $table = 'mt_dt_hieracry';

	// public static function getDistributors($channels){
	// 	return self::select('areas.area_code', 'areas.area_name', 'customers.customer_code', 'customers.customer_name',
	// 		'ship_tos.ship_to_code', 'ship_tos.plant_code', 'ship_tos.ship_to_name')
	// 		->join('sub_channels', 'sub_channels.coc_03_code', '=', 'mt_dt_hieracry.coc_03_code')
	// 		->join('areas', 'areas.area_code', '=', 'mt_dt_hieracry.area_code')
	// 		->join('customers', 'customers.customer_code', '=', 'mt_dt_hieracry.customer_code')
	// 		->join('ship_tos', 'ship_tos.plant_code', '=', 'mt_dt_hieracry.plant_code')
	// 		->whereIn('mt_dt_hieracry.coc_05_code', $channels)
	// 		->whereNotNull('ship_tos.ship_to_code')
	// 		->groupBy('mt_dt_hieracry.customer_code')
	// 		->groupBy('mt_dt_hieracry.area_code')
	// 		->get();
	// }

	// public static function getAllHierarcy(){
	// 	// return self::->join('sub_channels', 'sub_channels.coc_03_code', '=', 'mt_dt_hieracry.coc_03_code')
	// 	$query = sprintf('select * from mt_dt_hieracry
	// 		left join areas on areas.area_code = mt_dt_hieracry.area_code
	// 		left join groups on groups.group_code = areas.group_code
	// 		left join customers on customers.customer_code = mt_dt_hieracry.customer_code
	// 		left join ship_tos on ship_tos.plant_code = mt_dt_hieracry.plant_code
	// 		left join ship_to_plant_codes on ship_to_plant_codes.distributor_code = mt_dt_hieracry.distributor_code
	// 		left join accounts on (mt_dt_hieracry.account_name = accounts.account_name AND ship_tos.ship_to_code = accounts.ship_to_code)
	// 		left join sub_channels on sub_channels.coc_03_code = mt_dt_hieracry.coc_03_code
	// 		left join channels on channels.channel_code = sub_channels.channel_code
	// 		left join level4 on level4.l4_code = mt_dt_hieracry.coc_04_code
	// 		left join level5 on level5.l5_code = mt_dt_hieracry.coc_05_code');
	// 	return DB::select(DB::raw($query));
	// }

	
}