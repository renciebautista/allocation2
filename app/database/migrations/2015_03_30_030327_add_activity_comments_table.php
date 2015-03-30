<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddActivityCommentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('activity_comments', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('created_by')->unsigned();
            $table->foreign('created_by')->references('id')->on('users');
            $table->integer('activity_id')->unsigned();
            $table->foreign('activity_id')->references('id')->on('activities');
            $table->text('comment');
            $table->integer('comment_status_id');
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
		
		Schema::drop('activity_comments');
	}

}
