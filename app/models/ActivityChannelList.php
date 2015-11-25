<?php

class ActivityChannelList extends \Eloquent {
	protected $fillable = [];
	public $timestamps = false;
	protected $table = 'activity_channel_list';

	private static function isSelected($key,$collections){

		if(in_array($key, $collections)){
			return true;	
		}
		return false;
	}

	public static function addChannel($activity_id,$collections){
		self::where('activity_id',$activity_id)->delete();

		$selected_channels = array();
		if(count($collections) > 0){
			foreach ($collections as $collection) {
				$selected_channels[] = $collection['channel_node'];
			}
		}
		
		$channels = Channel::getChannelList();
		$data = array();
		foreach ($channels  as $channel) {
			$chn_selected = self::isSelected($channel['key'],$selected_channels);
			$data[] = array('activity_id' => $activity_id,
				'parent_id' =>null,
				'title' => $channel['title'], 
				'isfolder' => $channel['isfolder'], 
				'key' => $channel['key'], 
				'unselectable' => $channel['unselectable'],
				'selected' => $chn_selected);
			if(count($channel['children'])>0){
				foreach ($channel['children'] as $subgroup) {
					$subgroup_selected = self::isSelected($subgroup['key'],$selected_channels) || $chn_selected;
					$data[] = array('activity_id' => $activity_id,
						'parent_id' => $channel['key'],
						'title' => $subgroup['title'], 
						'isfolder' => isset($subgroup['isfolder']) ? $subgroup['isfolder'] : null, 
						'key' => $subgroup['key'], 
						'unselectable' => $subgroup['unselectable'],
						'selected' =>  $subgroup_selected);
				}
			}
		}
		self::insert($data);
	}

	public static function getSelectecdChannels($id){

		$channels = array();

		$channel_nodes = ActivityChannel2::where('activity_id',$id)->orderBy('id')->get();
		// Helper::print_r($channel_nodes);
		if(!empty($channel_nodes)){
			foreach ($channel_nodes as $channel_node) {
				$_selected_node = explode(".", $channel_node->channel_node);
				$ch = self::where('key',$_selected_node[0])->first();
				$_ch = $ch->title;
				// if(!empty($_selected_node[1])){
				// 	if($_selected_node[1] == "OTHERS"){
				// 		$_ch .= " - OTHERS";
				// 	}else{
				// 		$_grp = AccountGroup::where('account_group_code',$_selected_node[1])->first();
				// 		$_ch .= " - ".$_grp->account_group_name;
				// 	}
					
					
				// }
				$channels[] = $_ch;
			}
		}
		return $channels;


	}
}