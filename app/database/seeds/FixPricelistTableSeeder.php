<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class FixPricelistTableSeeder extends Seeder {

	public function run()
	{
		Excel::selectSheets('Initial')->load(app_path().'/database/seeds/seed_files/pricelist2.xlsx', function($reader) {
			Pricelist::updatePriceList($reader->ignoreEmpty());
		});
	}

}