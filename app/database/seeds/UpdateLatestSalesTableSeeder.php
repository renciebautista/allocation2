<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class UpdateLatestSalesTableSeeder extends Seeder {

	public function run()
	{
		Eloquent::unguard();
		DB::statement('SET FOREIGN_KEY_CHECKS=0;');

		$this->call('MtPrimarySalesTableSeeder');
		$this->call('DtSecondarySalesTableSeeder');
		$this->call('ShipToSalesTableSeeder');
		$this->call('OutletSalesTableSeeder');

		DB::statement('SET FOREIGN_KEY_CHECKS=1;');
	}

}