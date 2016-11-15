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

	public static function getAllHierarcy(){
		$query = sprintf('select mt_dt_hieracry.id as mt_dt_hieracry_id,
			mt_dt_hieracry.area_code as mt_dt_hieracry_area_code, mt_dt_hieracry.customer_code as mt_dt_hieracry_customer_code, mt_dt_hieracry.distributor_code as mt_dt_hieracry_distributor_code, mt_dt_hieracry.plant_code as mt_dt_hieracry_plant_code, mt_dt_hieracry.account_name as mt_dt_hieracry_account_name, mt_dt_hieracry.coc_03_code as mt_dt_hieracry_coc_03_code, mt_dt_hieracry.coc_04_code as mt_dt_hieracry_coc_04_code, mt_dt_hieracry.coc_05_code as mt_dt_hieracry_coc_05_code,
			areas.id as areas_id, areas.group_code as areas_group_code, areas.area_code as areas_area_code, areas.area_name as areas_area_name,
			groups.id as groups_id, groups.group_code as groups_group_code, groups.group_name as groups_group_name,
			customers.id as customers_id, customers.area_code as customers_area_code, customers.area_code_two as customers_area_code_two, customers.customer_code as customers_customer_code, customers.sob_customer_code as customers_sob_customer_code, customers.customer_name as customers_customer_name, customers.active as customers_active, customers.multiplier as customers_multiplier, customers.from_dt as customers_from_dt, customers.trade_deal as customers_trade_deal,
			ship_tos.id as ship_tos_id, ship_tos.customer_code as ship_tos_customer_code, ship_tos.sold_to_code as ship_tos_sold_to_code, ship_tos.ship_to_code as ship_tos_ship_to_code, ship_tos.plant_code as ship_tos_plant_code, ship_tos.ship_to_name as ship_tos_ship_to_name, ship_tos.split as ship_tos_split, ship_tos.leadtime as ship_tos_leadtime, ship_tos.mon as ship_tos_mon, ship_tos.tue as ship_tos_tue, ship_tos.wed as ship_tos_wed, ship_tos.thu as ship_tos_thu, ship_tos.fri as ship_tos_fri, ship_tos.sat as ship_tos_sat, ship_tos.sun as ship_tos_sun, ship_tos.active as ship_tos_active,
			ship_to_plant_codes.id as ship_to_plant_codes_id, ship_to_plant_codes.group_code as ship_to_plant_codes_group_code, ship_to_plant_codes.group as ship_to_plant_codes_group, ship_to_plant_codes.area_code as ship_to_plant_codes_area_code, ship_to_plant_codes.area as ship_to_plant_codes_area, ship_to_plant_codes.customer_code as ship_to_plant_codes_customer_code, ship_to_plant_codes.customer as ship_to_plant_codes_customer, ship_to_plant_codes.distributor_code as ship_to_plant_codes_distributor_code, ship_to_plant_codes.distributor_name as ship_to_plant_codes_distributor_name, ship_to_plant_codes.plant_code as ship_to_plant_codes_plant_code, ship_to_plant_codes.ship_to_name as ship_to_plant_codes_ship_to_name,
			accounts.id as accounts_id, accounts.area_code as accounts_area_code, accounts.ship_to_code as accounts_ship_to_code, accounts.account_group_code as accounts_account_group_code, accounts.channel_code as accounts_channel_code, accounts.account_name as accounts_account_name, accounts.active as accounts_active,
			sub_channels.id as sub_channels_id, sub_channels.channel_code as sub_channels_channel_code, sub_channels.coc_03_code as sub_channels_coc_03_code, sub_channels.l3_desc as sub_channels_l3_desc, sub_channels.l4_code as sub_channels_l4_code, sub_channels.l4_desc as sub_channels_l4_desc, sub_channels.l5_code as sub_channels_l5_code, sub_channels.l5_desc as sub_channels_l5_desc, sub_channels.rtm_tag as sub_channels_rtm_tag, sub_channels.trade_deal as sub_channels_trade_deal,
			channels.id as channels_id, channels.channel_code as channels_channel_code, channels.channel_name as channels_name
			from mt_dt_hieracry
			left join areas on areas.area_code = mt_dt_hieracry.area_code
			left join groups on groups.group_code = areas.group_code
			left join customers on customers.customer_code = mt_dt_hieracry.customer_code
			left join ship_tos on ship_tos.plant_code = mt_dt_hieracry.plant_code
			left join ship_to_plant_codes on ship_to_plant_codes.distributor_code = mt_dt_hieracry.distributor_code
			left join accounts on (mt_dt_hieracry.account_name = accounts.account_name AND ship_tos.ship_to_code = accounts.ship_to_code)
			left join sub_channels on (mt_dt_hieracry.coc_03_code = sub_channels.coc_03_code AND mt_dt_hieracry.coc_04_code = sub_channels.l4_code AND mt_dt_hieracry.coc_05_code = sub_channels.l5_code)
			left join channels on channels.channel_code = sub_channels.channel_code');
		return DB::select(DB::raw($query));
	}

	public static function getSubTypes($channel_code, $sub_type){
		$query = sprintf("select l5_code, rtm_tag from mt_dt_hieracry
				left join customers on customers.customer_code = mt_dt_hieracry.customer_code
				left join ship_tos on ship_tos.plant_code = mt_dt_hieracry.plant_code
				left join sub_channels on (mt_dt_hieracry.coc_03_code = sub_channels.coc_03_code AND mt_dt_hieracry.coc_04_code = sub_channels.l4_code AND mt_dt_hieracry.coc_05_code = sub_channels.l5_code)
				left join channels on channels.channel_code = sub_channels.channel_code
				where customers.trade_deal = 1
				and sub_channels.channel_code = '%s'
				and sub_channels.rtm_tag = '%s'
				and ship_tos.ship_to_code IS NOT NULL
				group by l5_code, rtm_tag", $channel_code, $sub_type);
		return DB::select(DB::raw($query));
	}
}