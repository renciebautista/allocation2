<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class MaterialSourceTableSeeder extends Seeder {

	public function run()
	{
		DB::statement('SET FOREIGN_KEY_CHECKS=0;');
		DB::table('material_sources')->truncate();

		DB::statement("INSERT INTO material_sources (id, source) VALUES
			(1, 'EX-ULP'),
			(2, 'EX-AAA'),
			(3, 'EX-DISTRIBUTOR'),
			(4, 'EX-ACCOUNT');");
		DB::statement('SET FOREIGN_KEY_CHECKS=1;');

	}

}