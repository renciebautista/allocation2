<?php

use Faker\Factory as Faker;

class SubChannelsTableSeeder extends Seeder {

	public function run()
	{
		DB::table('sub_channels')->truncate();
		Excel::selectSheets('sub_channel')->load(app_path().'/database/seeds/seed_files/masterfile.xlsx', function($reader) {
			SubChannel::batchInsert($reader->ignoreEmpty());
		});
	}

}