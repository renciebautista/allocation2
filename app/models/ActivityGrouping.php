<?php

class ActivityGrouping extends \Eloquent {
	protected $fillable = ['activity_grouping_id', 'activity_grouping'];
	public $timestamps = false;

	public static function batchInsert($records){
		$records->each(function($row) {
			if(!is_null($row->activity_grouping)){
				$attributes = array(
					'activity_grouping_id' => $row->activity_grouping_id,
					'activity_grouping' => $row->activity_grouping
					);
				self::updateOrCreate($attributes, $attributes);
			}
			
		});
	}
}