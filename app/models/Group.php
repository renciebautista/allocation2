<?php

class Group extends \Eloquent {
	protected $fillable = ['group_code', 'group_name'];
	public $timestamps = false;

	public static function batchInsert($records){
		$records->each(function($row) {
			if(!is_null($row->group_code)){
				$attributes = array(
					'group_code' => $row->group_code,
					'group_name' => $row->group_name);
				self::updateOrCreate($attributes, $attributes);
			}
			
		});
	}

	
	
}