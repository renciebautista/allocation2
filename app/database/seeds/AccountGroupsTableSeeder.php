<?php

// Composer: "fzaninotto/faker": "v1.3.0"
// use Faker\Factory as Faker;

class AccountGroupsTableSeeder extends Seeder {

	public function run()
	{
		DB::table('account_groups')->truncate();
		Excel::selectSheets('account_group')->load(app_path().'/database/seeds/seed_files/masterfile.xlsx', function($reader) {
			AccountGroup::batchInsert($reader->get());
		});
	}

}