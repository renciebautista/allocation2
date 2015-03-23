<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateActivityFisTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('activity_fis', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('created_by')->unsigned();
            $table->foreign('created_by')->references('id')->on('users');
            $table->integer('activity_id')->unsigned();
            $table->foreign('activity_id')->references('id')->on('activities');
            $table->string('hash_name');
            $table->string('file_name');
            $table->string('file_desc');
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
		Schema::table('activity_fis', function(Blueprint $table)
		{
			$table->dropForeign('activity_fis_created_by_foreign');
			$table->dropForeign('activity_fis_activity_id_foreign');
		});
		
		Schema::drop('activity_fis');
	}

}
