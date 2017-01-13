<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTargetDateOnJobordersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('joborders', function(Blueprint $table)
		{
			$table->date('target_date')->after('department_id');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('joborders', function(Blueprint $table)
		{
			$table->dropColumn(['target_date']);
		});
	}

}
