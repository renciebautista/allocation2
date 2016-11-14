<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddCoverageOnTradedealChannelsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('tradedeal_channels', function(Blueprint $table)
		{
			$table->decimal('coverage', 12, 2)->after('free');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('tradedeal_channels', function(Blueprint $table)
		{
			$table->dropColumn(['coverage']);
		});
	}

}
