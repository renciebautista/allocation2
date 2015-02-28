<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateActivityTypeBudgetRequiredTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('activity_type_budget_requireds', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('activity_type_id')->unsigned();
			$table->foreign('activity_type_id')->references('id')->on('activity_types');
			$table->integer('budget_type_id')->unsigned();
			$table->foreign('budget_type_id')->references('id')->on('budget_types');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('activity_type_budget_requireds', function(Blueprint $table)
		{
			$table->dropForeign('activity_type_budget_requireds_activity_type_id_foreign');
			$table->dropForeign('activity_type_budget_requireds_budget_type_id_foreign');
		});
		Schema::drop('activity_type_budget_requireds');
	}

}
