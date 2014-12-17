<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSubChannelsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sub_channels', function(Blueprint $table)
		{
			$table->string('coc_03_code');
			$table->string('channel_code');
			
			$table->index('coc_03_code');
			$table->index('channel_code');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('sub_channels');
	}

}
