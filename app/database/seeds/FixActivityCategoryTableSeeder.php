<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class FixActivityCategoryTableSeeder extends Seeder {

	public function run()
	{
		$activitycategories = ActivityCategory::all();
		foreach($activitycategories  as $activitycategory){
			$category = Sku::category($activitycategory->category_code);
			$activitycategory->category_desc = $category->category_desc;
			$activitycategory->update();
		}
	}

}