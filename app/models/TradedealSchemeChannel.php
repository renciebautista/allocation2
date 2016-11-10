<?php

class TradedealSchemeChannel extends \Eloquent {
	protected $fillable = [];
	public $timestamps = false;

	public static function createChannelSelection($scheme, $activity, $inpu_channels){
		self::where('tradedeal_scheme_id', $scheme->id)->delete();
		$channels = explode(",", $inpu_channels);
		if(!empty($channels)){
			$td_channels = array();
			foreach ($channels as $channel){
				$node = explode(".", trim($channel));
				if(isset($node[1])){
					$td_channels[] = array('tradedeal_scheme_id' => $scheme->id, 'channel_node' => trim($channel));
				}else{
					$selected_channels = self::getallselected($activity);
					$sel_ch_arr = [];
					foreach ($selected_channels as $sel_ch) {
						$sel_node = explode(".", $sel_ch->channel_node);
						if(isset($sel_node[1])){
							$sel_ch_arr[] = $sel_node[1];
						}
						
					}
					$l5s = TradedealChannel::getChannels($activity, $node[0]);

					foreach ($l5s as $av_ch) {
						if(!in_array($av_ch->l5_code, $sel_ch_arr)){
							$td_channels[] = array('tradedeal_scheme_id' => $scheme->id, 'channel_node' => $node[0].'.'.$av_ch->l5_code);
						}
					}
				}
			}
			self::insert($td_channels);	
		}
	}



	public static function getallselected($activity){
		return self::join('tradedeal_schemes', 'tradedeal_schemes.id', '=', 'tradedeal_scheme_channels.tradedeal_scheme_id')
			->join('tradedeals', 'tradedeals.id', '=', 'tradedeal_schemes.tradedeal_id')
			->get();
	}

	public static function getSelectedDetails($scheme){
		$tradedeal = Tradedeal::findOrFail($scheme->tradedeal_id);
		$activity = Activity::findOrFail($tradedeal->activity_id);

		$channels = self::getLevel5($scheme);

		return TradedealChannel::where('activity_id', $activity->id)
			->whereIn('l5_code', $channels)
			->orderBy('l5_desc')
			->get();
	}

	public static function getLevel5($scheme){
		$tradedeal = Tradedeal::findOrFail($scheme->tradedeal_id);
		$activity = Activity::findOrFail($tradedeal->activity_id);
		$nodes = self::where('tradedeal_scheme_id', $scheme->id)->get();
		$l5 = [];
		foreach ($nodes as $node) {
			$_selected_node = explode(".", $node->channel_node);

			$ch[] = $_selected_node[0];
			if(!empty($_selected_node[1])){
				$l5[] = $_selected_node[1];
			}else{
				$level5s = TradedealChannel::getChannel($activity, $ch);
				foreach ($level5s as $value) {
					$l5[] = $value->l5_code;
				}
			}
		}
		
		return array_unique($l5);
		
	}
}