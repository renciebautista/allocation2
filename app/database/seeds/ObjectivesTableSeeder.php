<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class ObjectivesTableSeeder extends Seeder {

	public function run()
	{
		DB::table('objectives')->truncate();
		
		DB::statement("INSERT INTO objectives (id, objective) VALUES
			(1, 'INCREASE PENETRATION'),
			(2, 'INCREASE CONSUMPTION'),
			(3, 'UPGRADATION'),
			(4, 'INCREASE VISIBILITY'),
			(5, 'OTHERS');");
	}

}