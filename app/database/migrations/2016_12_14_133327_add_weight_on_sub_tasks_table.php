<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddWeightOnSubTasksTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('sub_tasks', function(Blueprint $table)
		{
			$table->integer('weight')->after('lead_time');
			$table->decimal('cost', 12,2)->after('weight');
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
			$table->dropColumn(['weight', 'cost']);
		});
	}

}
