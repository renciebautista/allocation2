<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTradedealHostSkus extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tradedeal_host_skus', function(Blueprint $table)
		{
			$table->engine = "MyISAM";

			$table->increments('id');
			$table->integer('tradedeal_part_sku_id')->unsigned();
			$table->foreign('tradedeal_part_sku_id')->references('id')->on('tradedeal_part_skus');
			$table->string('code');
			$table->string('desc');
			$table->decimal('cost',10,2);
			$table->integer('pcs_case');
			$table->integer('qty');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('tradedeal_host_skus');
	}

}
