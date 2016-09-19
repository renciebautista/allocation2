<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class DepartmentTableSeederTableSeeder extends Seeder {

	public function run()
	{
		DB::statement('SET FOREIGN_KEY_CHECKS=0;');
		DB::table('departments')->truncate();

		DB::statement("INSERT INTO departments (id, department) VALUES
			(1, 'ADMINISTRATOR');");
		DB::statement('SET FOREIGN_KEY_CHECKS=1;');
	}

}