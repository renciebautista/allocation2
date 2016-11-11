<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class RemoveL4l5Tables extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::drop('level5');
		Schema::drop('level4');

		Schema::table('sub_channels', function(Blueprint $table)
		{
			$table->string('l4_code');
			$table->string('l4_desc');
			$table->string('l5_code');
			$table->string('l5_desc');
			$table->string('rtm_tag');
			$table->boolean('trade_deal');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('sub_channels', function(Blueprint $table)
		{
			$table->dropColumn(['l4_code', 'l4_desc', 'l5_code', 'l5_desc', 'rtm_tag', 'trade_deal']);
		});

		Schema::create('level5', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('l4_code');
			$table->string('l5_code');
			$table->string('l5_desc');
			$table->string('rtm_tag');
			$table->boolean('trade_deal');
		});

		Schema::create('level4', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('coc_03_code');
			$table->string('l4_code');
			$table->string('l4_desc');
		});

	}

}
