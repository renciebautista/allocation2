<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class JoborderStatusSeederTableSeeder extends Seeder {

	public function run()
	{
		DB::statement('SET FOREIGN_KEY_CHECKS=0;');
		DB::table('joborder_status')->truncate();

		DB::statement("INSERT INTO joborder_status (id, joborder_status) VALUES
			(1, 'UNASSIGNED'),
			(2, 'ASSIGNED'),
			(3, 'RETURNED'),
			(4, 'CLOSED'),
			(5, 'CANCELED');");
		DB::statement('SET FOREIGN_KEY_CHECKS=1;');
	}

}