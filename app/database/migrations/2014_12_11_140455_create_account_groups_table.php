<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAccountGroupsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('account_groups', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('account_group_code');
			$table->string('account_group_name');
			
			$table->index('account_group_code');
			$table->index('account_group_nam');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('account_groups');
	}

}
