<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddFinalDatesOnActivityTimingsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('activity_timings', function(Blueprint $table)
		{
			$table->date('final_start_date');
			$table->date('final_end_date');
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
			$table->dropForeign('final_start_date', 'final_end_date');
		});
	}

}
