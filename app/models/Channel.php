<?php

class Channel extends \Eloquent {
	protected $fillable = ['channel_code', 'channel_name'];
	public $timestamps = false;

	public static function batchInsert($records){
		$records->each(function($row) {
			if(!is_null($row->channel_code)){
				$attributes = array(
					'channel_code' => $row->channel_code,
					'channel_name' => $row->channel_name);
				self::updateOrCreate($attributes, $attributes);
			}
			
		});
	}

	public static function getList(){
		return self::orderBy('channel_name')->lists('channel_name', 'id');
	}

	public static function getChannelList(){
		$unselectable = true;
		$channels = Channel::orderBy('id')->get();
		$data = array();
		foreach ($channels as $channel) {
			$subgroups = Account::getChannelGroup($channel->channel_code);
			$group_children = array();

			if(count($subgroups)>0){
				foreach ($subgroups as $subgroup) {
					$group_children[] = array(
						'title' => $subgroup->account_group_name,
						'isfolder' => false,
						'unselectable' => $unselectable,
						'key' => $channel->channel_code.".".$subgroup->account_group_code,
						);
				}
				$group_children[] = array(
						'title' => 'OTHERS',
						'isfolder' => false,
						'unselectable' => $unselectable,
						'key' => $channel->channel_code.".OTHERS",
						);
			}
			
			
			$data[] = array(
				'title' => $channel->channel_name,
				'isfolder' => true,
				'unselectable' => $unselectable,
				'key' => $channel->channel_code,
				'children' => $group_children
				);
		}
		return $data;
	}
}