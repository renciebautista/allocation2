<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateActivityDivisionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('activity_divisions', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('activity_id')->unsigned();
			$table->foreign('activity_id')->references('id')->on('activities');
			$table->string('division_code');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('activity_divisions');
	}

}
