<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class CyclesTableSeeder extends Seeder {

	public function run()
	{
		DB::table('cycles')->truncate();

		DB::statement("INSERT INTO cycles (id, cycle_name) VALUES
			(1, 'TEST CYCLE'),
			(2, 'CUSTOME CYCLE');");
	}

}