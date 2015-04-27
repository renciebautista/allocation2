<?php

// Composer: "fzaninotto/faker": "v1.3.0"
// use Faker\Factory as Faker;

class ChannelsTableSeeder extends Seeder {

	public function run()
	{
		DB::statement('SET FOREIGN_KEY_CHECKS=0;');
		DB::table('channels')->truncate();
		Excel::selectSheets('channel')->load(app_path().'/database/seeds/seed_files/masterfile.xlsx', function($reader) {
			Channel::batchInsert($reader->get());
		});
		DB::statement('SET FOREIGN_KEY_CHECKS=1;');
	}

}