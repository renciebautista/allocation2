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
		
		$this->call('RolesTableSeeder');
		
		$this->call('UserTableTableSeeder');

		$this->call('ScopeTypesTableSeeder');
		
		$this->call('ObjectivesTableSeeder');

		$this->call('ActivityGroupingTableSeeder');

		$this->call('MonthTableSeeder');
		$this->call('BudgetTypeTableSeeder');
		
		$this->call('ActivityStatusTableSeeder');
		$this->call('MaterialSourceTableSeeder');

		$this->call('ActivityTypesTableSeeder');

		$this->call('NewUserTableTableSeeder');
			//
		DB::statement('SET FOREIGN_KEY_CHECKS=1;');

	}

}
