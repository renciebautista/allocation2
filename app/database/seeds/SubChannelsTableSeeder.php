<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class SubChannelsTableSeeder extends Seeder {

	public function run()
	{
		Excel::selectSheets('sub_channel')->load(app_path().'/database/seeds/seed_files/masterfile.xlsx', function($reader) {
			SubChannel::batchInsert($reader->ignoreEmpty());
		});
	}

}