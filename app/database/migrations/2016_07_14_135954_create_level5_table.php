<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLevel5Table extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('level5', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('l4_code');
			$table->string('l5_code');
			$table->string('l5_desc');
			$table->string('rtm_tag')->nullable();
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
		Schema::drop('level5');
	}

}
