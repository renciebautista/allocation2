<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddStatusOnTradedealTypesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('tradedeal_types', function(Blueprint $table)
		{
			$table->boolean('active')->after('tradedeal_type');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('tradedeal_types', function(Blueprint $table)
		{
			$table->dropColumn(['active']);
		});
	}

}
