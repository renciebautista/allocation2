<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateShipHolidaysTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ship_holidays', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('sob_holiday_id')->unsigned();
			$table->foreign('sob_holiday_id')->references('id')->on('sob_holidays');
			$table->string('ship_to_code');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ship_holidays');
	}

}
