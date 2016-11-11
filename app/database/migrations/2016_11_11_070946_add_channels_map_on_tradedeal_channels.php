<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddChannelsMapOnTradedealChannels extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('tradedeal_channels', function(Blueprint $table)
		{
			$table->dropColumn(['l5_code', 'l5_desc']);
			$table->string('coc_03_code')->after('activity_id');
			$table->string('coc_04_code')->after('coc_03_code');
			$table->string('coc_05_code')->after('coc_04_code');
			$table->string('sub_channel_desc')->after('channel_desc');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('tradedeal_channels', function(Blueprint $table)
		{
			$table->string('l5_code')->after('activity_id');
			$table->string('l5_desc')->after('l5_code');

			$table->dropColumn(['coc_03_code', 'coc_04_code', 'coc_05_code', 'sub_channel_desc']);
		});
	}

}
