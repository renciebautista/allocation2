<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddDescOnActivityPlannersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('activity_planners', function(Blueprint $table)
		{
			$table->string('planner_desc')->after('user_id');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('activity_planners', function(Blueprint $table)
		{
			$table->dropColumn(['planner_desc']);
		});
	}

}
