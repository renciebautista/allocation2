<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class DepartmentTableSeederTableSeeder extends Seeder {

	public function run()
	{
		DB::statement('SET FOREIGN_KEY_CHECKS=0;');
		DB::table('departments')->truncate();

		DB::statement("INSERT INTO departments (id, department) VALUES
			(1, 'ADMIN'),
			(2, 'CMD'),
			(3, 'FIELD'),
			(4, 'CHANNEL'),
			(5, 'PMOG'),
			(6, 'OTHERS');");
		DB::statement('SET FOREIGN_KEY_CHECKS=1;');
	}

}