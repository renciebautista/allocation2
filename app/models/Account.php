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
}