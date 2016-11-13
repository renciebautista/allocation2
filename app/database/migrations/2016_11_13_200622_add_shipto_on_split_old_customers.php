<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddShiptoOnSplitOldCustomers extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('split_old_customers', function(Blueprint $table)
		{
			$table->string('from_customer');
			$table->string('from_plant');
			$table->string('to_customer');
			$table->string('to_plant');
			$table->dropColumn(['inactive_customer_code', 'active_customer_code']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('split_old_customers', function(Blueprint $table)
		{
			$table->dropColumn(['from_customer', 'from_plant', 'to_customer', 'to_plant']);
			$table->string('inactive_customer_code');
			$table->string('active_customer_code');
		});
	}

}
