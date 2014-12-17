<?php

// Composer: "fzaninotto/faker": "v1.3.0"
// use Faker\Factory as Faker;

class CustomersTableSeeder extends Seeder {

	public function run()
	{
		DB::table('customers')->truncate();

		Excel::selectSheets('customer')->load(app_path().'/database/seeds/seed_files/masterfile.xlsx', function($reader) {
			Customer::batchInsert($reader->get());
		});
	}

}