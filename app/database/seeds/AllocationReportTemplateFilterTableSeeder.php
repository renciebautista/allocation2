<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class AllocationReportTemplateFilterTableSeeder extends Seeder {

	public function run()
	{
		DB::statement('SET FOREIGN_KEY_CHECKS=0;');
		DB::table('allocation_report_template_filters')->truncate();

		DB::statement("INSERT INTO allocation_report_template_filters (filter_desc) VALUES
			('STATUS'),
			('SCOPE'),
			('PROPONENT'),
			('PLANNER'),
			('APPROVER'),
			('ACTIVITY TYPE'),
			('DIVISION'),
			('CATEGORY'),
			('BRAND'),
			('GROUP'),
			('AREA'),
			('SOLDTO'),
			('SHIPTO'),
			('OUTLET');");
		DB::statement('SET FOREIGN_KEY_CHECKS=1;');
	}

}