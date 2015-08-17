<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddShowOnAllocationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('allocations', function(Blueprint $table)
		{	
			$table->boolean('show')->after('pe_budget');
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
			$table->dropColumn(array('show'));
		});
	}

}
