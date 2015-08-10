<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddDescOnActivitiesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('activities', function(Blueprint $table)
		{
			$table->string('scope_desc')->after('scope_type_id');
			$table->string('cycle_desc')->after('cycle_id');
			$table->string('activitytype_desc')->after('activity_type_id');
			$table->string('uom_desc')->after('activitytype_desc');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('activities', function(Blueprint $table)
		{
			$table->dropColumn(['scope_desc', 'cycle_desc','activity_desc','uom_desc']);
		});
	}

}
