<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class UpdateCreatedAtOnAllocationReportTemplatesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('allocation_report_templates', function(Blueprint $table)
		{
			$table->string('token')->after('name');
			$table->string('file_name')->after('token');
			$table->string('template_name')->after('file_name');
			$table->dateTime('report_generated')->after('template_name');
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
		Schema::table('allocation_report_templates', function(Blueprint $table)
		{
			$table->dropColumn(array('token', 'file_name','template_name','report_generated','created_at','updated_at'));
		});
	}

}
