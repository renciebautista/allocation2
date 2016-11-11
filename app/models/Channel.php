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

	public static function import($records){
		DB::beginTransaction();
			try {
			DB::table('channels')->truncate();
			DB::table('sub_channels')->truncate();
			DB::table('level4')->truncate();
			DB::table('level5')->truncate();
			$records->each(function($row)  {
				if(!is_null($row->channel_code)){
					Channel::firstOrCreate(['channel_code' => $row->channel_code, 'channel_name' => $row->channel_name]);
					SubChannel::firstOrCreate(['coc_03_code' => $row->coc_03_code, 'channel_code' => $row->channel_code, 'l3_desc' => $row->l3_desc]);
					if(!empty($row->l4_code)){
						Level4::firstOrCreate(['coc_03_code' => $row->coc_03_code, 'l4_code' => $row->l4_code, 'l4_desc' => $row->l4_desc]);
					}
					if(!empty($row->l5_code)){
						Level5::firstOrCreate(['l4_code' => $row->l4_code, 'l5_code' => $row->l5_code, 'l5_desc' => $row->l5_desc, 'rtm_tag' => $row->rtm_tag]);
					}
				}
				
			});
			DB::commit();
		} catch (\Exception $e) {
			// dd($e);
			DB::rollback();
		}
	}
}