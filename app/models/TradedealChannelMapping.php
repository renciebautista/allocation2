<?php

class TradedealChannelMapping extends \Eloquent {
	// protected $fillable = ['coc_03_code', 'coc_04_code', 'coc_05_code', 'sub_channel_desc', 'active'];
	// public $timestamps = false;

	// public static function getChannels(){
	// 	return self::join('sub_channels', 'sub_channels.coc_03_code', '=', 'tradedeal_channel_mappings.coc_03_code')
	// 		->join('channels', 'channels.channel_code', '=', 'sub_channels.channel_code')
	// 		->where('active', 1)->get();
	// }
}