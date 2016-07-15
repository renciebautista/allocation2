<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTradedealChannelsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tradedeal_channels', function(Blueprint $table)
		{
			$table->engine = "MyISAM";

			$table->increments('id');
			$table->integer('activity_id')->unsigned()->nullable();
			$table->foreign('activity_id')->references('id')->on('activities');
			$table->string('l5_code');
			$table->string('l5_desc');
			$table->string('rtm_tag')->nullable();
			$table->integer('tradedeal_type_id')->unsigned()->nullable();
			$table->foreign('tradedeal_type_id')->references('id')->on('tradedeal_types');
			$table->integer('tradedeal_uom_id')->unsigned()->nullable();
			$table->foreign('tradedeal_uom_id')->references('id')->on('tradedeal_uoms');
			$table->string('scheme');
			$table->integer('buy')->unsigned()->nullable();
			$table->integer('free')->unsigned()->nullable();
			$table->decimal('pur_req', 10, 2);
			$table->integer('buy_pcs');
			$table->integer('pcs_deal');
			$table->string('scheme_code');
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
		Schema::drop('tradedeal_channels');
	}

}
