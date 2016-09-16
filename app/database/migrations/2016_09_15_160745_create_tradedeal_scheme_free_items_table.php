<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTradedealSchemeFreeItemsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tradedeal_scheme_free_items', function(Blueprint $table)
		{
			$table->engine = "MyISAM";
			$table->increments('id');
			$table->integer('tradedeal_scheme_id')->unsigned();
			$table->foreign('tradedeal_scheme_id')->references('id')->on('tradedeal_schemes');
			$table->integer('tradedeal_scheme_sku_id')->unsigned();
			$table->foreign('tradedeal_scheme_sku_id')->references('id')->on('tradedeal_scheme_skus');
			$table->string('pre_code');
			$table->string('pre_desc');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('tradedeal_scheme_free_items');
	}

}
