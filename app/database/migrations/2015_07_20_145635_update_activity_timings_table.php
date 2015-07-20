<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class UpdateActivityTimingsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('activity_timings', function(Blueprint $table)
		{
			DB::statement('ALTER TABLE `activity_timings` CHANGE `final_start_date` `final_start_date` DATE NULL, CHANGE `final_end_date` `final_end_date` DATE NULL;');
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
			DB::statement('ALTER TABLE `activity_timings` CHANGE `final_start_date` `final_start_date` DATE NOT NULL, CHANGE `final_end_date` `final_end_date` DATE NOT NULL;');
		});
	}

}
