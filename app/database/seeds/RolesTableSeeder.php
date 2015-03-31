<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class RolesTableSeeder extends Seeder {

	public function run()
	{
		DB::statement('SET FOREIGN_KEY_CHECKS=0;');
		DB::table('roles')->truncate();

		DB::statement("INSERT INTO roles (id, name) VALUES
			(1, 'ADMINISTRATOR'),
			(2, 'PROPONENT'),
			(3, 'PMOG PLANNER'),
			(4, 'GCOM APPROVER'),
			(5, 'CD OPS APPROVER'),
			(6, 'CMD DIRECTOR'),
			(7, 'FIELD SALES');");
		DB::statement('SET FOREIGN_KEY_CHECKS=1;');
	}

}