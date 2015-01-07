<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSplitOldCustomersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('split_old_customers', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('inactive_customer_code');
			$table->string('active_customer_code');
			$table->integer('split')->nullable();

			$table->index('inactive_customer_code');
			$table->index('active_customer_code');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('split_old_customers');
	}

}
