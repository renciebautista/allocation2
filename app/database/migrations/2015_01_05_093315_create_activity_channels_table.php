<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateActivityChannelsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('activity_channels', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('activity_id')->unsigned();
			$table->foreign('activity_id')->references('id')->on('activities');
			$table->integer('channel_id')->unsigned();
			$table->foreign('channel_id')->references('id')->on('channels');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('activity_channels', function(Blueprint $table)
		{
			$table->dropForeign('activity_channels_activity_id_foreign');
			$table->dropForeign('activity_channels_channel_id_foreign');
		});
		
		Schema::drop('activity_channels');
	}

}
