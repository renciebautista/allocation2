<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTradedealChannelSchemes extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tradedeal_channel_schemes', function(Blueprint $table)
		{

			$table->engine = "MyISAM";
			
			$table->increments('id');
			$table->integer('tradedeal_channel_id')->unsigned();
			$table->foreign('tradedeal_channel_id')->references('id')->on('tradedeal_channels');
			$table->integer('tradedeal_part_sku_id')->unsigned();
			$table->foreign('tradedeal_part_sku_id')->references('id')->on('tradedeal_part_skus');
			$table->integer('tradedeal_uom_id')->unsigned();
			$table->foreign('tradedeal_uom_id')->references('id')->on('tradedeal_uoms');
			$table->integer('buy');
			$table->integer('free');

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
		Schema::drop('tradedeal_channel_schemes');
	}

}
