<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTradedealChannelListsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tradedeal_channel_lists', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('parent_id')->nullable();
			$table->integer('tradedeal_scheme_id');
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
		Schema::drop('tradedeal_channel_lists');
	}

}
