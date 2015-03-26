<?php

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();
		DB::statement('SET FOREIGN_KEY_CHECKS=0;');
		// $this->call('UserTableSeeder');
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

		$this->call('ScopeTypesTableSeeder');
		$this->call('CyclesTableSeeder');
		$this->call('ActivityTypesTableSeeder');
		$this->call('SkusTableSeeder');

		$this->call('ObjectivesTableSeeder');

		// new
		$this->call('MonthTableSeeder');
		$this->call('BudgetTypeTableSeeder');
		$this->call('PricelistTableSeeder');
		$this->call('ActivityStatusTableSeeder');
		
		DB::statement('SET FOREIGN_KEY_CHECKS=1;');

	}

}
