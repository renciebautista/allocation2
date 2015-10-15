<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class RemoveAllocReportTableSeeder extends Seeder {

	public function run()
	{
		DB::table('allocation_report_files')->truncate();
		DB::table('allocation_report_filters')->truncate();
		DB::table('alloc_scheme_template_fields')->truncate();
		DB::table('allocation_report_templates')->truncate();
	}

}