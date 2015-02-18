<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateActivtyBudgetsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('activity_budgets', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('activity_id')->unsigned();
            $table->foreign('activity_id')->references('id')->on('activities');
            $table->integer('budget_type_id')->unsigned();
            $table->foreign('budget_type_id')->references('id')->on('budget_types');
            $table->text('io_number');
            $table->decimal('amount',12,2);
            $table->date('start_date');
            $table->date('end_date');
            $table->string('remarks')->nullable();
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('activity_budgets', function(Blueprint $table)
		{
			$table->dropForeign('activity_budgets_activity_id_foreign');
			$table->dropForeign('activity_budgets_budget_type_id_foreign');
		});
		Schema::drop('activity_budgets');
	}

}
