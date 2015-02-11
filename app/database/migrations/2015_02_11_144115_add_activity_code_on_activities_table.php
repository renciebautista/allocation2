<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddActivityCodeOnActivitiesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('activities', function(Blueprint $table)
		{
			$table->text('activity_code')->after('id');
			$table->integer('duration')->after('scope_type_id');
			$table->date('edownload_date')->after('duration');
			$table->date('eimplementation_date')->after('edownload_date');
			
			$table->dropColumn(array('budget_tts', 'budget_pe'));
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
			$table->dropColumn(array('activity_code', 'duration', 'edownload_date', 'eimplementation_date'));
			$table->decimal('budget_tts',15,2)->after('division_code');
			$table->decimal('budget_pe',15,2)->after('budget_tts');
		});
	}

}
