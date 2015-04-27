<?php

// Composer: "fzaninotto/faker": "v1.3.0"
// use Faker\Factory as Faker;

class MotherChildSkusTableSeeder extends Seeder {

	public function run()
	{
		DB::table('mother_child_skus')->truncate();
		Excel::selectSheets('mother_child_sku')->load(app_path().'/database/seeds/seed_files/masterfile.xlsx', function($reader) {
			MotherChildSku::batchInsert($reader->ignoreEmpty());
		});
	}

}