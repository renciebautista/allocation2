<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddPreVariantOnTradedealPartSkusTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('tradedeal_part_skus', function(Blueprint $table)
		{
			$table->string('pre_variant')->after('pre_desc')->nullable();
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
			$table->dropColumn(['pre_variant']);
		});
	}

}
