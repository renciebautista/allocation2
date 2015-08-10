<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class FixActivitySkuTableSeeder extends Seeder {

	public function run()
	{
		$activityskus = ActivitySku::all();
		foreach($activityskus  as $activitysku){
			$sku = Pricelist::getSku($activitysku->sap_code);
			if(!empty($sku)){
				$activitysku->sap_desc = $sku->sap_desc. " - ".$sku->sap_code;
				$activitysku->update();
			}
			
		}
	}

}