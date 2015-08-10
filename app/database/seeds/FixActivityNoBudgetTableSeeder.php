<?php


class FixActivityNoBudgetTableSeeder extends Seeder {

	public function run()
	{
		$nobudgets = ActivityNobudget::all();
		// $nobudgets = DB::table('activity_nobudgets')->get();
		foreach ($nobudgets as $nobudget) {
			$budgettype = BudgetType::find($nobudget->budget_type_id);
			$nobudget->budget_desc = $budgettype->budget_type;
			$nobudget->update();
		}
	}

}