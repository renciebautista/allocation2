<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddActivityIdOnActivityTypeNetworksTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('activity_type_networks', function(Blueprint $table)
		{
			$table->integer('task_id')->unsigned()->after('id');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('activity_type_networks', function(Blueprint $table)
		{
			$table->dropColumn(array('task_id'));
		});
	}

}
