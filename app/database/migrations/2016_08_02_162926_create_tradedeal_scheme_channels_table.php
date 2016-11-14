<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTradedealSchemeChannelsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tradedeal_scheme_channels', function(Blueprint $table)
		{
			$table->engine = "MyISAM";
			$table->increments('id');
			$table->integer('tradedeal_scheme_id')->unsigned();
			$table->foreign('tradedeal_scheme_id')->references('id')->on('tradedeal_schemes');
			$table->integer('tradedeal_channel_id')->unsigned();
			$table->foreign('tradedeal_channel_id')->references('id')->on('tradedeal_channels');
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('tradedeal_scheme_channels');
	}

}
