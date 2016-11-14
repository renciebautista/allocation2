<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateShipToPlantCodesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ship_to_plant_codes', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('group_code');
			$table->string('group');
			$table->string('area_code');
			$table->string('area');
			$table->string('customer_code');
			$table->string('customer');
			$table->string('distributor_code');
			$table->string('distributor_name');
			$table->string('plant_code');
			$table->string('ship_to_name');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ship_to_plant_codes');
	}

}
