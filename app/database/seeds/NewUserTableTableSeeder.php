<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class NewUserTableTableSeeder extends Seeder {

	public function run()
	{
		
		Excel::selectSheets('users')->load(app_path().'/database/seeds/seed_files/masterfile2.xlsx', function($reader) {
			User::batchInsert($reader->toArray());
		});
	}

}