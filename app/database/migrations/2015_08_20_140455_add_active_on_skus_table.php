<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddActiveOnSkusTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('skus', function(Blueprint $table)
		{
			$table->boolean('active')->default(true);
			$table->boolean('launch')->default(false);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('skus', function(Blueprint $table)
		{
			$table->dropColumn(array('active','launch'));
		});
	}

}
