<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class UpdateCustomerTreeTableSeeder extends Seeder {

	public function run()
	{
		CustomerTree::updateTree();
	}

}