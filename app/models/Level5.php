<?php

class Level5 extends \Eloquent {
	protected $table = 'level5';
	protected $fillable = ['l4_code', 'l5_code', 'l5_desc', 'rtm_tag', 'trade_deal'];
	public $timestamps = false;

	public static function import($records){
		DB::beginTransaction();
			try {
				DB::table('level5')->truncate();
			$records->each(function($row)  {
				if(!is_null($row->l4_code)){
					$channel = new Level5;
					$channel->l4_code = $row->l4_code;
					$channel->l5_code = $row->l5_code;
					$channel->l5_desc = $row->l5_desc;
					$channel->trade_deal = ($row->trade_deal == 1) ? 1 : 0 ;
					$channel->rtm_tag = $row->rtm_tag;
					$channel->save();
				}				
			});
			DB::commit();
		} catch (\Exception $e) {
			dd($e);
			DB::rollback();
		}
	}

	// public static function getForTradeDeal(){

	// 	return self::select('channels.channel_code', 'channels.channel_name',
	// 		'level5.l5_code', 'level5.l5_desc')
	// 		->join('mt_dt_hieracry', 'mt_dt_hieracry.coc_05_code', '=', 'level5.l5_code')
	// 		->join('sub_channels', 'sub_channels.coc_03_code', '=', 'mt_dt_hieracry.coc_03_code')
	// 		->join('channels', 'channels.channel_code', '=', 'sub_channels.channel_code')
	// 		->where('trade_deal',1)
	// 		->orderBy('channels.channel_name')
	// 		->orderBy('level5.l5_desc')
	// 		->groupBy('mt_dt_hieracry.coc_05_code')
	// 		->get();
	// }

	public static function getL4ByL5($l5_code){
		return self::where('l5_code',$l5_code)
			->where('trade_deal',1)
			->orderBy('l5_desc')
			->get();;
	}

	public static function getChannels(){
		return self::select('channels.channel_code', 'channels.channel_name',
			'sub_channels.coc_03_code', 'sub_channels.l3_desc',
			'level4.l4_code', 'level4.l4_desc',
			'level5.l5_code', 'level5.l5_desc', 'rtm_tag', 'trade_deal')
			->join('level4', 'level4.l4_code', '=', 'level5.l4_code','right')
			->join('sub_channels', 'sub_channels.coc_03_code', '=', 'level4.coc_03_code', 'right')
			->join('channels', 'channels.channel_code', '=', 'sub_channels.channel_code', 'right')
			// ->orderBy('channels.channel_name')
			// ->orderBy('sub_channels.l3_desc')
			->get();
	}

	public static function getCustomers($l5_code){
		return self::select('customers.area_code', 'areas.area_name', 'customers.customer_code',
			'customers.customer_name', 'ship_tos.ship_to_code', 'ship_tos.plant_code', 'ship_tos.ship_to_name')
			->join('level4', 'level4.l4_code', '=', 'level5.l4_code')
			->join('sub_channels', 'sub_channels.coc_03_code', '=', 'level4.coc_03_code')
			->join('accounts', 'accounts.channel_code', '=', 'sub_channels.channel_code')
			->join('ship_tos', 'ship_tos.ship_to_code', '=', 'accounts.ship_to_code')
			->join('customers', 'customers.customer_code', '=', 'ship_tos.customer_code')
			->join('areas', 'areas.area_code', '=', 'customers.area_code')
			->whereIn('level5.l5_code', $l5_code)
			->where('accounts.active',1)
			->where('ship_tos.active',1)
			->groupBy('ship_tos.ship_to_code')
			->orderBy('customers.customer_name')
			->orderBy('ship_tos.ship_to_name')
			->get();
	}


}