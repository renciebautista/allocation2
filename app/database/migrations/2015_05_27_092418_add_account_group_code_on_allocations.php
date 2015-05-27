<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddAccountGroupCodeOnAllocations extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('allocations', function(Blueprint $table)
		{
			$table->string('account_group_name')->after('channel');
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
			$table->dropColumn(array('account_group_name'));
		});
	}

}
