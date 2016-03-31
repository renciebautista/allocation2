<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddDivisionOnSchemesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('schemes', function(Blueprint $table)
		{
			$table->string('sdivision_code')->nullable()->after('weeks');
			$table->string('sdivision')->nullable()->after('sdivision_code');
			$table->string('scategory_code')->nullable()->after('sdivision');
			$table->string('scategory')->nullable()->after('scategory_code');
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
			$table->dropColumn(['sdivision_code', 'sdivision', 'scategory_code', 'scategory']);
		});
	}

}
