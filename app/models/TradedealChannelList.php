<?php

class TradedealChannelList extends \Eloquent {
	protected $fillable = [];

	public $timestamps = false;
	private static function isSelected($key,$collections){

		if(in_array($key, $collections)){
			return true;	
		}
		return false;
	}

	public static function addChannel($activity, $scheme_id,$collections){
		self::where('tradedeal_scheme_id',$scheme_id)->delete();

		$scheme = TradedealScheme::find($scheme_id);
		$scheme_channels = TradedealSchemeChannel::where('tradedeal_scheme_id',$scheme->id)->get();

		$scheme_selection = [];
		if(!empty($scheme_channels)){
			foreach ($scheme_channels as $sel) {
				$scheme_selection[] = $sel->channel_node;
			}
		}

		$all_selected_nodes = \TradedealSchemeChannel::getallselected($activity);
		$all_nodes = [];
		if(!empty($all_selected_nodes)){
			foreach ($all_selected_nodes as $sel) {
				$all_nodes[] = $sel->channel_node;
			}
		}

		$final_nodes = array_diff($all_nodes,$scheme_selection);


		$selected_customers = array();
		if(count($collections) > 0){
			foreach ($collections as $collection) {
				$selected_customers[] = $collection['channel_node'];
			}
		}
		
		$selected_channels = \ActivityCustomer::getSelectedChannels($activity);
		$channels = \DB::table('sub_channels')
			->join('channels', 'channels.channel_code', '=', 'sub_channels.channel_code')
			->where('trade_deal', 1)
			->whereIn('channels.channel_code', $selected_channels)
			->groupBy('channels.channel_code')
			->orderBy('channel_name')
			->get();

		$channel_selections = array();
		foreach ($channels as $channel) {
			$sub_channels = \DB::table('sub_channels')
				->where('channel_code', $channel->channel_code)
				->where('trade_deal', 1)
				->groupBy('rtm_tag')
				->orderBy('rtm_tag')
				->get();
				
			$channel_children = array();
			foreach ($sub_channels as $sub_channel) {
				$key = $channel->channel_code.".".$sub_channel->rtm_tag;
				if(!in_array($key, $final_nodes)){
					$channel_children[] = array(
						'select' => true,
						'title' => $sub_channel->rtm_tag,
						'isfolder' => true,
						'key' => $key,
						);
				}
			}
			if(count($channel_children) > 0){
				$channel_selections[] = array(
					'title' => $channel->channel_name,
					'isfolder' => true,
					'key' => $channel->channel_code,
					'children' => $channel_children,
					);				
			}
		}

		foreach ($channel_selections as $channel) {
			$ch_selected = self::isSelected($channel['key'],$selected_customers);
			$data[] = array('tradedeal_scheme_id' => $scheme_id,
				'parent_id' =>null,
				'title' => $channel['title'], 
				'isfolder' => $channel['isfolder'], 
				'key' => $channel['key'], 
				'unselectable' => true,
				'selected' => $ch_selected);
			if(count($channel['children'])>0){
				foreach ($channel['children'] as $rtm) {
					// $area_selceted = self::isSelected($group['key'].".".$area['key'],$selected_customers) || $grp_selected;
					$rtms_selected = self::isSelected($rtm['key'],$selected_customers) || $ch_selected;
					$data[] = array('tradedeal_scheme_id' => $scheme_id,
						'parent_id' => $channel['key'],
						'title' => $rtm['title'], 
						'isfolder' => $rtm['isfolder'], 
						'key' => $rtm['key'], 
						'unselectable' => true,
						'selected' =>  $rtms_selected);
				}
			}
		}
		if(count($data) > 0){
			self::insert($data);
		}
	}
}