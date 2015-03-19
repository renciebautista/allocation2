<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddFinalAllocationOnSchemes extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('schemes', function(Blueprint $table)
		{
			$table->integer('total_deals')->nullable()->after('deals');
			$table->integer('total_cases')->nullable()->after('total_deals');

			$table->integer('final_alloc')->nullable()->after('user_id');
			$table->integer('final_total_deals')->nullable()->after('final_alloc');
			$table->integer('final_total_cases')->nullable()->after('final_total_deals');
			$table->decimal('final_tts_r',12,2)->nullable()->after('final_total_cases');
			$table->decimal('final_pe_r',12,2)->nullable()->after('final_tts_r');
			$table->decimal('final_total_cost',12,2)->nullable()->after('final_pe_r');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('schemes', function(Blueprint $table)
		{
			$table->dropColumn(array('total_deals', 'total_cases','final_alloc','final_total_deals','final_total_cases',
				'final_tts_r','final_pe_r','final_total_cost'));
		});
	}

}
