<?php

class TradedealChannel extends \Eloquent {
	protected $fillable = [];

	public static function getChannel($activity, $channels){
		return self::where('activity_id', $activity->id)
			->whereIn('channel_code', $channels)
			->get();
	}

	public static function getChannels($activity, $channel_code){
		return self::where('activity_id', $activity->id)
			->where('channel_code', $channel_code)
			->get();
	}


}