<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateChannelsToChannels2 extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$activities = Activity::all();
		foreach ($activities as $activity) {
			$channels = ActivityChannel::join('channels', 'activity_channels.channel_id', '=', 'channels.id')
				->where('activity_id',$activity->id)->get();
			if(!empty($channels)){
				$activity_channels = array();
				foreach ($channels as $channel){
					$activity_channels[] = array('activity_id' => $activity->id, 'channel_node' => $channel->channel_code);
				}
				if(!empty($activity_channels)){
					ActivityChannel2::insert($activity_channels);
				}
				
			}
		}
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		
	}

}
