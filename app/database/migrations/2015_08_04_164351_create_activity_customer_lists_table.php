<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateActivityCustomerListsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('activity_customer_lists', function(Blueprint $table)
		{
			$table->bigIncrements('id');
			$table->string('parent_id')->nullable();
			$table->integer('activity_id');
			$table->string('title');
			$table->boolean('isfolder');
			$table->string('key');
			$table->boolean('unselectable');
			$table->boolean('selected');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('activity_customer_lists');
	}

}
