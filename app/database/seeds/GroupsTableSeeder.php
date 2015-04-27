<?php

// Composer: "fzaninotto/faker": "v1.3.0"
// use Faker\Factory as Faker;

class GroupsTableSeeder extends Seeder {

	public function run()
	{
		DB::table('groups')->truncate();
		Excel::selectSheets('group')->load(app_path().'/database/seeds/seed_files/masterfile.xlsx', function($reader) {
			Group::batchInsert($reader->get());
		});
	}

}