<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddTokenOnAllocationReportTemplates extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		
		DB::statement('ALTER TABLE `allocation_report_templates` CHANGE `token` `token` VARCHAR(255) NULL;');
		DB::statement('ALTER TABLE `allocation_report_templates` CHANGE `file_name` `file_name` VARCHAR(255) NULL;');
		DB::statement('ALTER TABLE `allocation_report_templates` CHANGE `template_name` `template_name` VARCHAR(255) NULL;');
		
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
			DB::statement('ALTER TABLE `allocation_report_templates` CHANGE `token` `token` VARCHAR(255) NOT NULL;');
			DB::statement('ALTER TABLE `allocation_report_templates` CHANGE `file_name` `file_name` VARCHAR(255) NOT NULL;');
			DB::statement('ALTER TABLE `allocation_report_templates` CHANGE `template_name` `template_name` VARCHAR(255) NOT NULL;');
		});
	}

}
