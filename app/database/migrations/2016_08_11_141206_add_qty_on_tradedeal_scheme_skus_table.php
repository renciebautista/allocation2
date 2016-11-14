<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddQtyOnTradedealSchemeSkusTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('tradedeal_scheme_skus', function(Blueprint $table)
		{
			$table->integer('qty')->after('id');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('tradedeal_scheme_skus', function(Blueprint $table)
		{
			$table->dropColumn(['qty']);
		});
	}

}
