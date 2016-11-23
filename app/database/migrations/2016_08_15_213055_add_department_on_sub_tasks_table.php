<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddDepartmentOnSubTasksTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('sub_tasks', function(Blueprint $table)
		{
			$table->integer('department_id')->unsigned()->nullable()->after('sub_task');
			$table->foreign('department_id')->references('id')->on('departments');
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
			$table->dropColumn(['department_id']);
		});
	}

}
