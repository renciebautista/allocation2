<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class FixAllocReportTemplateTableSeeder extends Seeder {

	public function run()
	{
		$templates = AllocationReportTemplate::all();
		foreach ($templates as $template) {
			$template->created_at = date('Y-m-d H:i:s');
			$template->update();
		}
	}

}