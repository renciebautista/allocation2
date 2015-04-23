<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateActivitiesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('activities', function(Blueprint $table)
		{
			// $table->dropColumn(array('scope_type_id','cycle_id','activity_type_id'));
			
			// $table->integer('scope_type_id')->unsigned()->after('circular_name');
			// $table->foreign('scope_type_id')->references('id')->on('scope_types');
			// $table->integer('cycle_id')->unsigned()->after('eimplementation_date');
			// $table->foreign('cycle_id')->references('id')->on('cycles');
			// $table->integer('activity_type_id')->unsigned()->after('cycle_id');
			// $table->foreign('activity_type_id')->references('id')->on('activity_types');

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
			// $table->dropColumn(array('scope_type_id','cycle_id','activity_type_id'));
			// $table->integer('scope_type_id')->unsigned()->after('circular_name');
			// $table->integer('cycle_id')->unsigned()->after('eimplementation_date');
			// $table->integer('activity_type_id')->unsigned()->after('cycle_id');

		});
	}

}
