<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class FixActivityNoBudgetTableSeeder extends Seeder {

	public function run()
	{
		$nobudgets = \ActivityNoBudget::all();
		// $nobudgets = DB::table('activity_nobudgets')->get();
		foreach ($nobudgets as $nobudget) {
			$budgettype = BudgetType::find($nobudget->budget_type_id);
			$nobudget->budget_desc = $budgettype->budget_type;
			$nobudget->update();
		}
	}

}