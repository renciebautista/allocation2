<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSchemesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('schemes', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('activity_id')->unsigned();
			$table->foreign('activity_id')->references('id')->on('activities');
			$table->string('name');
			$table->integer('quantity');
			$table->integer('user_id')->unsigned();
			$table->foreign('user_id')->references('id')->on('users');
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
		Schema::table('schemes', function(Blueprint $table)
		{
			$table->dropForeign('schemes_activity_id_foreign');
			$table->dropForeign('schemes_user_id_foreign');
		});
		Schema::drop('schemes');
	}

}
