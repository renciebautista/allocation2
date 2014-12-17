<?php

class SubChannel extends \Eloquent {
	protected $fillable = ['coc_03_code', 'channel_code'];
	public $timestamps = false;

	public static function batchInsert($records){
		$records->each(function($row) {
			if(!is_null($row->coc_03_code)){
				$attributes = array(
					'coc_03_code' => $row->coc_03_code,
					'channel_code' => $row->channel_code);
				self::updateOrCreate($attributes, $attributes);
			}
			
		});
	}
}