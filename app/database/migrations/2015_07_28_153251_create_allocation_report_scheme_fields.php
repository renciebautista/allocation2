<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAllocationReportSchemeFields extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('allocation_report_scheme_fields', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('field_name');
			$table->string('desc_name');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('allocation_report_scheme_fields');
	}

}
