<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddDayOfWeekOnShipTosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('ship_tos', function(Blueprint $table)
		{
			$table->integer('dayofweek')->after('split')->default(0);
			$table->integer('leadtime')->after('dayofweek')->default(1);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('ship_tos', function(Blueprint $table)
		{
			$table->dropColumn(['dayofweek','leadtime']);
		});
	}

}
