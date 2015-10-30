<?php

class ShipTo extends \Eloquent {
	protected $fillable = ['customer_code', 'ship_to_code', 'ship_to_name', 'dayofweek', 'leadtime', 'split', 'active'];
	public $timestamps = false;

	public static function batchInsert($records){
		$records->each(function($row) {
			if(!is_null($row->customer_code)){
				$attributes = array(
					'customer_code' => $row->customer_code,
					'ship_to_code' => $row->ship_to_code,
					'ship_to_name' => $row->ship_to_name,
					'dayofweek' => $row->day_of_week,
					'leadtime' => $row->leadtime,
					'split' => $row->split,
					'active' => ($row->active == 'Y') ? 1 : 0);
				self::updateOrCreate($attributes, $attributes);
			}
			
		});
	}
}