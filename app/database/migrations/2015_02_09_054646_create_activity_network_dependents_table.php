<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateActivityNetworkDependentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('activity_network_dependents', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('child_id')->unsigned();
            $table->foreign('child_id')->references('id')->on('activity_type_networks');
            $table->integer('parent_id')->unsigned();
            $table->foreign('parent_id')->references('id')->on('activity_type_networks');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('activity_network_dependents');
	}

}
