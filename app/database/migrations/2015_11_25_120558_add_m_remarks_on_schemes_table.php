<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddMRemarksOnSchemesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('schemes', function(Blueprint $table)
		{
			$table->string('m_remarks')->after('brand_shortcut')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('schemes', function(Blueprint $table)
		{
			$table->dropColumn(array('m_remarks'));
		});
	}

}
