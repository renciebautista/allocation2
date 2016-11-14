<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTradedealSchemeAllocationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tradedeal_scheme_allocations', function(Blueprint $table)
		{
			$table->engine = "MyISAM";
			$table->increments('id');
			$table->integer('tradedeal_scheme_sku_id')->unsigned();
			$table->foreign('tradedeal_scheme_sku_id')->references('id')->on('tradedeal_scheme_skus');
			$table->string('area_code');
			$table->string('area');
			$table->string('sold_to_code');
			$table->string('sold_to');
			$table->string('ship_to_code');
			$table->string('plant_code');
			$table->string('ship_to_name');
			$table->decimal('sold_to_gsv', 15,3);
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
		Schema::drop('tradedeal_scheme_allocations');
	}

}
