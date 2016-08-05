<?php

class TradedealSchemeChannel extends \Eloquent {
	protected $fillable = [];

	public function channel(){
		return $this->belongsTo('TradedealChannel','tradedeal_channel_id','id');
	}

	public static function getSelected($scheme){
		$records = self::where('tradedeal_scheme_id', $scheme->id)->get();
		$data = [];
		foreach ($records as $row) {
			$data[] = $row->tradedeal_channel_id;
		}

		return $data;
	}
}