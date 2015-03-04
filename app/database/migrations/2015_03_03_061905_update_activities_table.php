<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateActivitiesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('activities', function(Blueprint $table)
		{
			$table->foreign('scope_type_id')->references('id')->on('scope_types');
			$table->foreign('cycle_id')->references('id')->on('cycles');
			$table->foreign('activity_type_id')->references('id')->on('activity_types');

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
			$table->dropForeign('activities_scope_type_id_foreign');
			$table->dropForeign('activities_cycle_id_foreign');
			$table->dropForeign('activities_activity_type_id_foreign');
		});
	}

}
