<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class BudgetTypeTableSeeder extends Seeder {

	public function run()
	{
		DB::statement('SET FOREIGN_KEY_CHECKS=0;');
		DB::table('budget_types')->truncate();

		DB::statement("INSERT INTO budget_types (id, budget_type) VALUES
			(1, 'TTS'),
			(2, 'PE');");
		DB::statement('SET FOREIGN_KEY_CHECKS=1;');
	}

}