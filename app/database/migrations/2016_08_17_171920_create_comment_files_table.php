<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCommentFilesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('comment_files', function(Blueprint $table)
		{
			$table->engine = "MyISAM";
			$table->increments('id');
			$table->integer('comment_id')->unsigned();
			$table->foreign('comment_id')->references('id')->on('joborder_comments');
			$table->string('random_name');
			$table->string('file_name');
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
		Schema::drop('comment_files');
	}

}
