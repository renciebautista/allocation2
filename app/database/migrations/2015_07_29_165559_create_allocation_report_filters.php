<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAllocationReportFilters extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('allocation_report_filters', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('template_id');
			$table->integer('filter_type_id');
			$table->string('filter_id');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('allocation_report_filters');
	}

}
