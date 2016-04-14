<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sos', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('series');
			$table->boolean('locked');
			$table->timestamps();
		});

		DB::statement("INSERT INTO sos (id, series, locked) VALUES
			(1, 1, 0);");
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('sos');
	}

}
