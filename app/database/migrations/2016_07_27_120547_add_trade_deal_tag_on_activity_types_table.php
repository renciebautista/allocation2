<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddTradeDealTagOnActivityTypesTable extends Migration {

	
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('activity_types', function(Blueprint $table)
		{
			if (!Schema::hasColumn('activity_types', 'with_tradedeal'))
			{

				
			    $table->boolean('with_tradedeal')->default(false)->after('with_sob');
			}
			
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('activity_types', function(Blueprint $table)
		{
			if (Schema::hasColumn('activity_types', 'with_tradedeal'))
			{
			    $table->dropColumn(['with_tradedeal']);
			}
			
		});
	}


}
