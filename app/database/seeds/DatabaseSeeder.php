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
	}

}
