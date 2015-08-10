<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddContactNoOnActivityPlannersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('activity_planners', function(Blueprint $table)
		{
			$table->string('contact_no')->after('planner_desc');
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
			$table->dropColumn(array('contact_no'));
		});
	}

}
