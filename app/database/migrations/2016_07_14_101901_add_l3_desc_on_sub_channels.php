<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddL3DescOnSubChannels extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('sub_channels', function(Blueprint $table)
		{
			$table->string('l3_desc')->nullable()->after('channel_code');
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
			$table->dropColumn(['l3_desc']);
		});
	}

}
