<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class ActivityStatusTableSeeder extends Seeder {

	public function run()
	{
		DB::statement('SET FOREIGN_KEY_CHECKS=0;');
		DB::table('activity_statuses')->truncate();

		DB::statement("INSERT INTO activity_statuses (id, status) VALUES
			(1, 'DRAFTED'),
			(2, 'DENIED'),
			(3, 'RECALLED'),
			(4, 'SUBMITTED TO PMOG'),
			(5, 'SUBMITTED TO GCOM'),
			(6, 'SUBMITTED TO CD OPS'),
			(7, 'SUBMITTED TO CMD DIRECTOR'), 
			(8, 'APPROVED'),
			(9, 'RELEASED');");
		DB::statement('SET FOREIGN_KEY_CHECKS=1;');
	}

}