<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateActivityObjectivesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('activity_objectives', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('activity_id')->unsigned();
			$table->foreign('activity_id')->references('id')->on('activities');
			$table->integer('objective_id')->unsigned();
			$table->foreign('objective_id')->references('id')->on('objectives');
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
			$table->dropForeign('activity_objectives_activity_id_foreign');
			$table->dropForeign('activity_objectives_objective_id_foreign');
		});
		
		Schema::drop('activity_objectives');
	}

}
