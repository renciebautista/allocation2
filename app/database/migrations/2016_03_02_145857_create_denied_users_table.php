<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDeniedUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('denied_users', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('first_name');
			$table->string('middle_initial');
			$table->string('last_name');
            $table->string('username');
            $table->string('email');
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
		Schema::drop('denied_users');
	}

}
