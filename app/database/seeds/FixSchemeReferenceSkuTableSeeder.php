<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class FixSchemeReferenceSkuTableSeeder extends Seeder {

	public function run()
	{
		$scheme_skus = SchemeSku::all();
		foreach($scheme_skus  as $scheme_sku){
			$sku = Sku::getSku($scheme_sku->sku);
			if(!empty($sku)){
				$scheme_sku->sku_desc = $sku->sku_desc;
				$scheme_sku->division_code = $sku->division_code;
				$scheme_sku->division_desc = $sku->division_desc;
				$scheme_sku->category_code = $sku->category_code;
				$scheme_sku->category_desc = $sku->category_desc;
				$scheme_sku->brand_code = $sku->brand_code;
				$scheme_sku->brand_desc = $sku->brand_desc;
				$scheme_sku->cpg_code = $sku->cpg_code;
				$scheme_sku->cpg_desc = $sku->cpg_desc;
				$scheme_sku->packsize_code = $sku->packsize_code;
				$scheme_sku->packsize_desc = $sku->packsize_desc;
				$scheme_sku->update();
			}
			
		}
	}

}