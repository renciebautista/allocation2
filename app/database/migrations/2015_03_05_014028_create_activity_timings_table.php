<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateActivityTimingsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('activity_timings', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('activity_id')->unsigned();
            $table->foreign('activity_id')->references('id')->on('activities');
			$table->integer('task_id');
			$table->string('milestone');
			$table->string('task');
			$table->string('responsible');
			$table->integer('duration');
			$table->string('depend_on');
			$table->date('start_date');
			$table->date('end_date');
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
			$table->dropForeign('activity_timings_activity_id_foreign');
		});

		Schema::drop('activity_timings');
	}

}
