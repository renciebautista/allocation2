<?php

class Area extends \Eloquent {
	protected $fillable = ['group_code', 'area_code', 'area_name'];
	public $timestamps = false;

	public static function batchInsert($records){
		$records->each(function($row) {
			if(!is_null($row->group_code)){
				$attributes = array(
					'group_code' => $row->group_code,
					'area_code' => $row->area_code,
					'area_name' => $row->area_name);
				self::updateOrCreate($attributes, $attributes);
			}
			
		});
	}

	public static function getAreaWithGroup(){
		return self::join('groups','groups.group_code','=','areas.group_code')->get();
	}
	
}