<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddChannelNodeOnTradedealSchemeChannels extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('tradedeal_scheme_channels', function(Blueprint $table)
		{
			$table->dropColumn(['tradedeal_channel_id', 'created_at', 'updated_at']);
			$table->string('channel_node')->after('tradedeal_scheme_id');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('tradedeal_scheme_channels', function(Blueprint $table)
		{
			$table->integer('tradedeal_channel_id')->unsigned()->after('tradedeal_scheme_id');
			$table->foreign('tradedeal_channel_id')->references('id')->on('tradedeal_channels');
			$table->timestamps();
			$table->dropColumn(['channel_node']);
		});
	}

}
