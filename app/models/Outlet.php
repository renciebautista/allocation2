<?php

class Outlet extends \Eloquent {
	protected $fillable = ['area_code', 'ship_to_code', 'account_name', 'customer_code', 'outlet_code'];
	public $timestamps = false;

	public static function batchInsert($records){
		$records->each(function($row) {
			if(!is_null($row->area_code)){
				$attributes = array(
					'area_code' => $row->area_code,
					'ship_to_code' => $row->ship_to_code,
					'account_name' => $row->account_name,
					'customer_code' => $row->customer_code,
					'outlet_code' => $row->outlet_code);
				self::updateOrCreate($attributes, $attributes);
			}
			
		});
	}
}