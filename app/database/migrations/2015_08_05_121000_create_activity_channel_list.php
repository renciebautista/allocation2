<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateActivityChannelList extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('activity_channel_list', function(Blueprint $table)
		{
			$table->bigIncrements('id');
			$table->string('parent_id')->nullable();
			$table->integer('activity_id');
			$table->string('title');
			$table->boolean('isfolder');
			$table->string('key');
			$table->boolean('unselectable');
			$table->boolean('selected');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('activity_channel_list');
	}

}
