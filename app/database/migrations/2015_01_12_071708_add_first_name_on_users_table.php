<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddFirstNameOnUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('users', function(Blueprint $table)
		{
			$table->string('first_name')->after('id');
			$table->string('middle_initial')->after('first_name');
			$table->string('last_name')->after('middle_initial');
			$table->boolean('active')->after('confirmed')->default(1);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('users', function(Blueprint $table)
		{
			$table->dropColumn(array('first_name', 'middle_initial', 'last_name', 'active'));
		});
	}

}
