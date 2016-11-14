<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTradeCollectiveSeriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('trade_collective_series', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('month_year');
			$table->integer('tradedeal_scheme_id');
			$table->integer('series');
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
		Schema::drop('trade_collective_series');
	}

}
