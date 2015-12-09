<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddPrimaryKeyOnSubChannelsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('sub_channels', function(Blueprint $table)
		{
			$table->increments('id')->before('coc_03_code');
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
			$table->dropColumn(array('id'));
		});
	}

}
