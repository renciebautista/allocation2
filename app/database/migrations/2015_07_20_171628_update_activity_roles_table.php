<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class UpdateActivityRolesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('activity_roles', function(Blueprint $table)
		{
			DB::statement('ALTER TABLE `activity_roles` CHANGE `timing` `timing` VARCHAR(255) NOT NULL;');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('activity_roles', function(Blueprint $table)
		{
			DB::statement('ALTER TABLE `activity_roles` CHANGE `timing` `timing` DATE NOT NULL;');
		});
	}

}
