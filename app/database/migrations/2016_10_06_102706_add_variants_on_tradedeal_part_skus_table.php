<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddVariantsOnTradedealPartSkusTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('tradedeal_part_skus', function(Blueprint $table)
		{
			$table->string('variant')->after('host_desc');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('tradedeal_part_skus', function(Blueprint $table)
		{
			$table->dropColumn(['variant']);
		});
	}

}
