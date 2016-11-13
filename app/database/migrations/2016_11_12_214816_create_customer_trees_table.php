<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCustomerTreesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('customer_trees', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('channel_code');
			$table->string('group_code');
			$table->string('area_code');
			$table->string('customer_code');
			$table->string('plant_code');
			$table->string('account_id');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('customer_trees');
	}

}
