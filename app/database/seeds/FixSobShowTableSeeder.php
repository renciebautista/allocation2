<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class FixSobShowTableSeeder extends Seeder {

	public function run()
	{
		set_time_limit(0);
		$_customers = Allocation::where('group_code','E1397')
			->whereNull('customer_id')
			->whereNull('shipto_id')
			->orderBy('id')
			->get();
		foreach ($_customers as $_customer) {
			$_shiptos = Allocation::where('customer_id',$_customer->id)->get();
			if(count($_shiptos) == 0){
				$_customer->sob = true;
				$_customer->update();
			}else{
				foreach ($_shiptos as $_shipto) {
					$_shipto->sob = true;
					$_shipto->update();
				}
			}

		}
	}

}