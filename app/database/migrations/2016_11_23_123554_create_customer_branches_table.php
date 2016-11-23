<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCustomerBranchesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('customer_branches', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('customer_code');
			$table->string('plant_code')->nullable();
			$table->string('distributor_code');
			$table->string('branch_name');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('customer_branches');
	}

}
