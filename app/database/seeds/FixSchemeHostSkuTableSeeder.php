<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class FixSchemeHostSkuTableSeeder extends Seeder {

	public function run()
	{
		$host_skus = SchemeHostSku::all();
		foreach ($host_skus as $host_sku) {
			$pricelist = Pricelist::getSku($host_sku->sap_code);
			if(!empty($pricelist)){
				$host_sku->sap_desc = $pricelist->sap_desc;
				$host_sku->pack_size = $pricelist->pack_size;
				$host_sku->barcode = $pricelist->barcode;
				$host_sku->case_code = $pricelist->case_code;
				$host_sku->price_case = $pricelist->price_case;
				$host_sku->price_case_tax = $pricelist->price_case_tax;
				$host_sku->price = $pricelist->price;
				$host_sku->srp = $pricelist->srp;
				$host_sku->update();
			}
			
		}
	}

}