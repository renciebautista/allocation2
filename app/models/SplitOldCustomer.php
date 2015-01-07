<?php

class SplitOldCustomer extends \Eloquent {
	protected $fillable = ['inactive_customer_code', 'active_customer_code', 'split'];
	public $timestamps = false;

	public static function batchInsert($records){
		$records->each(function($row) {
			if(!is_null($row->inactive_customer_code)){
				$attributes = array(
					'inactive_customer_code' => $row->inactive_customer_code,
					'active_customer_code' => $row->active_customer_code,
					'split' => $row->split);
				self::insert($attributes, $attributes);
			}
		});
	}
}