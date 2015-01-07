<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class SplitOldCustomerTableSeeder extends Seeder {

	public function run()
	{
		DB::table('split_old_customers')->truncate();
		
		Excel::selectSheets('split_old_customer')->load(app_path().'/database/seeds/seed_files/masterfile.xlsx', function($reader) {
			SplitOldCustomer::batchInsert($reader->ignoreEmpty());
		});
	}

}