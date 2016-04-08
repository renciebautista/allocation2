<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSobGroupsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sob_groups', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('sobgroup');
		});

		DB::statement("INSERT INTO sob_groups (id, sobgroup) VALUES
			(1, 'GMA / LUZ'),
			(2, 'VIS / MIN');");
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('sob_groups');
	}

}
