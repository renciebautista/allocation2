<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class FixPricelistTableSeeder extends Seeder {

	public function run()
	{
		Excel::selectSheets('cycles')->load(app_path().'/database/seeds/seed_files/pricelists_cpg.csv', function($reader) {
			Pricelist::updateCpg($reader->ignoreEmpty());
		});
	}

}