<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddChannelIdOnSubTypes extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('tradedeal_scheme_sub_types', function(Blueprint $table)
		{
			$table->integer('tradedeal_scheme_channel_id');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('tradedeal_scheme_sub_types', function(Blueprint $table)
		{
			$table->dropColumn(['tradedeal_scheme_channel_id']);
		});
	}

}
