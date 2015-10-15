<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddTemplateIdOnAllocationReportFilesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('allocation_report_files', function(Blueprint $table)
		{
			$table->integer('template_id')->unsigned()->after('created_by');
			$table->foreign('template_id')->references('id')->on('allocation_report_templates');
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
			$table->dropForeign('allocation_report_files_template_id_foreign');
			$table->dropColumn(array('template_id'));
		});
	}

}
