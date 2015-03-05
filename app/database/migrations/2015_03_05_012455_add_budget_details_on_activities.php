<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddBudgetDetailsOnActivities extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('activities', function(Blueprint $table)
		{
			$table->date('billing_date')->nullable()->after('status_id');
            $table->string('billing_remarks')->nullable()->after('billing_date');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('activities', function(Blueprint $table)
		{
			$table->dropColumn(array('billing_date', 'billing_remarks'));
		});
	}

}
