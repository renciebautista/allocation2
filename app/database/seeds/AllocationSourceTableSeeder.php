<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class AllocationSourceTableSeeder extends Seeder {

	public function run()
	{
		DB::statement('SET FOREIGN_KEY_CHECKS=0;');
		DB::table('allocation_sources')->truncate();

		DB::statement("INSERT INTO allocation_sources (id, alloc_ref) VALUES
			(1, 'USE SYSTEM GENERATED'),
			(2, 'USE MANUAL UPLOAD'),
			(3, 'NO ALLOCATION');");
		DB::statement('SET FOREIGN_KEY_CHECKS=1;');
	}

}