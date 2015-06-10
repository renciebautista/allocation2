<?php

class ActivityChannel2 extends \Eloquent {
	protected $table = 'activity_channels_2';
	protected $fillable = [];

	public static function channels($id){
		$channels = array();
		$channel_nodes = self::where('activity_id',$id)->get();
		if(!empty($channel_nodes)){
			foreach ($channel_nodes as $channel_node) {
				$channels[] = $channel_node->channel_node;
			}
		}
		return $channels;
	}

	public static function getSelectecdChannels($id){
		$channels = array();
		$channel_nodes = self::where('activity_id',$id)->get();
		if(!empty($channel_nodes)){
			foreach ($channel_nodes as $channel_node) {
				$_selected_node = explode(".", $channel_node->channel_node);
				$ch = Channel::where('channel_code',$_selected_node[0])->first();
				$_ch = $ch->channel_name;
				if(!empty($_selected_node[1])){
					if($_selected_node[1] == "OTHERS"){
						$_ch .= " - OTHERS";
					}else{
						$_grp = AccountGroup::where('account_group_code',$_selected_node[1])->first();
						$_ch .= " - ".$_grp->account_group_name;
					}
					
					
				}
				$channels[] = $_ch;
			}
		}
		return $channels;
	}
}