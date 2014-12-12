<?php

class AccountGroup extends \Eloquent {
	protected $fillable = ['account_group_code', 'account_group_name'];
	public $timestamps = false;

	public static function batchInsert($records){
		$records->each(function($row) {
			if(!is_null($row->account_group_code)){
				$attributes = array(
					'account_group_code' => $row->account_group_code,
					'account_group_name' => $row->account_group_name);
				self::updateOrCreate($attributes, $attributes);
			}
			
		});
	}
}