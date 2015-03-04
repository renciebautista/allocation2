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


	public static function getList($id){
		$list = array();
		$data = self::where('activity_id',$id)->get();
		if(!empty($data)){
			foreach ($data as $row) {
				$list[] = $row->channel_id;
			}
		}
		return $list;
	}
}