<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class RemoveTradedealSchemeFreeItemsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('tradedeal_scheme_free_items', function(Blueprint $table)
		{
			Schema::drop('tradedeal_scheme_free_items');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('tradedeal_scheme_free_items', function(Blueprint $table)
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

}
