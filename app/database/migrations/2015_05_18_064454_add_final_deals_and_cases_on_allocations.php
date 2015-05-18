<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddFinalDealsAndCasesOnAllocations extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('allocations', function(Blueprint $table)
		{
			$table->integer('in_deals')->after('final_alloc');
			$table->integer('in_cases')->after('in_deals');
			$table->decimal('tts_budget',12,2)->after('in_cases');
			$table->decimal('pe_budget',12,2)->after('tts_budget');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('allocations', function(Blueprint $table)
		{
			$table->dropColumn(array('in_deals', 'in_cases', 'tts_budget', 'pe_budget'));
		});
	}

}
