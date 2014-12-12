<?php

// Composer: "fzaninotto/faker": "v1.3.0"
// use Faker\Factory as Faker;

class AccountsTableSeeder extends Seeder {

	public function run()
	{
		Excel::selectSheets('account')->load(app_path().'/database/seeds/seed_files/masterfile.xlsx', function($reader) {
			Account::batchInsert($reader->ignoreEmpty());
		});
	}

}