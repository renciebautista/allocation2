<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddRefPcsCaseOnTradedealPartSkusTables extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('tradedeal_part_skus', function(Blueprint $table)
		{
			$table->integer('ref_pcs_case')->nullable()->after('ref_desc');
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
			$table->dropColumn(['ref_pcs_case']);
		});
	}

}
