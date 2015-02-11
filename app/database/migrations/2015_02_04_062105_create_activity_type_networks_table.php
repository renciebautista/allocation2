<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateActivityTypeNetworksTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('activity_type_networks', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('activitytype_id')->unsigned();
            $table->foreign('activitytype_id')->references('id')->on('activity_types');
			$table->text('milestone');
			$table->text('task');
			$table->text('responsible');
			$table->integer('duration');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('activity_type_networks');
	}

}
