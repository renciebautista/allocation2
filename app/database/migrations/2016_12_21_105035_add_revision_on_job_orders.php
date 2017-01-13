<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddRevisionOnJobOrders extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('joborders', function(Blueprint $table)
		{
			$table->integer('weight')->after('end_date');
			$table->integer('revision')->after('weight')->default(1);
			$table->integer('cost')->after('revision');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('joborders', function(Blueprint $table)
		{
			$table->dropColumn(['weight', 'revision', 'cost']);
		});
	}

}
