<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRevisionWeightsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('revision_weights', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('description');
			$table->integer('weight');
		});


		DB::statement("INSERT INTO revision_weights (description, weight) VALUES
			('Minor (small portion)  text - add/revise', 1),
			('Major (large portion) text - add/revise', 3),
			('Minor element - add/revise', 1),
			('Major element - add/revise', 3),
			('Change font', 1),
			('Change color', 2),
			('Change of structure element', 4),
			('Multiple revision (1-3)', 4);");
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('revision_weights');
	}

}
