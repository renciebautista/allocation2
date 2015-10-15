<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class RemoveTokenOnAllocationReportTemplates extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('allocation_report_templates', function(Blueprint $table)
		{
			$table->dropColumn(array('token', 'file_name','template_name','report_generated'));
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('allocation_report_templates', function(Blueprint $table)
		{
			$table->string('token')->after('name')->nullable();
			$table->string('file_name')->after('token')->nullable();
			$table->string('template_name')->after('file_name')->nullable();
			$table->dateTime('report_generated')->after('template_name')->nullable();
		});
	}

}
