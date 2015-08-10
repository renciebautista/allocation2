<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class FixSchemePremiumSkuTableSeeder extends Seeder {

	public function run()
	{
		$premium_skus = SchemePremuimSku::all();
		foreach ($premium_skus as $premium_sku) {
			$pricelist = Pricelist::getSku($premium_sku->sap_code);
			if(!empty($pricelist)){
				$premium_sku->sap_desc = $pricelist->sap_desc;
				$premium_sku->pack_size = $pricelist->pack_size;
				$premium_sku->barcode = $pricelist->barcode;
				$premium_sku->case_code = $pricelist->case_code;
				$premium_sku->price_case = $pricelist->price_case;
				$premium_sku->price_case_tax = $pricelist->price_case_tax;
				$premium_sku->price = $pricelist->price;
				$premium_sku->srp = $pricelist->srp;
				$premium_sku->update();
			}
			
		}
	}

}