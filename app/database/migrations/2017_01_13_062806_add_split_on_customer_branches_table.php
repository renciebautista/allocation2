<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddSplitOnCustomerBranchesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('customer_branches', function(Blueprint $table)
		{
			$table->integer('split')->nullable()->after('branch_name');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('customer_branches', function(Blueprint $table)
		{
			$table->dropColumn(['split']);
		});
	}

}
