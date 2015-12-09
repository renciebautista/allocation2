<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddWeekPercentagesOnTables extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('week_percentages', function(Blueprint $table)
		{
			$table->bigIncrements('id');
			$table->integer('scheme_id')->unsigned();
            
            $table->integer('weekno');

            $table->decimal('share',12,2);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('week_percentages', function(Blueprint $table)
		{
			
		});
	}

}
