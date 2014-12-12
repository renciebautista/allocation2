<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAccountsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('accounts', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('area_code');
			$table->string('ship_to_code');
			$table->string('account_group_code');
			$table->string('channel_code');
			$table->string('account_name');
			$table->boolean('active')->default(0);
			$table->index('area_code');
			$table->index('ship_to_code');
			$table->index('account_group_code');
			$table->index('channel_code');
			$table->index('account_name');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('accounts');
	}

}
