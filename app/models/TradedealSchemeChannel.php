<?php

class TradedealSchemeChannel extends \Eloquent {
	protected $fillable = [];
	public $timestamps = false;

	public static function createChannelSelection($scheme, $activity, $input_channels){
		self::where('tradedeal_scheme_id', $scheme->id)->delete();
		$channels = explode(",", $input_channels);
		if(!empty($channels)){
			$td_channels = array();
			foreach ($channels as $channel){
				$node = explode(".", trim($channel));
				if(isset($node[1])){
					self::insert(array('tradedeal_scheme_id' => $scheme->id, 'channel_node' => trim($channel)));
				}else{
					// $selected_channels = self::getallselected($activity);
					// $sel_ch_arr = [];
					// foreach ($selected_channels as $sel_ch) {
					// 	$sel_node = explode(".", $sel_ch->channel_node);
					// 	if(isset($sel_node[1])){
					// 		$sel_ch_arr[] = $sel_node[1];
					// 	}
					// }

					$sub_channels = \DB::table('sub_channels')
						->where('channel_code', $node[0])
						->where('trade_deal', 1)
						->groupBy('rtm_tag')
						->orderBy('rtm_tag')
						->get();

					foreach ($sub_channels as $av_ch) {
						// if(!in_array($av_ch->sub_channel_desc, $sel_ch_arr)){
							self::insert(array('tradedeal_scheme_id' => $scheme->id, 'channel_node' => $node[0].'.'.$av_ch->rtm_tag));
						// }
					}
				}
			}
		}


		// create subtypes
		TradedealSchemeSubType::where('tradedeal_scheme_id',  $scheme->id)->delete();

		$selections = self::where('tradedeal_scheme_id', $scheme->id)->get();
		foreach ($selections as $selection) {
			$nodes =  explode(".", $selection->channel_node);
			$sub_chns =  MtDtHieracry::getSubTypes($nodes[0], $nodes[1]);
			$data = [];
			foreach ($sub_chns as $sub_chn) {
				$data[] = ['tradedeal_scheme_id' => $scheme->id, 'sub_type' => $sub_chn->l5_code, 'sub_type_desc' => $sub_chn->rtm_tag];
			}

			if(!empty($data)){
				TradedealSchemeSubType::insert($data);
			}
		}

	}



	public static function getallselected($activity){
		return self::join('tradedeal_schemes', 'tradedeal_schemes.id', '=', 'tradedeal_scheme_channels.tradedeal_scheme_id')
			->join('tradedeals', 'tradedeals.id', '=', 'tradedeal_schemes.tradedeal_id')
			->where('activity_id', $activity->id)
			->get();
	}

	public static function getCustomers($tradealscheme){
		$customer_codes = [];
		
		$selections = self::where('tradedeal_scheme_id', $tradealscheme->id)->get();

		foreach ($selections as $selection) {
			$nodes =  explode(".", $selection->channel_node);
			$sub_chns = \DB::table('sub_channels')->where('channel_code', $nodes[0])->where('rtm_tag', $nodes[1])->get();
			foreach ($sub_chns as $sub_chn) {
				$areas = MtDtHieracry::where('coc_03_code', $sub_chn->coc_03_code)
					->where('coc_04_code', $sub_chn->l4_code)
					->where('coc_05_code', $sub_chn->l5_code)
					->get();
				foreach ($areas as $area) {
					$customer_codes[] = $area->customer_code;
				}
			}

		}

		return array_unique($customer_codes);
	}
}