<?php

class Channel extends \Eloquent {
	protected $fillable = ['channel_code', 'channel_name'];
	public $timestamps = false;

	public static function batchInsert($records){
		$records->each(function($row) {
			if(!is_null($row->channel_code)){
				$attributes = array(
					'channel_code' => $row->channel_code,
					'channel_name' => $row->channel_name);
				self::updateOrCreate($attributes, $attributes);
			}
			
		});
	}

	public static function getList(){
		return self::orderBy('channel_name')->lists('channel_name', 'id');
	}
}