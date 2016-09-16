<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTradedealTypesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tradedeal_types', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('tradedeal_type');
		});

		DB::statement("INSERT INTO tradedeal_types (id, tradedeal_type) VALUES
			(1, 'INDIVIDUAL'),
			(2, 'COLLECTIVE DEFINED'),
			(3, 'COLLECTIVE UNDEFINED');");
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('tradedeal_types');
	}

}
