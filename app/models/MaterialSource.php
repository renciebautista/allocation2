<?php

class MaterialSource extends \Eloquent {
	protected $fillable = ['source'];
	public $timestamps = false;

	public static function batchInsert($records){
		$records->each(function($row) {
			if(!is_null($row->source)){
				$attributes = array(
					'source' => strtoupper($row->source),
					);
				self::updateOrCreate($attributes, $attributes);
			}
			
		});
	}

	public static function getList($activity_id){
		return self::where('activity_id', $activity_id)->get();
	}
}