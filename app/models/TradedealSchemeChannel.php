<?php

class TradedealSchemeChannel extends \Eloquent {
	protected $fillable = [];

	public function channel(){
		return $this->belongsTo('TradedealChannel','tradedeal_channel_id','id');
	}
}