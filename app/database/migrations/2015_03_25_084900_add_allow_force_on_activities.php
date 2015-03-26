<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddAllowForceOnActivities extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('activities', function(Blueprint $table)
		{
			$table->boolean('allow_force')->default(0)->after('instruction');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('activities', function(Blueprint $table)
		{
			$table->dropColumn(array('allow_force'));
		});
	}

}
