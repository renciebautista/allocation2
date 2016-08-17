<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddCustomizedApproverOnSettingsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('settings', function(Blueprint $table)
		{
			$table->string('customized_preapprover')->after('pasword_expiry');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('settings', function(Blueprint $table)
		{
			$table->dropColumn(['customized_preapprover']);	
		});
	}

}
