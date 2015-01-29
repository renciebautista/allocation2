<?php

class Customer extends \Eloquent {
	protected $fillable = ['area_code', 'customer_code', 'customer_name', 'active', 'multiplier'];
	public $timestamps = false;

	public static function batchInsert($records){
		$records->each(function($row) {
			if(!is_null($row->area_code)){
				$attributes = array(
					'area_code' => $row->area_code,
					'customer_code' => $row->customer_code,
					'customer_name' => $row->customer_name,
					'active' => ($row->active == 'Y') ? 1 : 0,
					'multiplier' => $row->multiplier);
				self::updateOrCreate($attributes, $attributes);
			}
			
		});
	}
}