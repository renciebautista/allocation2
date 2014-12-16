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
			$table->integer('activity_id');
			$table->integer('objective_id');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('activity_objectives');
	}

}
