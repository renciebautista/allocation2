<?php

class ActivityChannel extends \Eloquent {
	protected $fillable = [];

	public static function channels($id){
		$channels = array();
		$_channels = self::where('activity_id',$id)
			->join('channels', 'activity_channels.channel_id', '=', 'channels.id')
			->get();
		foreach($_channels as $channel){
			$channels[] = $channel->channel_code;
		}

		return $channels;
	}
}