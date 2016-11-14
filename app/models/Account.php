<?php

class Account extends \Eloquent {
	protected $fillable = ['area_code', 'ship_to_code', 'account_group_code', 'channel_code', 'account_name', 'active'];
	public $timestamps = false;

	public static function batchInsert($records){
		$records->each(function($row) {
			if(!is_null($row->area_code)){
				$attributes = array(
					'area_code' => $row->area_code,
					'ship_to_code' => $row->ship_to_code,
					'account_group_code' => $row->account_group_code,
					'channel_code' => $row->channel_code,
					'account_name' => $row->account_name,
					'active' => ($row->active == 'Y') ? 1 : 0);
				self::updateOrCreate($attributes, $attributes);
			}
			
		});
	}

	public static function getChannelGroup($channel_code){
		return self::select('accounts.account_group_code', 'account_groups.account_group_name')
			->join('account_groups', 'account_groups.account_group_code', '=', 'accounts.account_group_code')
			->where('channel_code',$channel_code)
			->where('account_groups.show_in_summary',1)
			->groupBy('account_group_code')
			->get();
	}

	public static function getAll(){
		return self::join('areas', 'areas.area_code', '=', 'accounts.area_code')
			->get();
	}

	public static function import($records){
		DB::beginTransaction();
			try {
						DB::table('accounts')->truncate();

			$records->each(function($row)  {
				if(!is_null($row->account_name)){
					$account = self::where('area_code',$row->area_code)
						->where('ship_to_code',$row->ship_to_code)
						->where('account_group_code',$row->account_group_code)
						->where('channel_code',$row->channel_code)
						->where('account_name',$row->account_name)
						->first();

					if(empty($account)){

						$account = new Account;
						$account->area_code = $row->area_code;
						$account->ship_to_code = $row->ship_to_code;
						$account->account_group_code = $row->account_group_code;
						$account->channel_code = $row->channel_code;
						$account->account_name = $row->account_name;
						$account->active = $row->active;
						$account->save();
					}
						// else{
					// 	$account->area_code = $row->area_code;
					// 	$account->ship_to_code = $row->ship_to_code;
					// 	$account->account_group_code = $row->account_group_code;
					// 	$account->channel_code = $row->channel_code;
					// 	$account->account_name = $row->account_name;
					// 	$account->active = $row->active;
					// 	$account->update();
					// }
				}
				
			});
			DB::commit();
		} catch (\Exception $e) {
			dd($e);
			DB::rollback();
		}
	}
}