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

		// $this->call('UserTableSeeder');
		$this->call('GroupsTableSeeder');
		$this->call('AreasTableSeeder');
		$this->call('CustomersTableSeeder');
		$this->call('ShipTosTableSeeder');
		$this->call('AccountGroupsTableSeeder');
		$this->call('ChannelsTableSeeder');
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
	}

}
