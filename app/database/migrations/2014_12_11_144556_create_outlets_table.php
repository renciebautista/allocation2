<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOutletsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('outlets', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('area_code');
			$table->string('ship_to_code')->nullable();
			$table->string('account_name')->nullable();
			$table->string('customer_code')->nullable();
			$table->string('outlet_code');
			$table->index('area_code');
			$table->index('ship_to_code');
			$table->index('account_name');
			$table->index('customer_code');
			$table->index('outlet_code');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('outlets');
	}

}
