<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class ScopeTypesTableSeeder extends Seeder {

	public function run()
	{
		DB::table('scope_types')->truncate();

		DB::statement("INSERT INTO scope_types (id, scope_name) VALUES
			(1, 'NATIONAL'),
			(2, 'CUSTOMIZE');");
	}

}