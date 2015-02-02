<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class MonthTableSeeder extends Seeder {

	public function run()
	{
		DB::statement('SET FOREIGN_KEY_CHECKS=0;');
		DB::table('months')->truncate();

		DB::statement("INSERT INTO months (id, month) VALUES
			(1, 'JANUARY'),
			(2, 'FEBRUARY'),
			(3, 'MARCH'),
			(4, 'APRIL'),
			(5, 'MAY'),
			(6, 'JUNE'),
			(7, 'JULY'),
			(8, 'AUGUST'),
			(9, 'SEPTEMBER'),
			(10, 'OCTOBER'),
			(11, 'NOVEMBER'),
			(12, 'DECEMBER');");
		DB::statement('SET FOREIGN_KEY_CHECKS=1;');
	}

}