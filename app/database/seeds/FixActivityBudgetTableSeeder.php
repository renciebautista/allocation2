<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class FixActivityBudgetTableSeeder extends Seeder {

	public function run()
	{
		$budgets = ActivityBudget::all();
		foreach ($budgets as $budget) {
			$budgettype = BudgetType::find($budget->budget_type_id);
			$budget->budget_desc = $budgettype->budget_type;
			$budget->update();
		}
	}

}