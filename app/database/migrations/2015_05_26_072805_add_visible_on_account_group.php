<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddVisibleOnAccountGroup extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('account_groups', function(Blueprint $table)
		{
			$table->boolean('show_in_summary')->default(false);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('account_groups', function(Blueprint $table)
		{
			$table->dropColumn(array('show_in_summary'));
		});
	}

}
