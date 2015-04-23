<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddShowOnActivityTimingsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('activity_timings', function(Blueprint $table)
		{
			$table->boolean('show')->default(false)->after('depend_on');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('activity_timings', function(Blueprint $table)
		{
			$table->dropColumn(array('show'));
		});
	}

}
