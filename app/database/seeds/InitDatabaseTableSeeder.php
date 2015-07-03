<?php

use Faker\Factory as Faker;

class InitDatabaseTableSeeder extends Seeder {

	public function run()
	{
		Eloquent::unguard();
		DB::statement('SET FOREIGN_KEY_CHECKS=0;');
		
		$this->call('PricelistTableSeeder');
		$this->call('SkusTableSeeder');

		$this->call('GroupsTableSeeder');
		$this->call('AreasTableSeeder');
		$this->call('CustomersTableSeeder');
		$this->call('SplitOldCustomerTableSeeder');
		$this->call('ShipTosTableSeeder');
		$this->call('AccountGroupsTableSeeder');
		$this->call('ChannelsTableSeeder');
		$this->call('SubChannelsTableSeeder');
		$this->call('AccountsTableSeeder');
		$this->call('OutletsTableSeeder');
		$this->call('MotherChildSkusTableSeeder');

		$this->call('MtPrimarySalesTableSeeder');
		$this->call('DtSecondarySalesTableSeeder');
		$this->call('ShipToSalesTableSeeder');
		$this->call('OutletSalesTableSeeder');

		
			//
		DB::statement('SET FOREIGN_KEY_CHECKS=1;');
	}

}