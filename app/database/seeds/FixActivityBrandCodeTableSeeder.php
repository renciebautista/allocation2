<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class FixActivityBrandCodeTableSeeder extends Seeder {

	public function run()
	{
		$activitybrands = ActivityBrand::all();
		foreach($activitybrands  as $activitybrand){
			$brand = Pricelist::where('cpg_code',$activitybrand->brand_code)->first();
			if(!empty($brand)){
				$activitybrand->b_desc = $brand->brand_desc;
				$activitybrand->update();
			}
			
		}
	}

}