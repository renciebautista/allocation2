<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class FixActivityBrandTableSeeder extends Seeder {

	public function run()
	{
		$activitybrands = ActivityBrand::all();
		foreach($activitybrands  as $activitybrand){
			$brand = Sku::brand($activitybrand->brand_code);
			if(!empty($brand)){
				$activitybrand->brand_desc = $brand->brand_desc.' - '.$brand->cpg_desc;
				$activitybrand->update();
			}
			
		}
	}

}