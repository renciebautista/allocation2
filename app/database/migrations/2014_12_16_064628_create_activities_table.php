<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateActivitiesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('activities', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('circular_name');
			$table->integer('scope_type_id')->unsigned();
			$table->foreign('scope_type_id')->references('id')->on('scope_types');
			$table->integer('cycle_id')->unsigned();
			$table->foreign('cycle_id')->references('id')->on('cycles');
			$table->integer('activity_type_id')->unsigned();
			$table->foreign('activity_type_id')->references('id')->on('activity_types');
			$table->integer('division_code');
			$table->decimal('budget_tts',15,2);
			$table->decimal('budget_pe',15,2);
			$table->text('background');
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('activities');
	}

}
