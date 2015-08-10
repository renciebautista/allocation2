<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class FixActivityTableSeeder extends Seeder {

	public function run()
	{
		Eloquent::unguard();
		DB::statement('SET FOREIGN_KEY_CHECKS=0;');
		
		$this->call('FixActivityDetailsTableSeeder');
		$this->call('FixActivityPlannerDescTableSeeder');
		$this->call('FixActivityApproverDescTableSeeder');

		$this->call('FixActivityDivisionTableSeeder');
		$this->call('FixActivityCategoryTableSeeder');
		$this->call('FixActivityBrandTableSeeder');
		$this->call('FixActivitySkuTableSeeder');
		$this->call('FixActivityObjectiveTableSeeder');
		$this->call('FixActivityMaterialTableSeeder');
		
		$this->call('FixCustomerListTableSeeder');
		$this->call('FixActivityForceAllocationTableSeeder');
		$this->call('FixActivityChannelListTableSeeder');

		$this->call('FixActivityBudgetTableSeeder');
		$this->call('FixActivityNoBudgetTableSeeder');

		$this->call('FixSchemeReferenceSkuTableSeeder');
		$this->call('FixSchemeHostSkuTableSeeder');
		$this->call('FixSchemePremiumSkuTableSeeder');

		$this->call('AddCodeOnAllocationTableSeeder');
		$this->call('SchemesFieldsTableSeeder');
		$this->call('AllocationReportTemplateFilterTableSeeder');
		
		

		DB::statement('SET FOREIGN_KEY_CHECKS=1;');
	}

}