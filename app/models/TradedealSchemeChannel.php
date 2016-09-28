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

	public static function getSelectedDetails($scheme){
		return self::join('tradedeal_channels', 'tradedeal_channels.id' , '=', 'tradedeal_scheme_channels.tradedeal_channel_id')
			->where('tradedeal_scheme_id', $scheme->id)->get();
	}
	public static function getChannels($scheme){
		$records = self::join('tradedeal_channels', 'tradedeal_channels.id' , '=', 'tradedeal_scheme_channels.tradedeal_channel_id')
			->where('tradedeal_scheme_id', $scheme->id)->get();
		$data = [];
		foreach ($records as $row) {
			$data[] = $row->l5_code;
		}

		return $data;
	}
}