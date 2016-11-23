<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddLeadTimeOnSubTasksTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('sub_tasks', function(Blueprint $table)
		{
			$table->integer('lead_time')->after('sub_task')->default(1);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('sub_tasks', function(Blueprint $table)
		{
			$table->dropColumn(['lead_time']);
		});
	}

}
