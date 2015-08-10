<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddDescOnActivityObjectivesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('activity_objectives', function(Blueprint $table)
		{
			$table->string('objective_desc')->after('objective_id');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('activity_objectives', function(Blueprint $table)
		{
			$table->dropColumn(['objective_desc']);
		});
	}

}
