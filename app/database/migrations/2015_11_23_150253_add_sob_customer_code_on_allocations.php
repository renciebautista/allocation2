<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddSobCustomerCodeOnAllocations extends Migration {
/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('allocations', function(Blueprint $table)
		{
			$table->string('sob_customer_code')->after('sold_to_code')->nullable();
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
			$table->dropColumn(array('sob_customer_code'));
		});
	}

}
