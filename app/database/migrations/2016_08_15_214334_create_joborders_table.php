<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateJobordersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('joborders', function(Blueprint $table)
		{
			$table->engine = "MyISAM";
			$table->increments('id');
			$table->integer('activity_id')->unsigned();
			$table->foreign('activity_id')->references('id')->on('activities');
			$table->integer('created_by')->unsigned();
			$table->foreign('created_by')->references('id')->on('users');
			$table->integer('assigned_to')->unsigned();
			$table->foreign('assigned_to')->references('id')->on('users');
			$table->integer('task_id')->unsigned();
			$table->string('task');
			$table->integer('sub_task_id')->unsigned();
			$table->string('sub_task');
			$table->integer('department_id')->unsigned();
			$table->foreign('department_id')->references('id')->on('departments');
			$table->date('start_date');
			$table->date('end_date');
			$table->string('details');
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
		Schema::drop('joborders');
	}

}
