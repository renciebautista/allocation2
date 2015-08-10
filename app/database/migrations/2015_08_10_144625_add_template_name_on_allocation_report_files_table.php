<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddTemplateNameOnAllocationReportFilesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('allocation_report_files', function(Blueprint $table)
		{
			$table->string('template_name')->after('file_name');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('allocation_report_files', function(Blueprint $table)
		{
			$table->dropColumn(array('template_name'));
		});
	}

}
