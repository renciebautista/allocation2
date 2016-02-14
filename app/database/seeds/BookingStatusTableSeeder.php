<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class BookingStatusTableSeeder extends Seeder {

	public function run()
	{
		DB::statement('SET FOREIGN_KEY_CHECKS=0;');
		DB::table('booking_status')->truncate();

		DB::statement("INSERT INTO booking_status (id, status) VALUES
			(1, 'FOR SKU VERIFICATION'),
			(2, 'FOR REVIEWING'),
			(3, 'FOR PROCESSING'),
			(4, 'PROCESSED'),
			(5, 'INVOICED'),
			(6, 'CLOSED');");
		DB::statement('SET FOREIGN_KEY_CHECKS=1;');
	}

}