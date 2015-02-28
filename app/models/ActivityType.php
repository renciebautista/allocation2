<?php

class ActivityType extends \Eloquent {
	protected $fillable = ['id', 'activity_type', 'uom'];
	public static $rules = array(
        'activity_type' => 'required|between:4,128|unique:activity_types',
        'uom' => 'required'
    );
	public $timestamps = false;

	public static function batchInsert($records){
		$records->each(function($row) {
			if(!is_null($row->activity_type_code)){
				$attributes = array(
					'id' => $row->activity_type_code,
					'activity_type' => $row->activity_type,
					'uom' => strtoupper($row->uom));
				self::insert($attributes);
			}
			
		});
	}

	public static function search($filter){
		return self::where('activity_type', 'LIKE' ,"%$filter%")
			->get();
	}
}